<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Appointment;
use Core\Database;

class AdminController extends Controller {
    public function dashboard() {
        // Verificar si es administrador
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        // Citas (Calendario básico)
        $appointments = Appointment::getAll();
        
        // Usuarios y tratamientos para el formulario manual
        $users = \App\Models\User::getAll();
        $treatments = \App\Models\Treatment::getAll();
        
        // Agrupar citas por fecha en JSON para el calendario JS
        $appointmentsJson = [];
        foreach ($appointments as $apt) {
            $date = $apt['appointment_date'];
            if (!isset($appointmentsJson[$date])) {
                $appointmentsJson[$date] = [];
            }
            $appointmentsJson[$date][] = $apt;
        }
        
        // Mensajes de contacto
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Compras de la tienda (Cesta)
        $stmtPurchases = $db->query("
            SELECT p.*, u.name as user_name, u.email as user_email,
                   (SELECT GROUP_CONCAT(t.name, ', ') 
                    FROM purchase_items pi 
                    JOIN treatments t ON pi.treatment_id = t.id 
                    WHERE pi.purchase_id = p.id) as products,
                   pay.card_last_four, pay.card_name
            FROM purchases p 
            JOIN users u ON p.user_id = u.id 
            LEFT JOIN payments pay ON p.id = pay.purchase_id
            ORDER BY p.purchase_date DESC
        ");
        $purchases = $stmtPurchases->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('admin/dashboard', [
            'title' => 'Panel de Administración - Inspire Beauty',
            'appointments' => $appointments,
            'messages' => $messages,
            'purchases' => $purchases
        ]);
    }

    public function clients() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        $db = Database::getConnection();
        // Obtener clientes web y mostrador, con su historial de compras
        $stmt = $db->query("
            SELECT u.*, 
                   (SELECT SUM(total_amount) FROM purchases WHERE user_id = u.id) as total_spent,
                   (SELECT COUNT(id) FROM purchases WHERE user_id = u.id) as total_orders
            FROM users u 
            WHERE role = 'client'
            ORDER BY u.created_at DESC
        ");
        $clients = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Para cada cliente, obtener las descripciones de los tratamientos que han comprado
        foreach ($clients as &$client) {
            $stmtItems = $db->prepare("
                SELECT t.name 
                FROM purchase_items pi 
                JOIN purchases p ON pi.purchase_id = p.id 
                JOIN treatments t ON pi.treatment_id = t.id 
                WHERE p.user_id = ?
                GROUP BY t.name
            ");
            $stmtItems->execute([$client['id']]);
            $client['products'] = $stmtItems->fetchAll(\PDO::FETCH_COLUMN);
        }

        $this->render('admin/clients', [
            'title' => 'Gestión de Clientes - Inspire Beauty',
            'clients' => $clients
        ]);
    }

    public function calendar() {
        // Verificar si es administrador
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        $appointments = Appointment::getAll();
        $users = \App\Models\User::getAll();
        $treatments = \App\Models\Treatment::getAll();
        
        // Agrupar citas por fecha en JSON para el calendario JS
        $appointmentsJson = [];
        foreach ($appointments as $apt) {
            $date = $apt['appointment_date'];
            if (!isset($appointmentsJson[$date])) {
                $appointmentsJson[$date] = [];
            }
            $appointmentsJson[$date][] = $apt;
        }

        $this->render('admin/calendar', [
            'title' => 'Gestión de Calendario - Inspire Beauty',
            'appointments' => $appointments,
            'appointmentsJson' => json_encode($appointmentsJson),
            'users' => $users,
            'treatments' => $treatments
        ]);
    }

    public function addAppointment() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $treatmentId = $_POST['treatment_id'] ?? null;
            $date = $_POST['date'] ?? null;
            $time = $_POST['time'] ?? null;

            if ($userId === 'new') {
                $newName = $_POST['new_user_name'] ?? '';
                $newEmail = $_POST['new_user_email'] ?? '';
                $newPhone = $_POST['new_user_phone'] ?? '';
                
                if (!$newName || !$newEmail) {
                    $_SESSION['error'] = 'Para un nuevo cliente, el nombre y el email son obligatorios.';
                    header('Location: /admin');
                    exit;
                }
                
                // Asignar contraseña especial para identificar que es un cliente de mostrador
                $randomPassword = 'CUENTA_MOSTRADOR';
                $newUserId = \App\Models\User::create($newName, $newEmail, $randomPassword, $newPhone);
                
                if (!$newUserId) {
                    $_SESSION['error'] = 'No se pudo crear el nuevo cliente (el email ya podría estar registrado).';
                    header('Location: /admin');
                    exit;
                }
                
                $userId = $newUserId; // Usar el nuevo ID
            }

            if ($userId && $treatmentId && $date && $time) {
                // Al agendar manualmente, la cita ya está confirmada por defecto
                $result = Appointment::create($userId, $treatmentId, $date, $time, 'confirmed');
                if ($result['success']) {
                    // Enviar email de confirmación
                    $db = \Core\Database::getConnection();
                    $stmtUser = $db->prepare("SELECT name, email FROM users WHERE id = ?");
                    $stmtUser->execute([$userId]);
                    $user = $stmtUser->fetch();
                    
                    $stmtTreatment = $db->prepare("SELECT name FROM treatments WHERE id = ?");
                    $stmtTreatment->execute([$treatmentId]);
                    $treatment = $stmtTreatment->fetch();
                    
                    if ($user && $user['email'] && $treatment) {
                        $emailId = \Core\EmailService::sendBookingUpdate(
                            $user['email'],
                            $user['name'],
                            $treatment['name'],
                            $date,
                            $time,
                            'confirmed'
                        );
                        $_SESSION['success'] = "Cita programada manualmente con éxito. <a href='/ver-correo?id={$emailId}' target='_blank' style='color: #0366d6; text-decoration: underline; font-weight: bold;'>📧 Ver correo enviado</a>";
                    } else {
                        $_SESSION['success'] = 'Cita programada manualmente con éxito.';
                    }
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            } else {
                $_SESSION['error'] = 'Faltan datos obligatorios para programar la cita.';
            }
        }
        
        header('Location: /admin/reservar');
        exit;
    }

    public function updateStatus() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($id && $status && in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'])) {
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
                if ($stmt->execute([$status, $id])) {
                    // Enviar email de actualización
                    $apt = Appointment::getById($id);
                    if ($apt) {
                        $stmtUser = $db->prepare("SELECT name, email FROM users WHERE id = ?");
                        $stmtUser->execute([$apt['user_id']]);
                        $user = $stmtUser->fetch();
                        
                        $stmtTreat = $db->prepare("SELECT name FROM treatments WHERE id = ?");
                        $stmtTreat->execute([$apt['treatment_id']]);
                        $treatName = $stmtTreat->fetchColumn();
                        
                        if ($user && !empty($user['email']) && $treatName) {
                            $emailId = \Core\EmailService::sendBookingUpdate(
                                $user['email'],
                                $user['name'],
                                $treatName,
                                $apt['appointment_date'],
                                date('H:i', strtotime($apt['start_time'])),
                                $status
                            );
                            $link = "<br><a href='/ver-correo?id=$emailId' target='_blank' style='display:inline-block; margin-top:10px; background:#0366d6; color:white; padding:5px 10px; border-radius:3px; text-decoration:none;'>📧 Ver correo enviado al cliente</a>";
                            $_SESSION['success'] = 'Estado de la cita actualizado correctamente.' . $link;
                        } else {
                            $_SESSION['success'] = 'Estado de la cita actualizado correctamente.';
                        }
                    } else {
                        $_SESSION['success'] = 'Estado de la cita actualizado correctamente.';
                    }
                } else {
                    $_SESSION['error'] = 'Error al actualizar el estado de la cita.';
                }
            } else {
                $_SESSION['error'] = 'Datos inválidos para actualizar el estado.';
            }
        }
        
        header('Location: /admin');
        exit;
    }

    public function reschedule() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $newDate = $_POST['new_date'] ?? null;
            $newTime = $_POST['new_time'] ?? null;

            if ($id && $newDate && $newTime) {
                // Obtener cita original
                $apt = Appointment::getById($id);
                if ($apt) {
                    $db = Database::getConnection();
                    
                    // Calcular nueva hora de fin
                    $totalMinutes = $apt['duration_minutes'] + $apt['cleaning_time_minutes'];
                    $startTimestamp = strtotime($newDate . ' ' . $newTime);
                    $endTimestamp = $startTimestamp + ($totalMinutes * 60);
                    $newEndTime = date('H:i', $endTimestamp);

                    // Comprobar solapamientos
                    $stmtOverlap = $db->prepare("
                        SELECT COUNT(*) FROM appointments 
                        WHERE appointment_date = ? 
                        AND id != ?
                        AND status != 'cancelled'
                        AND start_time < ? 
                        AND end_time > ?
                    ");
                    $stmtOverlap->execute([$newDate, $id, $newEndTime, $newTime]);
                    $overlaps = $stmtOverlap->fetchColumn();

                    if ($overlaps > 0) {
                        $_SESSION['error'] = 'El nuevo horario solapa con otra cita.';
                    } else {
                        // Guardar originales si no estaban guardadas ya de un cambio previo
                        $origDate = $apt['original_appointment_date'] ?: $apt['appointment_date'];
                        $origTime = $apt['original_start_time'] ?: $apt['start_time'];

                        $stmt = $db->prepare("
                            UPDATE appointments 
                            SET appointment_date = ?, 
                                start_time = ?, 
                                end_time = ?, 
                                original_appointment_date = ?, 
                                original_start_time = ?,
                                status = 'pending'
                            WHERE id = ?
                        ");
                        if ($stmt->execute([$newDate, $newTime, $newEndTime, $origDate, $origTime, $id])) {
                            // Enviar email
                            $stmtUser = $db->prepare("SELECT name, email FROM users WHERE id = ?");
                            $stmtUser->execute([$apt['user_id']]);
                            $user = $stmtUser->fetch();
                            
                            $stmtTreat = $db->prepare("SELECT name FROM treatments WHERE id = ?");
                            $stmtTreat->execute([$apt['treatment_id']]);
                            $treatName = $stmtTreat->fetchColumn();
                            
                            if ($user && !empty($user['email']) && $treatName) {
                                $emailId = \Core\EmailService::sendBookingUpdate(
                                    $user['email'],
                                    $user['name'],
                                    $treatName,
                                    $newDate,
                                    $newTime,
                                    'pending'
                                );
                                $link = "<br><a href='/ver-correo?id=$emailId' target='_blank' style='display:inline-block; margin-top:10px; background:#0366d6; color:white; padding:5px 10px; border-radius:3px; text-decoration:none;'>📧 Ver correo enviado al cliente</a>";
                                $_SESSION['success'] = 'Cita pospuesta. El cliente debe confirmar el cambio.' . $link;
                            } else {
                                $_SESSION['success'] = 'Cita pospuesta. El cliente debe confirmar el cambio.';
                            }
                        } else {
                            $_SESSION['error'] = 'Error al posponer la cita.';
                        }
                    }
                } else {
                    $_SESSION['error'] = 'Cita no encontrada.';
                }
            } else {
                $_SESSION['error'] = 'Faltan datos para posponer la cita.';
            }
        }
        
        // Redirigir a la página desde la que se envió (dashboard o calendario)
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/reservar';
        header('Location: ' . $referer);
        exit;
    }

    public function treatments() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        $treatments = \App\Models\Treatment::getAll(true);

        $this->render('admin/treatments', [
            'title' => 'Gestión de Tratamientos - Inspire Beauty',
            'treatments' => $treatments
        ]);
    }

    public function toggleTreatment() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($id !== null && $status !== null) {
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE treatments SET is_active = ? WHERE id = ?");
                if ($stmt->execute([$status, $id])) {
                    $_SESSION['success'] = 'Estado del tratamiento actualizado correctamente.';
                } else {
                    $_SESSION['error'] = 'Error al actualizar el tratamiento.';
                }
            }
        }
        
        header('Location: /admin/tratamientos');
        exit;
    }

    public function updateTreatmentPrice() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $price = $_POST['price'] ?? null;

            if ($id !== null && $price !== null && is_numeric($price)) {
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE treatments SET price = ? WHERE id = ?");
                if ($stmt->execute([$price, $id])) {
                    $_SESSION['success'] = 'Precio actualizado correctamente.';
                } else {
                    $_SESSION['error'] = 'Error al actualizar el precio.';
                }
            } else {
                $_SESSION['error'] = 'Precio no válido.';
            }
        }
        
        header('Location: /admin/tratamientos');
        exit;
    }

    public function updateTreatmentPromo() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $promoPrice = $_POST['promo_price'] ?? null;
            $isPromo = isset($_POST['is_promo']) ? 1 : 0;

            if ($id !== null) {
                if ($promoPrice === '') $promoPrice = null;
                
                $db = \Core\Database::getConnection();
                
                // Obtener el precio base actual
                $stmtPrice = $db->prepare("SELECT price FROM treatments WHERE id = ?");
                $stmtPrice->execute([$id]);
                $treatment = $stmtPrice->fetch(\PDO::FETCH_ASSOC);

                if ($treatment) {
                    if ($promoPrice !== null && $promoPrice >= $treatment['price']) {
                        $_SESSION['error'] = 'Error: El precio de promoción (' . $promoPrice . '€) debe ser menor al precio base (' . $treatment['price'] . '€).';
                    } else {
                        $stmt = $db->prepare("UPDATE treatments SET promo_price = ?, is_promo = ? WHERE id = ?");
                        if ($stmt->execute([$promoPrice, $isPromo, $id])) {
                            $_SESSION['success'] = 'Promoción actualizada correctamente.';
                        } else {
                            $_SESSION['error'] = 'Error al actualizar la promoción.';
                        }
                    }
                }
            }
        }
        
        header('Location: /admin/tratamientos');
        exit;
    }
}

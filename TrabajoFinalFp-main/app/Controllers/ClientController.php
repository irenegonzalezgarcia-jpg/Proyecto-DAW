<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Appointment;
use Core\Database;

class ClientController extends Controller {
    public function dashboard() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Obtener citas del usuario
        $appointments = Appointment::getByUser($userId);
        
        // Obtener historial de compras del usuario
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, 
                   (SELECT GROUP_CONCAT(t.name, ', ') 
                    FROM purchase_items pi 
                    JOIN treatments t ON pi.treatment_id = t.id 
                    WHERE pi.purchase_id = p.id) as products
            FROM purchases p 
            WHERE p.user_id = ? 
            ORDER BY p.purchase_date DESC
        ");
        $stmt->execute([$userId]);
        $purchases = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('client/dashboard', [
            'title' => 'Mi Panel - Inspire Beauty',
            'appointments' => $appointments,
            'purchases' => $purchases
        ]);
    }

    public function confirmReschedule() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $db = Database::getConnection();
                // Verificamos que la cita pertenezca al usuario logueado
                $stmt = $db->prepare("UPDATE appointments SET status = 'confirmed' WHERE id = ? AND user_id = ? AND status = 'pending'");
                if ($stmt->execute([$id, $_SESSION['user_id']])) {
                    if ($stmt->rowCount() > 0) {
                        $_SESSION['success'] = 'Has confirmado correctamente la modificación de tu cita.';
                    } else {
                        $_SESSION['error'] = 'No se ha podido confirmar la cita. Es posible que ya esté confirmada o no tengas permiso.';
                    }
                } else {
                    $_SESSION['error'] = 'Error al actualizar el estado de la cita.';
                }
            }
        }
        
        header('Location: /panel');
        exit;
    }

    public function cancelAppointment() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $db = Database::getConnection();
                
                $stmt = $db->prepare("SELECT appointment_date, start_time, status FROM appointments WHERE id = ? AND user_id = ?");
                $stmt->execute([$id, $_SESSION['user_id']]);
                $apt = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($apt && in_array($apt['status'], ['pending', 'confirmed'])) {
                    $appointmentDateTime = new \DateTime($apt['appointment_date'] . ' ' . $apt['start_time']);
                    $now = new \DateTime();
                    $diffHours = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;

                    if ($diffHours >= 24) {
                        $updateStmt = $db->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
                        if ($updateStmt->execute([$id])) {
                            $_SESSION['success'] = 'Has cancelado tu cita correctamente.';
                        } else {
                            $_SESSION['error'] = 'Error al cancelar la cita.';
                        }
                    } else {
                        $_SESSION['error'] = 'No puedes cancelar esta cita porque faltan menos de 24 horas.';
                    }
                } else {
                    $_SESSION['error'] = 'Cita no encontrada o ya no se puede cancelar.';
                }
            }
        }
        
        header('Location: /panel');
        exit;
    }
}

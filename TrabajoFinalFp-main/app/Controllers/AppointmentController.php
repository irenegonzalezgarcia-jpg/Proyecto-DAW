<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Treatment;

class AppointmentController extends Controller {
    // Mostrar el formulario de reservas
    public function book() {
        // Obtenemos todos los tratamientos para mostrarlos en el select
        $treatments = Treatment::getAll();
        
        $oldInput = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);

        $this->render('appointments/book', [
            'title' => 'Reservar Cita - Inspire Beauty',
            'extraCss' => ['/css/estilos2.css', '/css/reserva.css'],
            'treatments' => $treatments,
            'oldInput' => $oldInput
        ]);
    }

    // Procesar la solicitud de reserva
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para reservar una cita.';
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $treatmentId = $_POST['treatment_id'] ?? null;
        $date = $_POST['fecha'] ?? null;
        $time = $_POST['hora'] ?? null;
        
        if (!$treatmentId || !$date || !$time) {
            $_SESSION['error'] = 'Faltan datos obligatorios.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /reservar');
            exit;
        }
        
        // Validar fecha pasada
        if (strtotime($date . ' ' . $time) < time()) {
            $_SESSION['error'] = 'No puedes reservar en una fecha u hora pasada.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /reservar');
            exit;
        }

        $result = \App\Models\Appointment::create($userId, $treatmentId, $date, $time);
        
        if ($result['success']) {
            // Enviar email de confirmación
            $db = \Core\Database::getConnection();
            $stmtUser = $db->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $user = $stmtUser->fetch();
            
            $stmtTreat = $db->prepare("SELECT name, duration_minutes FROM treatments WHERE id = ?");
            $stmtTreat->execute([$treatmentId]);
            $treatment = $stmtTreat->fetch();
            
            if ($user && !empty($user['email']) && $treatment) {
                $emailId = \Core\EmailService::sendBookingConfirmation(
                    $user['email'],
                    $user['name'],
                    $treatment['name'],
                    $date,
                    $time,
                    $treatment['duration_minutes']
                );
                
                $link = "<br><a href='/ver-correo?id=$emailId' target='_blank' style='display:inline-block; margin-top:10px; background:#0366d6; color:white; padding:5px 10px; border-radius:3px; text-decoration:none;'>📧 Ver correo enviado al cliente</a>";
                $_SESSION['success'] = $result['message'] . $link;
            } else {
                $_SESSION['success'] = $result['message'];
            }
            
            header('Location: /panel'); // Redirigimos al panel del usuario
        } else {
            $_SESSION['error'] = $result['message'];
            $_SESSION['old_input'] = $_POST;
            header('Location: /reservar');
        }
        exit;
    }

    // Endpoint API para obtener horas disponibles vía AJAX
    public function getAvailableHours() {
        header('Content-Type: application/json');
        
        $date = $_GET['date'] ?? null;
        $treatmentId = $_GET['treatment_id'] ?? null;
        
        if (!$date || !$treatmentId) {
            echo json_encode([]);
            exit;
        }

        $treatment = Treatment::getById($treatmentId);
        if (!$treatment) {
            echo json_encode([]);
            exit;
        }

        $duration = $treatment['duration_minutes'];
        $cleaning = $treatment['cleaning_time_minutes'];
        $totalMinutes = $duration + $cleaning;
        
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0) {
            echo json_encode([]); // Domingo cerrado
            exit;
        }
        
        $startHour = ($dayOfWeek == 6) ? 10 : 9;
        $endHour = ($dayOfWeek == 6) ? 14 : 20;
        
        // Obtener citas existentes para ese día
        $db = \Core\Database::getConnection();
        $stmt = $db->prepare("SELECT start_time, end_time FROM appointments WHERE appointment_date = ? AND status != 'cancelled'");
        $stmt->execute([$date]);
        $appointments = $stmt->fetchAll();
        
        $availableSlots = [];
        
        for ($h = $startHour; $h < $endHour; $h++) {
            foreach (['00', '15', '30', '45'] as $m) {
                $timeStr = sprintf("%02d:%s", $h, $m);
                $startTimestamp = strtotime($date . ' ' . $timeStr);
                $endTimestamp = $startTimestamp + ($totalMinutes * 60);
                
                // Comprobar si se sale del horario de cierre
                $closeTimestamp = strtotime($date . ' ' . sprintf("%02d:00", $endHour));
                if ($endTimestamp > $closeTimestamp) {
                    continue;
                }
                
                // Comprobar solapamientos con citas existentes
                $isAvailable = true;
                foreach ($appointments as $apt) {
                    $aptStart = strtotime($date . ' ' . $apt['start_time']);
                    $aptEnd = strtotime($date . ' ' . $apt['end_time']);
                    
                    // Lógica de solapamiento: (start1 < end2) AND (end1 > start2)
                    if ($startTimestamp < $aptEnd && $endTimestamp > $aptStart) {
                        $isAvailable = false;
                        break;
                    }
                }
                
                // No mostrar horas pasadas si es hoy
                if ($date == date('Y-m-d') && $startTimestamp < time()) {
                    $isAvailable = false;
                }
                
                if ($isAvailable) {
                    $availableSlots[] = $timeStr;
                }
            }
        }
        
        echo json_encode($availableSlots);
        exit;
    }
}

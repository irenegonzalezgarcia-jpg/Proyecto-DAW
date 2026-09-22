<?php
namespace App\Models;

use Core\Database;
use PDO;

class Appointment {
    
    // Función para crear una cita comprobando solapamientos
    public static function create($userId, $treatmentId, $date, $time, $status = 'pending') {
        $db = Database::getConnection();
        
        // Obtener datos del tratamiento para calcular el tiempo total (duración + limpieza)
        $treatment = Treatment::getById($treatmentId);
        if (!$treatment) {
            return ['success' => false, 'message' => 'Tratamiento no válido.'];
        }
        
        $duration = $treatment['duration_minutes'];
        $cleaning = $treatment['cleaning_time_minutes'];
        $totalMinutes = $duration + $cleaning;
        
        // Calcular hora de fin
        $startTimeStr = $date . ' ' . $time;
        $startTimestamp = strtotime($startTimeStr);
        $endTimestamp = $startTimestamp + ($totalMinutes * 60);
        $endTime = date('H:i', $endTimestamp);
        
        // Validación de horario comercial
        $dayOfWeek = date('w', $startTimestamp); // 0 = Domingo, 6 = Sábado
        
        if ($dayOfWeek == 0) {
            return ['success' => false, 'message' => 'El centro está cerrado los domingos.'];
        }
        
        $startLimitStr = ($dayOfWeek == 6) ? '10:00' : '09:00';
        $endLimitStr = ($dayOfWeek == 6) ? '14:00' : '20:00';
        
        $openTimestamp = strtotime($date . ' ' . $startLimitStr);
        $closeTimestamp = strtotime($date . ' ' . $endLimitStr);
        
        if ($startTimestamp < $openTimestamp || $endTimestamp > $closeTimestamp) {
            return ['success' => false, 'message' => "El horario permitido para este día es de {$startLimitStr}h a {$endLimitStr}h (incluyendo la duración del tratamiento)."];
        }
        
        // Comprobar solapamientos: start < old_end AND end > old_start
        $stmtOverlap = $db->prepare("
            SELECT COUNT(*) FROM appointments 
            WHERE appointment_date = ? 
            AND status != 'cancelled'
            AND start_time < ? 
            AND end_time > ?
        ");
        $stmtOverlap->execute([$date, $endTime, $time]);
        $overlaps = $stmtOverlap->fetchColumn();
        
        if ($overlaps > 0) {
            return ['success' => false, 'message' => 'El horario seleccionado no está disponible debido a otra reserva o tiempo de limpieza.'];
        }
        
        // Crear la cita
        try {
            $stmt = $db->prepare("INSERT INTO appointments (user_id, treatment_id, appointment_date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $treatmentId, $date, $time, $endTime, $status]);
            return ['success' => true, 'message' => 'Cita reservada correctamente.'];
        } catch (\PDOException $e) {
            // Comprobar si es un fallo de restricción de clave foránea
            if (strpos($e->getMessage(), 'FOREIGN KEY constraint failed') !== false) {
                // Si la sesión del usuario está obsoleta (usuario eliminado de la BD pero sesión activa)
                return ['success' => false, 'message' => 'Tu sesión parece haber expirado o es inválida. Por favor, cierra sesión y vuelve a iniciarla.'];
            }
            return ['success' => false, 'message' => 'Error interno al guardar la cita.'];
        }
    }

    // Obtener las citas de un usuario
    public static function getByUser($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, t.name as treatment_name 
            FROM appointments a 
            JOIN treatments t ON a.treatment_id = t.id 
            WHERE a.user_id = ? 
            ORDER BY a.appointment_date DESC, a.start_time DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las citas (para admin)
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT a.*, t.name as treatment_name, u.name as user_name 
            FROM appointments a 
            JOIN treatments t ON a.treatment_id = t.id 
            JOIN users u ON a.user_id = u.id
            ORDER BY a.appointment_date DESC, a.start_time DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una cita por ID
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, t.name as treatment_name, t.duration_minutes, t.cleaning_time_minutes, u.name as user_name 
            FROM appointments a 
            JOIN treatments t ON a.treatment_id = t.id 
            JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

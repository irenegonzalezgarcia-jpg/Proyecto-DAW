<?php
namespace App\Models;

use Core\Database;
use PDO;

class Treatment {
    public static function getAll($includeInactive = false) {
        $db = Database::getConnection();
        $sql = "SELECT * FROM treatments";
        if (!$includeInactive) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY category, name";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener un tratamiento por su ID
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM treatments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

<?php
namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

class User {
    // Buscar usuario por su correo electrónico
    public static function findByEmail($email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener todos los usuarios (clientes) omitiendo al administrador
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, name, email, phone, role FROM users WHERE role != 'admin' ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Crear un nuevo usuario
    public static function create($name, $email, $password, $phone) {
        $db = Database::getConnection();
        $hash = ($password === 'CUENTA_MOSTRADOR') ? 'CUENTA_MOSTRADOR' : password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, phone) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hash, $phone])) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            // Normalmente por email duplicado (UNIQUE constraint)
            return false;
        }
    }
}

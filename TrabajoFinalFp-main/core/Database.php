<?php
namespace Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    // Obtener la instancia de la conexión PDO (Patrón Singleton)
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dbPath = BASE_PATH . '/database/inspire_beauty.sqlite';
                self::$instance = new PDO("sqlite:" . $dbPath);
                // Activar excepciones y el uso de un array asociativo por defecto
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                // Activar el soporte para claves foráneas en SQLite
                self::$instance->exec('PRAGMA foreign_keys = ON;');
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

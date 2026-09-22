<?php
define('BASE_PATH', dirname(__DIR__));

// Autocargador simplificado para seed.php
spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    if (str_starts_with($path, 'Core')) {
        $path = 'core' . substr($path, 4);
    }
    $file = BASE_PATH . DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;

try {
    $db = Database::getConnection();

    // Ejecutamos el schema
    $schema = file_get_contents(BASE_PATH . '/database/schema.sql');
    $db->exec($schema);

    // Insertamos administrador por defecto
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) SELECT ?, ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = ?)");
    $stmt->execute(['Administrador', 'admin@inspirebeauty.com', $password, 'admin', 'admin@inspirebeauty.com']);

    // Insertamos tratamientos base si la tabla está vacía
    $stmtCheck = $db->query("SELECT COUNT(*) FROM treatments");
    if ($stmtCheck->fetchColumn() == 0) {
        $treatments = [
            ['Higiene Facial Avanzada', 'Limpieza profunda de la piel para recuperar luminosidad.', 'Facial', 45.00, 60, 15],
            ['Radiofrecuencia Rejuvenecedora', 'Tratamiento antiarrugas y flacidez con aparatología avanzada.', 'Facial', 60.00, 45, 15],
            ['Masaje Relajante Corporal', 'Masaje de cuerpo entero para relajar tensiones y musculatura.', 'Corporal', 55.00, 60, 15],
            ['Tratamiento Corporal Reductor', 'Técnicas manuales y aparatología para remodelar la figura.', 'Corporal', 70.00, 90, 15]
        ];
        
        $stmtInsert = $db->prepare("INSERT INTO treatments (name, description, category, price, duration_minutes, cleaning_time_minutes) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($treatments as $t) {
            $stmtInsert->execute($t);
        }
    }
    echo "Base de datos inicializada correctamente.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

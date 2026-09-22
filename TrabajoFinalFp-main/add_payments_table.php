<?php
define('BASE_PATH', __DIR__);
require_once __DIR__ . '/core/Database.php';

$db = \Core\Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    purchase_id INTEGER NOT NULL,
    card_name TEXT NOT NULL,
    card_last_four TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id)
);
";

try {
    $db->exec($sql);
    echo "Tabla 'payments' creada con éxito.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

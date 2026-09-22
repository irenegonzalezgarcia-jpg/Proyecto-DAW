<?php
define('BASE_PATH', __DIR__);
require 'core/Database.php';
$db = \Core\Database::getConnection();
$stmt = $db->query('SELECT * FROM users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hash = $users[0]['password_hash'];
echo "Hash: " . $hash . "\n";
echo "Verify admin123: " . (password_verify('admin123', $hash) ? 'true' : 'false') . "\n";

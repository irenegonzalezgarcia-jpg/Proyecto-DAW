<?php
define('BASE_PATH', __DIR__);
require 'core/Database.php';
$db = \Core\Database::getConnection();
$stmt = $db->query('SELECT COUNT(*) FROM treatments');
echo 'Treatments count: ' . $stmt->fetchColumn() . "\n";

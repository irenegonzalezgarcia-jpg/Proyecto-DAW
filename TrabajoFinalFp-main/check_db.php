<?php
require 'core/Database.php';
define('BASE_PATH', __DIR__);
$db = \Core\Database::getConnection();

echo "USERS:\n";
$stmt = $db->query("SELECT * FROM users");
print_r($stmt->fetchAll(\PDO::FETCH_ASSOC));

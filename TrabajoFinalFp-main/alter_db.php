<?php
$db = new PDO('sqlite:database/inspire_beauty.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $db->exec("ALTER TABLE appointments ADD COLUMN original_appointment_date DATE;");
    echo "Column original_appointment_date added.\n";
} catch (Exception $e) {
    echo "Error adding original_appointment_date: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE appointments ADD COLUMN original_start_time TIME;");
    echo "Column original_start_time added.\n";
} catch (Exception $e) {
    echo "Error adding original_start_time: " . $e->getMessage() . "\n";
}

echo "Done.\n";
?>

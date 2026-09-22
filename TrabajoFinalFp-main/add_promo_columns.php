<?php
$db = new PDO('sqlite:database/inspire_beauty.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $db->exec("ALTER TABLE treatments ADD COLUMN promo_price REAL DEFAULT NULL;");
    echo "Column promo_price added.\n";
} catch (Exception $e) {
    echo "Error adding promo_price: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE treatments ADD COLUMN is_promo INTEGER NOT NULL DEFAULT 0;");
    echo "Column is_promo added.\n";
} catch (Exception $e) {
    echo "Error adding is_promo: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE treatments ADD COLUMN image_url TEXT DEFAULT NULL;");
    echo "Column image_url added.\n";
} catch (Exception $e) {
    echo "Error adding image_url: " . $e->getMessage() . "\n";
}

echo "Done.\n";
?>

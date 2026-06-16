<?php
require __DIR__ . '/../config/database.php';
try {
    $pdo->exec("ALTER TABLE reviews ADD COLUMN seller_reply TEXT NULL");
    echo "Column seller_reply added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

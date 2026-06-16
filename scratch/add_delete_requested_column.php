<?php
require __DIR__ . '/../config/database.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN delete_requested TINYINT(1) NOT NULL DEFAULT 0");
    echo "Column delete_requested added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
require_once __DIR__ . '/../src/config/database.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(40) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS dob DATE NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS alternate_phone VARCHAR(40) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS delete_requested TINYINT(1) NOT NULL DEFAULT 0");
    echo "Columns added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}

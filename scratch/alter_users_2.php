<?php
require_once __DIR__ . '/../src/config/database.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL");
} catch (PDOException $e) {}
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(40) NULL");
} catch (PDOException $e) {}
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN dob DATE NULL");
} catch (PDOException $e) {}
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN alternate_phone VARCHAR(40) NULL");
} catch (PDOException $e) {}
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN delete_requested TINYINT(1) NOT NULL DEFAULT 0");
} catch (PDOException $e) {}

echo "Columns added successfully (or already exist).";

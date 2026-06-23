<?php
require __DIR__ . '/../config/database.php';

try {
    // 1. Alter users table to add title if it doesn't exist
    $cols = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('title', $cols, true)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN title VARCHAR(120) NULL DEFAULT 'Super Admin Rubby' AFTER name");
        echo "Successfully added 'title' column to 'users' table.\n";
    } else {
        echo "'title' column already exists in 'users' table.\n";
    }

    // 2. Create system_settings table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS system_settings (
            `key` VARCHAR(100) PRIMARY KEY,
            `value` TEXT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Checked/created 'system_settings' table.\n";

    // 3. Seed default system_settings
    $defaults = [
        'site_name' => 'Rubby Books Official',
        'support_email' => 'support@rubbybooks.id',
        'timezone' => 'Asia/Jakarta (WIB)',
        'maintenance_mode' => '0',
        'user_registration' => '1'
    ];

    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM system_settings WHERE `key` = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO system_settings (`key`, `value`) VALUES (?, ?)");

    foreach ($defaults as $key => $val) {
        $stmtCheck->execute([$key]);
        if ((int)$stmtCheck->fetchColumn() === 0) {
            $stmtInsert->execute([$key, $val]);
            echo "Seeded system setting '{$key}' with value '{$val}'.\n";
        }
    }

} catch (Exception $e) {
    echo "Database update error: " . $e->getMessage() . "\n";
}

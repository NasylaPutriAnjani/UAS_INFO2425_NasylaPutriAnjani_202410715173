<?php
require __DIR__ . '/../config/database.php';
try {
    // Check if columns exist first, or add them using ALTER TABLE (with IF NOT EXISTS simulation/try-catch)
    $colsToAdd = [
        "avatar VARCHAR(255) NULL",
        "phone VARCHAR(40) NULL",
        "dob DATE NULL",
        "alternate_phone VARCHAR(40) NULL"
    ];
    
    foreach ($colsToAdd as $col) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN $col");
            echo "Added column: $col\n";
        } catch (PDOException $e) {
            echo "Column might already exist or error: " . $e->getMessage() . "\n";
        }
    }
    echo "Migration completed successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

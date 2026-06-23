<?php
require 'config/database.php';
$cols = $pdo->query('DESCRIBE users')->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $cols) . PHP_EOL;

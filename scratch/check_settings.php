<?php
require 'config/database.php';
$cols = $pdo->query('DESCRIBE system_settings')->fetchAll(PDO::FETCH_COLUMN);
echo "Columns: " . implode(', ', $cols) . PHP_EOL;
$rows = $pdo->query('SELECT * FROM system_settings LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) { echo json_encode($r) . PHP_EOL; }

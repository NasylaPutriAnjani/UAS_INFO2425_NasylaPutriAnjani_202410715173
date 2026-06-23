<?php
require 'config/database.php';
$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
foreach ($cats as $c) {
    echo $c['id'] . '|' . $c['name'] . "\n";
}

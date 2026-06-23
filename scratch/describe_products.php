<?php
require 'config/database.php';
$cols = $pdo->query('DESCRIBE products')->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    echo $c['Field'] . "\n";
}

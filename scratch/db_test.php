<?php
require 'src/config/database.php';
$stmt = $pdo->prepare('SELECT id, name, email, phone, dob, alternate_phone, avatar FROM users WHERE id IN (3, 8)');
$stmt->execute();
print_r($stmt->fetchAll());

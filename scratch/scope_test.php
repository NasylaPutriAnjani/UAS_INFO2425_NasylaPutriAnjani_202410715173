<?php
session_start();
$_SESSION['user'] = ['id' => 3, 'name' => 'Putri Lestari', 'email' => 'putrilestari@gmail.com', 'role' => 'buyer'];

require 'src/config/database.php';
require 'src/includes/functions.php';
require 'src/controllers/PageController.php';

// Simulate index.php execution:
// 1. page_data
$data = page_data($pdo, 'account_settings');

// 2. header.php sets global $user
$user = current_user(); // session user

// 3. render_view
function test_render_view($view, $data) {
    extract($data, EXTR_SKIP);
    echo "INSIDE test_render_view scope:\n";
    echo "Is \$user set? " . (isset($user) ? 'YES' : 'NO') . "\n";
    echo "User contents: ";
    print_r($user);
}

test_render_view('shared/account.php', $data);

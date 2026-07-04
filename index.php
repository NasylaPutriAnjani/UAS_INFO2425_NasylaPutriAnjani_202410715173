<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/src/config/database.php';
require __DIR__ . '/src/includes/functions.php';
require __DIR__ . '/src/controllers/actions.php';
require __DIR__ . '/src/controllers/PageController.php';

$page = $_GET['page'] ?? 'home';
$action = $_POST['action'] ?? $_GET['action'] ?? null;

handle_action($pdo, $action, $page);

$route = route_config($page);

if (!empty($route['status'])) {
    http_response_code((int) $route['status']);
}

if (!empty($route['role'])) {
    require_role($route['role']);
}

// account_settings needs login but no specific role
if ($page === 'account_settings') {
    require_login();
}

if (in_array($page, ['login', 'register'], true) && !$action) {
    redirect('home', ['auth' => $page === 'register' ? 'daftar' : 'masuk']);
}

$data = page_data($pdo, $page);

require __DIR__ . '/src/views/layout/header.php';
render_view($route['view'], $data);
require __DIR__ . '/src/views/layout/footer.php';

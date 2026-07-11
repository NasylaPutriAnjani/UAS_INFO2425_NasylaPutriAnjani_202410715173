<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/src/config/database.php';
require __DIR__ . '/src/includes/functions.php';
require __DIR__ . '/src/controllers/actions.php';
require __DIR__ . '/src/controllers/PageController.php';

$page = trim((string) ($_GET['page'] ?? 'home'));
$page = trim($page, "/ \t\n\r\0\x0B");
if ($page === '') {
    $page = 'home';
}
$page = str_replace(['/', '-'], '_', $page);

$role = current_user()['role'] ?? 'guest';
$pageAliases = [
    'dashboard'     => $role === 'admin' ? 'admin' : ($role === 'seller' ? 'seller' : 'buyer'),
    'account'       => 'account_settings',
    'settings'      => $role === 'admin' ? 'admin_settings' : 'account_settings',
    'orders'        => $role === 'seller' ? 'seller_orders' : ($role === 'admin' ? 'admin_orders' : 'buyer_orders'),
    'reviews'       => $role === 'seller' ? 'seller_reviews' : 'buyer_reviews',
    'notifications' => $role === 'seller' ? 'seller_notifications' : ($role === 'admin' ? 'admin_notifications' : 'buyer_notifications'),
    'wishlist'      => 'buyer_wishlist',
    'cart_page'     => 'buyer_cart',
    'products'      => $role === 'admin' ? 'admin_products' : 'seller_products',
    'categories'    => 'admin_categories',
    'users'         => 'admin_users',
    'analytics'     => 'admin_analytics',
    'report'        => 'seller_report',
    'reports'       => 'seller_report',
];
$page = $pageAliases[$page] ?? $page;
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

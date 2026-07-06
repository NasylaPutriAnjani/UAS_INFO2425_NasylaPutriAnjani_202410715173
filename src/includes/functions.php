<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah(int|float $value): string
{
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

/**
 * Fetch all system settings as a key-value array.
 * Falls back to sensible defaults so the site always works.
 */
function get_system_settings(PDO $pdo): array
{
    static $cache = null;
    if ($cache !== null) return $cache;

    $defaults = [
        'currency'            => 'IDR',
        'timezone'            => 'Asia/Jakarta',
        'min_order'           => '50000',
        'ppn_rate'            => '11',
        'ppn_included'        => '0',
        'low_stock_alert'     => '1',
        'low_stock_threshold' => '10',
        'show_stock_display'  => '1',
    ];

    try {
        $rows = $pdo->query('SELECT `key`, `value` FROM system_settings')->fetchAll(PDO::FETCH_KEY_PAIR);
        $cache = array_merge($defaults, $rows);
    } catch (Throwable) {
        $cache = $defaults;
    }

    // Apply timezone globally — strip any parenthetical labels e.g. "(WIB)" stored by old data
    $tz = preg_replace('/\s*\(.*\)\s*$/', '', $cache['timezone']);
    if (@date_default_timezone_set($tz)) {
        $cache['timezone'] = $tz; // normalise stored value
    }

    return $cache;
}

function asset(?string $path): string
{
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) return $path;
    return 'src/' . ltrim($path, '/');
}

function redirect(string $page, array $query = []): never
{
    $params = array_merge(['page' => $page], $query);
    header('Location: index.php?' . http_build_query($params));
    exit;
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_role(string $role): bool
{
    return (current_user()['role'] ?? null) === $role;
}

function require_login(): void
{
    if (!current_user()) {
        flash('Silakan login terlebih dahulu.', 'error');
        redirect('home', ['auth' => 'masuk']);
    }
}

function require_role(array|string $roles): void
{
    require_login();
    $roles = (array) $roles;
    if (!in_array(current_user()['role'], $roles, true)) {
        flash('Akses halaman tidak sesuai role akun.', 'error');
        redirect('home');
    }
}

function role_icon(string $role): string
{
    return match ($role) {
        'seller' => '📦',
        'admin' => '🔐',
        default => '🛒',
    };
}

function role_chip_label(array $user): string
{
    return match ($user['role']) {
        'seller' => 'Seller Dashboard',
        'admin' => 'Admin Panel',
        default => explode(' ', trim($user['name']))[0] ?: 'Akun Saya',
    };
}

function role_chip_sublabel(string $role): string
{
    return match ($role) {
        'seller' => 'Penjual · RubbyBooks',
        'admin' => 'Administrator · RubbyBooks',
        default => 'Pembeli · RubbyBooks',
    };
}

function upload_file(string $field, string $dir): ?string
{
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    $name = uniqid('rb_', true) . '.' . $ext;
    $targetDir = __DIR__ . '/../uploads/' . trim($dir, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    move_uploaded_file($_FILES[$field]['tmp_name'], $targetDir . '/' . $name);
    return 'uploads/' . trim($dir, '/') . '/' . $name;
}

function shipping_cost(string $city): int
{
    $map = ['jakarta' => 10000, 'bandung' => 15000, 'surabaya' => 20000];
    return $map[strtolower(trim($city))] ?? 25000;
}

function next_invoice(PDO $pdo): string
{
    $prefix = 'INV-' . date('Ymd') . '-';
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE invoice_number LIKE ?');
    $stmt->execute([$prefix . '%']);
    return $prefix . str_pad((string) ($stmt->fetchColumn() + 1), 3, '0', STR_PAD_LEFT);
}

function notify_user(PDO $pdo, int $userId, string $message): void
{
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (?, ?)');
    $stmt->execute([$userId, $message]);
}

function log_activity(PDO $pdo, string $activity): void
{
    $stmt = $pdo->prepare('INSERT INTO system_logs (activity) VALUES (?)');
    $stmt->execute([$activity]);
}

function cart_count(PDO $pdo): int
{
    $user = current_user();
    if (!$user) {
        return 0;
    }
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(qty),0) FROM carts WHERE buyer_id = ?');
    $stmt->execute([$user['id']]);
    return (int) $stmt->fetchColumn();
}

function cart_items(PDO $pdo): array
{
    $user = current_user();
    if (!$user) {
        return [];
    }
    $stmt = $pdo->prepare('SELECT c.id cart_id, c.qty, p.* FROM carts c JOIN products p ON p.id = c.product_id WHERE c.buyer_id = ?');
    $stmt->execute([$user['id']]);
    return $stmt->fetchAll();
}

function buyer_menu_items(): array
{
    return [
        'buyer' => ['label' => 'Beranda', 'icon' => '🏠', 'page' => 'buyer'],
        'account' => ['label' => 'Akun', 'icon' => '👤', 'page' => 'buyer_account'],
        'wishlist' => ['label' => 'Wishlist', 'icon' => '❤️', 'page' => 'buyer_wishlist'],
        'cart' => ['label' => 'Keranjang', 'icon' => '🛒', 'page' => 'buyer_cart'],
        'orders' => ['label' => 'Pesanan Saya', 'icon' => '📦', 'page' => 'buyer_orders'],
        'reviews' => ['label' => 'Review Saya', 'icon' => '⭐', 'page' => 'buyer_reviews'],
        'notifications' => ['label' => 'Notifikasi', 'icon' => '🔔', 'page' => 'buyer_notifications'],
    ];
}

function buyer_sidebar_data(PDO $pdo): array
{
    $user = current_user();
    if (!$user || $user['role'] !== 'buyer') {
        return ['cartCount' => 0, 'unreadNotifications' => 0];
    }
    $id = (int) $user['id'];
    $cartStmt = $pdo->prepare('SELECT COALESCE(SUM(qty),0) FROM carts WHERE buyer_id = ?');
    $cartStmt->execute([$id]);
    $notifStmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $notifStmt->execute([$id]);
    return [
        'cartCount' => (int) $cartStmt->fetchColumn(),
        'unreadNotifications' => (int) $notifStmt->fetchColumn(),
    ];
}

function buyer_stats(PDO $pdo): array
{
    $id = (int) current_user()['id'];
    $stmt = $pdo->prepare(
        'SELECT
            (SELECT COUNT(*) FROM orders WHERE buyer_id = ?) orders_total,
            (SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ("pending","paid","processing","shipped")) orders_active,
            (SELECT COUNT(*) FROM reviews WHERE buyer_id = ?) reviews_total,
            (SELECT COALESCE(SUM(qty),0) FROM carts WHERE buyer_id = ?) cart_items'
    );
    $stmt->execute([$id, $id, $id, $id]);
    return $stmt->fetch() ?: [];
}

function order_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default => ucfirst($status),
    };
}

function get_user_wishlist_ids(PDO $pdo): array
{
    static $wishlistIds = null;
    if ($wishlistIds !== null) {
        return $wishlistIds;
    }
    
    $user = current_user();
    if (!$user || $user['role'] !== 'buyer') {
        return $wishlistIds = [];
    }
    
    $stmt = $pdo->prepare('SELECT product_id FROM wishlists WHERE buyer_id = ?');
    $stmt->execute([$user['id']]);
    $wishlistIds = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    return $wishlistIds;
}

function is_in_wishlist(PDO $pdo, int $productId): bool
{
    $ids = get_user_wishlist_ids($pdo);
    return in_array($productId, $ids, false); // loose check since DB might return string
}

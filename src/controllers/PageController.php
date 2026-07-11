<?php
declare(strict_types=1);

function route_config(string $page): array
{
    return [
        'home'             => ['view' => 'public/home.php'],
        'catalog'          => ['view' => 'public/catalog.php'],
        'product'          => ['view' => 'public/product.php'],
        'login'            => ['view' => 'public/home.php'],
        'register' => ['view' => 'public/home.php'],
        'account_settings' => ['view' => 'shared/account.php'],  // general – no role restriction
        'buyer' => ['view' => 'buyer/dashboard.php', 'role' => 'buyer'],
        'buyer_account' => ['view' => 'shared/account.php', 'role' => 'buyer'],
        'buyer_wishlist' => ['view' => 'buyer/wishlist.php', 'role' => 'buyer'],
        'buyer_cart' => ['view' => 'buyer/cart_page.php', 'role' => 'buyer'],
        'buyer_orders' => ['view' => 'buyer/orders.php', 'role' => 'buyer'],
        'buyer_reviews' => ['view' => 'buyer/reviews.php', 'role' => 'buyer'],
        'buyer_notifications' => ['view' => 'buyer/notifications.php', 'role' => 'buyer'],
        'cart' => ['view' => 'buyer/cart_page.php'],
        'checkout' => ['view' => 'buyer/checkout.php'],
        'tracking' => ['view' => 'buyer/orders.php'],
        'seller' => ['view' => 'seller/dashboard.php', 'role' => 'seller'],
        'seller_products' => ['view' => 'seller/products.php', 'role' => 'seller'],
        'seller_orders' => ['view' => 'seller/orders.php', 'role' => 'seller'],
        'seller_reviews' => ['view' => 'seller/reviews.php', 'role' => 'seller'],
        'seller_notifications' => ['view' => 'seller/notifications.php', 'role' => 'seller'],
        'seller_report' => ['view' => 'seller/reports.php', 'role' => 'seller'],
        'admin' => ['view' => 'admin/dashboard.php', 'role' => 'admin'],
        'admin_users' => ['view' => 'admin/users.php', 'role' => 'admin'],
        'admin_analytics' => ['view' => 'admin/analytics.php', 'role' => 'admin'],
        'admin_categories' => ['view' => 'admin/categories.php', 'role' => 'admin'],
        'admin_products' => ['view' => 'admin/products.php', 'role' => 'admin'],
        'admin_orders' => ['view' => 'admin/orders.php', 'role' => 'admin'],
        'admin_notifications' => ['view' => 'admin/notifications.php', 'role' => 'admin'],
        'admin_settings' => ['view' => 'admin/settings.php', 'role' => 'admin'],

    ][$page] ?? ['view' => 'errors/404.php', 'status' => 404];
}

function page_data(PDO $pdo, string $page): array
{
    if ($page === 'home') {
        $products = $pdo->query('SELECT p.*, c.name category, COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) as avg_rating FROM products p JOIN categories c ON c.id=p.category_id WHERE p.status="active" ORDER BY (SELECT SUM(qty) FROM order_items WHERE product_id=p.id) DESC, p.id DESC LIMIT 6')->fetchAll();
        $featured = $pdo->query('SELECT p.*, c.name category, COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) as avg_rating FROM products p JOIN categories c ON c.id=p.category_id WHERE p.status="active" ORDER BY RAND() LIMIT 1')->fetch();
        
        // Hero visual: 4 books (different from bestsellers, for decoration)
        $heroProducts = $pdo->query('SELECT p.*, c.name category FROM products p JOIN categories c ON c.id=p.category_id WHERE p.status="active" ORDER BY RAND() LIMIT 4')->fetchAll();
        
        // Dynamic hero stats
        $totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn();
        $totalBuyers   = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role="buyer"')->fetchColumn();
        $totalSellers  = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role="seller"')->fetchColumn();
        $avgRating     = (float)$pdo->query('SELECT COALESCE(AVG(rating), 0) FROM reviews')->fetchColumn();

        // Dynamic categories for strip
        $homeCategories = $pdo->query('SELECT * FROM categories ORDER BY name LIMIT 8')->fetchAll();

        // Dynamic testimonials from reviews
        $testimonials = $pdo->query('
            SELECT r.comment, r.rating, u.name buyer_name
            FROM reviews r
            JOIN users u ON u.id = r.buyer_id
            WHERE r.comment IS NOT NULL AND r.comment != ""
            ORDER BY r.id DESC LIMIT 3
        ')->fetchAll();

        return [
            'products'       => $products,
            'featured'       => $featured,
            'heroProducts'   => $heroProducts,
            'heroStats'      => [
                'products' => $totalProducts,
                'rating'   => $avgRating > 0 ? number_format($avgRating, 1) . '★' : '5.0★',
                'buyers'   => $totalBuyers,
                'sellers'  => $totalSellers,
            ],
            'homeCategories' => $homeCategories,
            'testimonials'   => $testimonials,
        ];
    }
    if ($page === 'product') {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('
            SELECT p.*, c.name category, u.name seller_name,
                   COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) as avg_rating,
                   COALESCE((SELECT COUNT(*) FROM reviews WHERE product_id=p.id), 0) as review_count,
                   COALESCE((SELECT SUM(qty) FROM order_items WHERE product_id=p.id), 0) as sold_count
            FROM products p
            JOIN categories c ON c.id = p.category_id
            JOIN users u ON u.id = p.seller_id
            WHERE p.id = ? AND p.status = "active"
        ');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            header('Location: index.php?page=catalog');
            exit;
        }

        // Reviews
        $revStmt = $pdo->prepare('
            SELECT r.*, u.name buyer_name
            FROM reviews r JOIN users u ON u.id = r.buyer_id
            WHERE r.product_id = ? ORDER BY r.id DESC
        ');
        $revStmt->execute([$id]);
        $reviews = $revStmt->fetchAll();

        // Rating breakdown
        $breakdown = [5=>0,4=>0,3=>0,2=>0,1=>0];
        foreach ($reviews as $rv) { $breakdown[(int)$rv['rating']]++; }

        // Wishlist state
        $inWishlist = false;
        $cu = current_user();
        if ($cu && $cu['role'] === 'buyer') {
            $wStmt = $pdo->prepare('SELECT id FROM wishlists WHERE buyer_id=? AND product_id=?');
            $wStmt->execute([$cu['id'], $id]);
            $inWishlist = (bool)$wStmt->fetch();
        }

        // Related products (same category)
        $relStmt = $pdo->prepare('
            SELECT p.*, c.name category FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.category_id = ? AND p.id != ? AND p.status = "active"
            ORDER BY RAND() LIMIT 4
        ');
        $relStmt->execute([$product['category_id'], $id]);
        $related = $relStmt->fetchAll();

        return [
            'product'    => $product,
            'reviews'    => $reviews,
            'breakdown'  => $breakdown,
            'inWishlist' => $inWishlist,
            'related'    => $related,
        ];
    }
    if ($page === 'catalog') {
        $qStr = $_GET['q'] ?? '';
        $q = '%' . $qStr . '%';
        $categoriesFilter = array_filter(array_map('intval', (array) ($_GET['category'] ?? [])));
        $priceMax = (int) ($_GET['price_max'] ?? 300000);
        $ratingsFilter = array_filter(array_map('floatval', (array) ($_GET['rating'] ?? [])));
        $conditionsFilter = array_filter((array) ($_GET['condition'] ?? []), fn($v) => in_array($v, ['new', 'used_good', 'used_fair'], true));
        $sort = $_GET['sort'] ?? 'Terlaris';
        $p = max(1, (int) ($_GET['p'] ?? 1));
        $limit = 8;
        $offset = ($p - 1) * $limit;

        $sql = 'SELECT p.*, c.name category, 
                       COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) as avg_rating,
                       COALESCE((SELECT SUM(qty) FROM order_items WHERE product_id=p.id), 0) as sales_count
                FROM products p 
                JOIN categories c ON c.id=p.category_id 
                WHERE p.status="active" AND p.name LIKE ? AND p.price <= ?';
        
        $params = [$q, $priceMax];

        if (!empty($categoriesFilter)) {
            $placeholders = implode(',', array_fill(0, count($categoriesFilter), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            $params = array_merge($params, $categoriesFilter);
        }

        if (!empty($conditionsFilter)) {
            $placeholders = implode(',', array_fill(0, count($conditionsFilter), '?'));
            $sql .= " AND p.book_condition IN ($placeholders)";
            $params = array_merge($params, $conditionsFilter);
        }

        // Ratings filter (minimum rating requested among selected options)
        if (!empty($ratingsFilter)) {
            $minRating = min($ratingsFilter);
            $sql .= " AND COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) >= ?";
            $params[] = $minRating;
        }

        // Count total for pagination
        $countSql = "SELECT COUNT(*) FROM ($sql) as t";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = (int) $countStmt->fetchColumn();
        $totalPages = max(1, ceil($totalItems / $limit));

        // Sort
        $orderBy = match($sort) {
            'Terbaru' => 'p.created_at DESC',
            'Harga Terendah' => 'p.price ASC',
            'Harga Tertinggi' => 'p.price DESC',
            'Rating Tertinggi' => 'avg_rating DESC',
            default => 'sales_count DESC, p.id DESC', // Terlaris
        };

        $sql .= " ORDER BY $orderBy LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return [
            'products' => $stmt->fetchAll(), 
            'categories' => $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(),
            'filters' => [
                'q' => $qStr,
                'category' => $categoriesFilter,
                'price_max' => $priceMax,
                'rating' => $ratingsFilter,
                'condition' => $conditionsFilter,
                'sort' => $sort,
                'page' => $p,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems
            ]
        ];
    }
    if ($page === 'account_settings' || $page === 'buyer_account') {
        $uid = current_user()['id'];
        $stmt = $pdo->prepare('SELECT id, name, email, role, created_at, avatar, phone, dob, alternate_phone, delete_requested FROM users WHERE id = ?');
        $stmt->execute([$uid]);
        $freshUser = $stmt->fetch();
        // Sync session name in case it was updated
        if ($freshUser) {
            $_SESSION['user']['name'] = $freshUser['name'];
        }
        $data = ['user' => $freshUser ?: current_user()];
        if (($freshUser['role'] ?? 'buyer') === 'buyer') {
            $data['buyerSidebar'] = buyer_sidebar_data($pdo);
        }
        return $data;
    }
    if (in_array($page, ['buyer', 'buyer_account', 'buyer_wishlist', 'buyer_cart', 'buyer_orders', 'buyer_reviews', 'buyer_notifications', 'cart', 'tracking'], true)) {
        $base = ['buyerSidebar' => buyer_sidebar_data($pdo)];
        $uid = current_user()['id'];

        if ($page === 'buyer') {
            $stmt = $pdo->prepare('SELECT * FROM orders WHERE buyer_id=? ORDER BY id DESC LIMIT 5');
            $stmt->execute([$uid]);
            return array_merge($base, [
                'stats' => buyer_stats($pdo),
                'recentOrders' => $stmt->fetchAll(),
            ]);
        }
        if (in_array($page, ['buyer_cart', 'cart'], true)) {
            $stmt = $pdo->prepare('SELECT c.id cart_id,c.qty,p.* FROM carts c JOIN products p ON p.id=c.product_id WHERE c.buyer_id=?');
            $stmt->execute([$uid]);
            return array_merge($base, ['items' => $stmt->fetchAll()]);
        }
        if ($page === 'buyer_orders') {
            $stmt = $pdo->prepare('SELECT * FROM orders WHERE buyer_id=? ORDER BY id DESC');
            $stmt->execute([$uid]);
            $orders = $stmt->fetchAll();
            $itemsStmt = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?');
            foreach ($orders as &$o) {
                $itemsStmt->execute([$o['id']]);
                $o['items'] = $itemsStmt->fetchAll();
            }
            return array_merge($base, ['orders' => $orders]);
        }
        if ($page === 'tracking') {
            $stmt = $pdo->prepare('SELECT * FROM orders WHERE buyer_id=? ORDER BY id DESC');
            $stmt->execute([$uid]);
            return array_merge($base, ['orders' => $stmt->fetchAll()]);
        }
        if ($page === 'buyer_wishlist') {
            $stmt = $pdo->prepare('SELECT w.*, p.*, c.name category FROM wishlists w JOIN products p ON p.id = w.product_id JOIN categories c ON c.id = p.category_id WHERE w.buyer_id = ? ORDER BY w.id DESC');
            $stmt->execute([$uid]);
            return array_merge($base, ['wishlistItems' => $stmt->fetchAll()]);
        }
        if ($page === 'buyer_reviews') {
            $stmt = $pdo->prepare('SELECT r.*, p.name product_name FROM reviews r JOIN products p ON p.id=r.product_id WHERE r.buyer_id=? ORDER BY r.id DESC');
            $stmt->execute([$uid]);
            return array_merge($base, ['reviews' => $stmt->fetchAll()]);
        }
        if ($page === 'buyer_notifications') {
            $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC');
            $stmt->execute([$uid]);
            return array_merge($base, ['notifications' => $stmt->fetchAll()]);
        }

        return $base;
    }
    if ($page === 'seller') {
        $sellerId = current_user()['id'];
        
        // Total revenue (from completed/paid orders)
        $stmtRev = $pdo->prepare('SELECT COALESCE(SUM(oi.subtotal), 0) as total_revenue FROM order_items oi JOIN products p ON p.id = oi.product_id JOIN orders o ON o.id = oi.order_id WHERE p.seller_id = ? AND o.status IN ("paid", "processing", "shipped", "delivered")');
        $stmtRev->execute([$sellerId]);
        $revenue = $stmtRev->fetch()['total_revenue'];

        // Total orders
        $stmtOrd = $pdo->prepare('SELECT COUNT(DISTINCT oi.order_id) as total_orders FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE p.seller_id = ?');
        $stmtOrd->execute([$sellerId]);
        $totalOrders = $stmtOrd->fetch()['total_orders'];

        // Active products & Low stock
        $stmtProd = $pdo->prepare('SELECT COUNT(*) as active_products FROM products WHERE seller_id = ? AND status = "active"');
        $stmtProd->execute([$sellerId]);
        $activeProducts = $stmtProd->fetch()['active_products'];

        $stmtLowStock = $pdo->prepare('SELECT * FROM products WHERE seller_id = ? AND stock <= 10 AND status = "active" ORDER BY stock ASC LIMIT 5');
        $stmtLowStock->execute([$sellerId]);
        $lowStockProducts = $stmtLowStock->fetchAll();

        // Average rating
        $stmtRating = $pdo->prepare('SELECT COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as total_reviews FROM reviews r JOIN products p ON p.id = r.product_id WHERE p.seller_id = ?');
        $stmtRating->execute([$sellerId]);
        $ratingData = $stmtRating->fetch();

        // Recent orders
        $stmtRecent = $pdo->prepare('SELECT DISTINCT o.*, u.name as buyer_name, (SELECT GROUP_CONCAT(p2.name SEPARATOR ", ") FROM order_items oi2 JOIN products p2 ON p2.id = oi2.product_id WHERE oi2.order_id = o.id AND p2.seller_id = ?) as product_names, (SELECT SUM(oi3.subtotal) FROM order_items oi3 JOIN products p3 ON p3.id = oi3.product_id WHERE oi3.order_id = o.id AND p3.seller_id = ?) as seller_total FROM orders o JOIN order_items oi ON oi.order_id = o.id JOIN products p ON p.id = oi.product_id JOIN users u ON u.id = o.buyer_id WHERE p.seller_id = ? ORDER BY o.created_at DESC LIMIT 5');
        $stmtRecent->execute([$sellerId, $sellerId, $sellerId]);
        $recentOrders = $stmtRecent->fetchAll();

        // Monthly Sales (Last 6 Months)
        $stmtSales = $pdo->prepare("SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month_key, SUM(oi.subtotal) as total FROM orders o JOIN order_items oi ON oi.order_id = o.id JOIN products p ON p.id = oi.product_id WHERE p.seller_id = ? AND o.created_at >= DATE_SUB(NOW(), INTERVAL 5 MONTH) AND o.status != 'cancelled' GROUP BY month_key ORDER BY month_key ASC");
        $stmtSales->execute([$sellerId]);
        $salesDataRaw = [];
        foreach ($stmtSales->fetchAll() as $row) {
            $salesDataRaw[$row['month_key']] = (float)$row['total'];
        }
        
        $monthlySales = [];
        $totalSales6Months = 0;
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = date('Y-m', strtotime("-$i months"));
            $monthName = date('M', strtotime("-$i months"));
            $val = $salesDataRaw[$monthKey] ?? 0;
            $monthlySales[] = ['label' => $monthName, 'val' => $val];
            $totalSales6Months += $val;
        }

        // Category Distribution
        $stmtCats = $pdo->prepare("SELECT c.name, COUNT(p.id) as product_count FROM products p JOIN categories c ON c.id = p.category_id WHERE p.seller_id = ? AND p.status = 'active' GROUP BY c.id ORDER BY product_count DESC LIMIT 4");
        $stmtCats->execute([$sellerId]);
        $categoryDistribution = $stmtCats->fetchAll();

        return [
            'revenue' => $revenue,
            'totalOrders' => $totalOrders,
            'activeProducts' => $activeProducts,
            'lowStockProducts' => $lowStockProducts,
            'ratingData' => $ratingData,
            'recentOrders' => $recentOrders,
            'monthlySales' => $monthlySales,
            'totalSales6Months' => $totalSales6Months,
            'categoryDistribution' => $categoryDistribution
        ];
    }
    if ($page === 'seller_products') {
        $sellerId = current_user()['id'];
        $qStr = $_GET['q'] ?? '';
        $q = '%' . $qStr . '%';
        $categoryFilter = $_GET['category'] ?? '';
        $sort = $_GET['sort'] ?? 'Terbaru';

        $sql = 'SELECT p.*, c.name category,
                       COALESCE((SELECT SUM(oi.qty) FROM order_items oi JOIN orders o ON o.id = oi.order_id WHERE oi.product_id = p.id AND o.status IN ("paid", "processing", "shipped", "delivered")), 0) as sold_count
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE p.seller_id = ? AND p.name LIKE ?';
        
        $params = [$sellerId, $q];
        if (!empty($categoryFilter)) {
            $sql .= ' AND p.category_id = ?';
            $params[] = (int)$categoryFilter;
        }

        $orderBy = match($sort) {
            'Harga Terendah' => 'p.price ASC',
            'Harga Tertinggi' => 'p.price DESC',
            'Stok Terendah' => 'p.stock ASC',
            'Stok Tertinggi' => 'p.stock DESC',
            'Terlaris' => 'sold_count DESC',
            default => 'p.id DESC', // Terbaru
        };

        $sql .= " ORDER BY $orderBy";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        return [
            'products' => $products,
            'categories' => $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(),
            'filters' => [
                'q' => $qStr,
                'category' => $categoryFilter,
                'sort' => $sort
            ]
        ];
    }
    if ($page === 'seller_orders') {
        $sellerId = current_user()['id'];
        $qStr = $_GET['q'] ?? '';
        $q = '%' . $qStr . '%';
        $statusFilter = $_GET['status'] ?? '';
        
        $sql = 'SELECT DISTINCT o.*, u.name buyer_name,
                       (SELECT SUM(oi.subtotal) FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = o.id AND p.seller_id = ?) as seller_total
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.id
                JOIN products p ON p.id = oi.product_id
                JOIN users u ON u.id = o.buyer_id
                WHERE p.seller_id = ? AND (o.invoice_number LIKE ? OR u.name LIKE ?)';
        
        $params = [$sellerId, $sellerId, $q, $q];
        if (!empty($statusFilter)) {
            if ($statusFilter === 'pending') {
                $sql .= ' AND o.status IN ("pending", "paid")';
            } else {
                $sql .= ' AND o.status = ?';
                $params[] = $statusFilter;
            }
        }
        
        $sql .= ' ORDER BY o.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
        
        // For each order, fetch the items belonging to this seller
        $itemsStmt = $pdo->prepare('
            SELECT oi.*, p.name product_name, c.name category_name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN categories c ON c.id = p.category_id
            WHERE oi.order_id = ? AND p.seller_id = ?
        ');
        foreach ($orders as &$order) {
            $itemsStmt->execute([$order['id'], $sellerId]);
            $order['items'] = $itemsStmt->fetchAll();
        }
        unset($order); // break reference

        if (($_GET['export'] ?? '') === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=pesanan_masuk_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['No. Invoice', 'Tanggal', 'Pembeli', 'Item', 'Total Pendapatan', 'Status', 'Nomor Resi']);
            
            foreach ($orders as $order) {
                $itemNames = [];
                foreach ($order['items'] as $item) {
                    $itemNames[] = $item['product_name'] . ' (x' . $item['qty'] . ')';
                }
                fputcsv($output, [
                    $order['invoice_number'],
                    $order['created_at'],
                    $order['buyer_name'],
                    implode(', ', $itemNames),
                    $order['seller_total'],
                    $order['status'],
                    $order['receipt_number'] ?? '-'
                ]);
            }
            fclose($output);
            exit;
        }

        return [
            'orders' => $orders,
            'filters' => [
                'q' => $qStr,
                'status' => $statusFilter
            ]
        ];
    }
    if ($page === 'seller_reviews') {
        $sellerId   = current_user()['id'];
        $ratingFilter = isset($_GET['rating']) && $_GET['rating'] !== '' ? (int)$_GET['rating'] : null;

        // Always fetch ALL reviews first for stats calculation
        $stmtAll = $pdo->prepare(
            'SELECT r.*, u.name buyer_name, p.name product_name
             FROM reviews r
             JOIN products p ON p.id = r.product_id
             JOIN users u ON u.id = r.buyer_id
             WHERE p.seller_id = ?
             ORDER BY r.created_at DESC'
        );
        $stmtAll->execute([$sellerId]);
        $allReviews = $stmtAll->fetchAll();

        // Calculate stats from ALL reviews
        $totalReviews = count($allReviews);
        $avgRating    = 0;
        $breakdown    = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        if ($totalReviews > 0) {
            $sum = 0;
            foreach ($allReviews as $rev) {
                $r = (int)$rev['rating'];
                $sum += $r;
                if (isset($breakdown[$r])) $breakdown[$r]++;
            }
            $avgRating = round($sum / $totalReviews, 1);
        }

        // Apply rating filter for display list
        $reviews = $ratingFilter
            ? array_values(array_filter($allReviews, fn($r) => (int)$r['rating'] === $ratingFilter))
            : $allReviews;

        return [
            'reviews' => $reviews,
            'filters' => ['rating' => $ratingFilter],
            'stats'   => [
                'average'   => $avgRating,
                'total'     => $totalReviews,
                'breakdown' => $breakdown,
            ],
        ];
    }
    if ($page === 'seller_report') {
        $sellerId = current_user()['id'];
        $period = $_GET['period'] ?? 'week'; // 'week', 'month', 'year'
        
        // Setup Date Ranges & Labels
        $today = date('Y-m-d H:i:s');
        $labels = [];
        $dataPoints = []; // ['label' => X, 'revenue' => Y, 'orders' => Z]

        if ($period === 'week') {
            // Last 7 days including today
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $dayLabel = date('D', strtotime("-$i days"));
                $labels[$d] = match($dayLabel) {
                    'Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 
                    'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab', 'Sun' => 'Min',
                    default => $dayLabel
                };
                $dataPoints[$d] = ['label' => $labels[$d], 'revenue' => 0.0, 'orders' => 0];
            }
            $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $endDate = date('Y-m-d 23:59:59');

            // Previous period for comparing percentage (7 days before that)
            $prevStartDate = date('Y-m-d 00:00:00', strtotime('-13 days'));
            $prevEndDate = date('Y-m-d 23:59:59', strtotime('-7 days'));
            $compareLabel = 'vs minggu lalu';
        } elseif ($period === 'month') {
            // Last 30 days grouped in 5-day intervals or just daily
            for ($i = 29; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                // Format: Date number
                $labels[$d] = date('j', strtotime("-$i days"));
                $dataPoints[$d] = ['label' => $labels[$d], 'revenue' => 0.0, 'orders' => 0];
            }
            $startDate = date('Y-m-d 00:00:00', strtotime('-29 days'));
            $endDate = date('Y-m-d 23:59:59');

            $prevStartDate = date('Y-m-d 00:00:00', strtotime('-59 days'));
            $prevEndDate = date('Y-m-d 23:59:59', strtotime('-30 days'));
            $compareLabel = 'vs bulan lalu';
        } else { // 'year'
            // Last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $m = date('Y-m', strtotime("-$i months"));
                $monthLabel = date('M', strtotime("-$i months"));
                $labels[$m] = match($monthLabel) {
                    'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
                    'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des',
                    default => $monthLabel
                };
                $dataPoints[$m] = ['label' => $labels[$m], 'revenue' => 0.0, 'orders' => 0];
            }
            $startDate = date('Y-m-01 00:00:00', strtotime('-11 months'));
            $endDate = date('Y-m-d 23:59:59');

            $prevStartDate = date('Y-m-01 00:00:00', strtotime('-23 months'));
            $prevEndDate = date('Y-m-t 23:59:59', strtotime('-12 months'));
            $compareLabel = 'vs tahun lalu';
        }

        // --- Current Period Query ---
        // Revenue & Orders
        $stmtCurr = $pdo->prepare('
            SELECT 
                o.id as order_id, 
                o.created_at, 
                SUM(oi.subtotal) as subtotal, 
                SUM(oi.qty) as qty
            FROM order_items oi 
            JOIN products p ON p.id = oi.product_id 
            JOIN orders o ON o.id = oi.order_id 
            WHERE p.seller_id = ? 
              AND o.created_at BETWEEN ? AND ? 
              AND o.status IN ("paid", "processing", "shipped", "delivered")
            GROUP BY o.id, o.created_at
        ');
        $stmtCurr->execute([$sellerId, $startDate, $endDate]);
        $currOrdersData = $stmtCurr->fetchAll();

        $currRevenue = 0.0;
        $currOrdersCount = 0;
        $currBooksCount = 0;
        $orderIdsSeen = [];

        foreach ($currOrdersData as $row) {
            $currRevenue += (float)$row['subtotal'];
            $currBooksCount += (int)$row['qty'];
            if (!in_array($row['order_id'], $orderIdsSeen, true)) {
                $orderIdsSeen[] = $row['order_id'];
                $currOrdersCount++;
            }

            // Map to graph datapoints
            $time = strtotime($row['created_at']);
            if ($period === 'year') {
                $key = date('Y-m', $time);
            } else {
                $key = date('Y-m-d', $time);
            }

            if (isset($dataPoints[$key])) {
                $dataPoints[$key]['revenue'] += (float)$row['subtotal'];
                $dataPoints[$key]['orders'] += 1;
            }
        }

        // --- Previous Period Query ---
        $stmtPrev = $pdo->prepare('
            SELECT 
                o.id as order_id, 
                SUM(oi.subtotal) as subtotal, 
                SUM(oi.qty) as qty
            FROM order_items oi 
            JOIN products p ON p.id = oi.product_id 
            JOIN orders o ON o.id = oi.order_id 
            WHERE p.seller_id = ? 
              AND o.created_at BETWEEN ? AND ? 
              AND o.status IN ("paid", "processing", "shipped", "delivered")
            GROUP BY o.id
        ');
        $stmtPrev->execute([$sellerId, $prevStartDate, $prevEndDate]);
        $prevOrdersData = $stmtPrev->fetchAll();

        $prevRevenue = 0.0;
        $prevOrdersCount = 0;
        $prevBooksCount = 0;

        foreach ($prevOrdersData as $row) {
            $prevRevenue += (float)$row['subtotal'];
            $prevBooksCount += (int)$row['qty'];
            $prevOrdersCount++;
        }

        // --- Calculate Percentage Change ---
        $revTrendVal = 0;
        if ($prevRevenue > 0) {
            $revTrendVal = round((($currRevenue - $prevRevenue) / $prevRevenue) * 100);
        } elseif ($currRevenue > 0) {
            $revTrendVal = 100;
        }

        $ordTrendVal = 0;
        if ($prevOrdersCount > 0) {
            $ordTrendVal = $currOrdersCount - $prevOrdersCount;
        } else {
            $ordTrendVal = $currOrdersCount;
        }

        $bookTrendVal = 0;
        if ($prevBooksCount > 0) {
            $bookTrendVal = $currBooksCount - $prevBooksCount;
        } else {
            $bookTrendVal = $currBooksCount;
        }

        // --- Best Sellers Query ---
        $stmtBest = $pdo->prepare('
            SELECT 
                p.id, 
                p.name as product_name, 
                c.name as category_name, 
                SUM(oi.qty) as sold_qty, 
                SUM(oi.subtotal) as total_rev
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN categories c ON c.id = p.category_id
            JOIN orders o ON o.id = oi.order_id
            WHERE p.seller_id = ?
              AND o.created_at BETWEEN ? AND ?
              AND o.status IN ("paid", "processing", "shipped", "delivered")
            GROUP BY p.id, p.name, c.name
            ORDER BY sold_qty DESC, total_rev DESC
            LIMIT 5
        ');
        $stmtBest->execute([$sellerId, $startDate, $endDate]);
        $bestSellers = $stmtBest->fetchAll();

        if (($_GET['export'] ?? '') === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=laporan_penjualan_' . $period . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Laporan Penjualan - Periode: ' . strtoupper($period)]);
            fputcsv($output, []);
            fputcsv($output, ['Total Pendapatan', 'Rp ' . number_format($currRevenue, 0, ',', '.')]);
            fputcsv($output, ['Total Pesanan', $currOrdersCount . ' pesanan']);
            fputcsv($output, ['Buku Terjual', $currBooksCount . ' buku']);
            fputcsv($output, []);
            
            fputcsv($output, ['Label Tren', 'Pendapatan', 'Jumlah Pesanan']);
            foreach ($dataPoints as $dp) {
                fputcsv($output, [$dp['label'], $dp['revenue'], $dp['orders']]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Produk Terlaris', 'Kategori', 'Jumlah Terjual', 'Pendapatan']);
            foreach ($bestSellers as $b) {
                fputcsv($output, [$b['product_name'], $b['category_name'], $b['sold_qty'], $b['total_rev']]);
            }
            fclose($output);
            exit;
        }

        return [
            'period' => $period,
            'compareLabel' => $compareLabel,
            'kpi' => [
                'revenue' => $currRevenue,
                'revenue_trend' => $revTrendVal,
                'orders' => $currOrdersCount,
                'orders_trend' => $ordTrendVal,
                'books' => $currBooksCount,
                'books_trend' => $bookTrendVal,
            ],
            'chartData' => array_values($dataPoints),
            'bestSellers' => $bestSellers,
        ];
    }
    if ($page === 'seller_notifications') {
        $uid = current_user()['id'];
        $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY is_read ASC, created_at DESC');
        $stmt->execute([$uid]);
        $notifications = $stmt->fetchAll();
        $unreadCount = count(array_filter($notifications, fn($n) => (int)($n['is_read'] ?? 0) === 0));
        return [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ];
    }
    if ($page === 'admin_notifications') {
        $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC');
        $stmt->execute([current_user()['id']]);
        return ['notifications' => $stmt->fetchAll()];
    }
    if ($page === 'admin') {
        // 1. KPI Stats
        $totalSellers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller'")->fetchColumn();
        $verifiedSellers = (int) $pdo->query("SELECT COUNT(*) FROM users u JOIN seller_verifications sv ON sv.seller_id = u.id WHERE u.role = 'seller' AND sv.status = 'approved'")->fetchColumn();
        $pendingSellers = (int) $pdo->query("SELECT COUNT(*) FROM users u JOIN seller_verifications sv ON sv.seller_id = u.id WHERE u.role = 'seller' AND sv.status = 'pending'")->fetchColumn();
        $sellersThisMonth = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetchColumn();

        $verificationQueue = (int) $pdo->query("SELECT COUNT(*) FROM seller_verifications WHERE status = 'pending'")->fetchColumn();

        $totalOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $completedOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
        $shippedOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn();
        $paidOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
        $pendingOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
        $ordersToday = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE created_at >= CURDATE()")->fetchColumn();

        $revenueMonth = (int) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetchColumn();
        $revenueYTD = (int) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled' AND created_at >= DATE_FORMAT(NOW(), '%Y-01-01')")->fetchColumn();

        // 2. Pending Sellers (Alert Banner)
        $pendingSellersList = $pdo->query("SELECT u.name FROM users u JOIN seller_verifications sv ON sv.seller_id = u.id WHERE u.role = 'seller' AND sv.status = 'pending' ORDER BY sv.id ASC")->fetchAll(PDO::FETCH_COLUMN);

        // 3. Monthly Revenue (Last 6 Months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = date('Y-m-01 00:00:00', strtotime("-$i months"));
            $monthEnd = date('Y-m-t 23:59:59', strtotime("-$i months"));
            $monthLabel = date('M', strtotime("-$i months"));
            
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled' AND created_at BETWEEN ? AND ?");
            $stmt->execute([$monthStart, $monthEnd]);
            $totalVal = (int) $stmt->fetchColumn();
            
            $monthlyRevenue[] = [
                'label' => $monthLabel,
                'total' => $totalVal
            ];
        }

        // 4. Recent Orders
        $recentOrders = $pdo->query("SELECT o.*, u.name as buyer_name, p.method as payment_method FROM orders o JOIN users u ON u.id = o.buyer_id LEFT JOIN payments p ON p.order_id = o.id ORDER BY o.id DESC LIMIT 5")->fetchAll();
        $stmtSellers = $pdo->prepare("
            SELECT DISTINCT u.name 
            FROM order_items oi 
            JOIN products p ON p.id = oi.product_id 
            JOIN users u ON u.id = p.seller_id 
            WHERE oi.order_id = ?
        ");
        foreach ($recentOrders as &$o) {
            $stmtSellers->execute([$o['id']]);
            $o['seller_names'] = implode(', ', $stmtSellers->fetchAll(PDO::FETCH_COLUMN));
        }
        unset($o);

        // 5. Recent Users
        $recentUsers = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 5")->fetchAll();

        // 6. Recent Logs
        $recentLogs = $pdo->query("SELECT * FROM system_logs ORDER BY id DESC LIMIT 5")->fetchAll();

        return [
            'stats' => [
                'total_sellers' => $totalSellers,
                'verified_sellers' => $verifiedSellers,
                'pending_sellers' => $pendingSellers,
                'sellers_this_month' => $sellersThisMonth,
                'verification_queue' => $verificationQueue,
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'shipped_orders' => $shippedOrders,
                'paid_orders' => $paidOrders,
                'pending_orders' => $pendingOrders,
                'orders_today' => $ordersToday,
                'revenue_month' => $revenueMonth,
                'revenue_ytd' => $revenueYTD
            ],
            'pendingSellersList' => $pendingSellersList,
            'monthlyRevenue' => $monthlyRevenue,
            'recentOrders' => $recentOrders,
            'recentUsers' => $recentUsers,
            'recentLogs' => $recentLogs
        ];
    }
    if ($page === 'admin_users') {
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC');
            $stmt->execute(["%$q%", "%$q%"]);
            return ['users' => $stmt->fetchAll(), 'q' => $q];
        }
        return ['users' => $pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll(), 'q' => ''];
    }
    if ($page === 'admin_categories') {
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $stmt = $pdo->prepare('SELECT * FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC');
            $stmt->execute(["%$q%", "%$q%"]);
            return ['categories' => $stmt->fetchAll(), 'q' => $q];
        }
        return ['categories' => $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll(), 'q' => ''];
    }
    if ($page === 'admin_products') {
        $q = trim($_GET['q'] ?? '');
        $sql = '
            SELECT p.*, c.name AS category, u.name AS seller_name,
                   COALESCE((SELECT SUM(oi.qty) FROM order_items oi WHERE oi.product_id = p.id), 0) AS sold_count
            FROM products p
            JOIN categories c ON c.id = p.category_id
            JOIN users u ON u.id = p.seller_id
        ';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE p.name LIKE ? OR c.name LIKE ? OR u.name LIKE ?';
            $params = ["%$q%", "%$q%", "%$q%"];
        }
        $sql .= ' ORDER BY p.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return ['products' => $stmt->fetchAll(), 'q' => $q];
    }
    if ($page === 'admin_orders') {
        $q = trim($_GET['q'] ?? '');
        $sql = '
            SELECT o.*, u.name AS buyer_name, pay.method AS payment_method, pay.status AS payment_status
            FROM orders o
            JOIN users u ON u.id = o.buyer_id
            LEFT JOIN payments pay ON pay.order_id = o.id
        ';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE o.invoice_number LIKE ? OR u.name LIKE ? OR o.status LIKE ?';
            $params = ["%$q%", "%$q%", "%$q%"];
        }
        $sql .= ' ORDER BY o.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        $sellerStmt = $pdo->prepare('
            SELECT DISTINCT u.name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN users u ON u.id = p.seller_id
            WHERE oi.order_id = ?
        ');
        foreach ($orders as &$order) {
            $sellerStmt->execute([$order['id']]);
            $order['seller_names'] = implode(', ', $sellerStmt->fetchAll(PDO::FETCH_COLUMN));
        }
        unset($order);

        return ['orders' => $orders, 'q' => $q];
    }
    if ($page === 'admin_analytics') {
        // ── PERIOD FILTER (4 options only) ──
        $period = in_array($_GET['period'] ?? '', ['daily','30days','1year','all']) ? ($_GET['period'] ?? 'daily') : 'daily';

        switch ($period) {
            case 'daily':
                // Current week Mon–Sun
                $periodLabel = 'Daily (Minggu Ini)';
                $periodStart = date('Y-m-d', strtotime('monday this week'));
                break;
            case '30days':
                $periodLabel = 'Last 30 Days';
                $periodStart = date('Y-m-d', strtotime('-30 days'));
                break;
            case '1year':
                $periodLabel = 'Last 1 Year';
                $periodStart = date('Y-m-d', strtotime('-1 year'));
                break;
            case 'all':
            default:
                $periodLabel = 'All Time';
                $periodStart = '2000-01-01';
                break;
        }

        // ── KPI within period ──
        $stmtRev = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND created_at>=?");
        $stmtRev->execute([$periodStart]);
        $totalRevenue = (int)$stmtRev->fetchColumn();

        $stmtOrd = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status<>'cancelled' AND created_at>=?");
        $stmtOrd->execute([$periodStart]);
        $totalOrdersPeriod = (int)$stmtOrd->fetchColumn();

        $avgOrderVal = $totalOrdersPeriod > 0 ? (int)($totalRevenue / $totalOrdersPeriod) : 0;
        $activeUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();

        // Month-over-month badge comparisons (fixed, always vs last month)
        $revenueThisMonth = (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND created_at>=DATE_FORMAT(NOW(),'%Y-%m-01')")->fetchColumn();
        $revenueLastMonth = (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND created_at>=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%Y-%m-01') AND created_at<DATE_FORMAT(NOW(),'%Y-%m-01')")->fetchColumn();
        $ordersThisMonth  = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status<>'cancelled' AND created_at>=DATE_FORMAT(NOW(),'%Y-%m-01')")->fetchColumn();
        $ordersLastMonth  = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status<>'cancelled' AND created_at>=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%Y-%m-01') AND created_at<DATE_FORMAT(NOW(),'%Y-%m-01')")->fetchColumn();
        $revTrend   = $revenueLastMonth > 0 ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100, 1) : 0;
        $orderTrend = $ordersLastMonth  > 0 ? round(($ordersThisMonth  - $ordersLastMonth)  / $ordersLastMonth  * 100, 1) : 0;

        // ── CHART DATA (auto granularity by period) ──
        $chartData = [];
        if ($period === 'daily') {
            // Mon–Sun of current week, label = Senin/Selasa/…
            $dayNames = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
            for ($d = 0; $d < 7; $d++) {
                $day  = date('Y-m-d', strtotime('monday this week +' . $d . ' days'));
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND DATE(created_at)=?");
                $stmt->execute([$day]);
                $chartData[] = ['label' => $dayNames[$d], 'total' => (int)$stmt->fetchColumn()];
            }
        } elseif ($period === '30days') {
            // Daily for last 30 days
            for ($i = 29; $i >= 0; $i--) {
                $day  = date('Y-m-d', strtotime("-$i days"));
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND DATE(created_at)=?");
                $stmt->execute([$day]);
                // Show every other label for readability
                $chartData[] = ['label' => ($i % 5 === 0 ? date('d/m', strtotime($day)) : ''), 'total' => (int)$stmt->fetchColumn()];
            }
        } elseif ($period === '1year') {
            // Monthly for last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $mStart = date('Y-m-01 00:00:00', strtotime("-$i months"));
                $mEnd   = date('Y-m-t 23:59:59',  strtotime("-$i months"));
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND created_at BETWEEN ? AND ?");
                $stmt->execute([$mStart, $mEnd]);
                $chartData[] = ['label' => date('M', strtotime("-$i months")), 'total' => (int)$stmt->fetchColumn()];
            }
        } else {
            // All time: monthly last 18 months max
            for ($i = 17; $i >= 0; $i--) {
                $mStart = date('Y-m-01 00:00:00', strtotime("-$i months"));
                $mEnd   = date('Y-m-t 23:59:59',  strtotime("-$i months"));
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled' AND created_at BETWEEN ? AND ?");
                $stmt->execute([$mStart, $mEnd]);
                $chartData[] = ['label' => date('M y', strtotime("-$i months")), 'total' => (int)$stmt->fetchColumn()];
            }
        }

        // ── TOP CATEGORIES (within period) ──
        $stmtCat = $pdo->prepare("
            SELECT c.name, COALESCE(SUM(oi.qty),0) as sold
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN categories c ON c.id = p.category_id
            JOIN orders o ON o.id = oi.order_id AND o.status<>'cancelled' AND o.created_at>=?
            GROUP BY c.id, c.name ORDER BY sold DESC LIMIT 5
        ");
        $stmtCat->execute([$periodStart]);
        $topCategories = $stmtCat->fetchAll();
        $totalCatSold  = array_sum(array_column($topCategories, 'sold')) ?: 1;

        // ── TOP SELLERS (within period) ──
        $stmtSell = $pdo->prepare("
            SELECT u.name, u.id,
                   COALESCE(SUM(oi.subtotal),0) as revenue,
                   COALESCE(SUM(oi.qty),0) as sold
            FROM users u
            JOIN products p ON p.seller_id = u.id
            JOIN order_items oi ON oi.product_id = p.id
            JOIN orders o ON o.id = oi.order_id AND o.status<>'cancelled' AND o.created_at>=?
            WHERE u.role='seller'
            GROUP BY u.id, u.name ORDER BY revenue DESC LIMIT 3
        ");
        $stmtSell->execute([$periodStart]);
        $topSellers = $stmtSell->fetchAll();

        // ── TOP PRODUCTS (within period) ──
        $stmtProd = $pdo->prepare("
            SELECT p.name, c.name as category,
                   COALESCE(SUM(oi.qty),0) as sold_this_month
            FROM products p
            JOIN categories c ON c.id = p.category_id
            JOIN order_items oi ON oi.product_id = p.id
            JOIN orders o ON o.id = oi.order_id AND o.status<>'cancelled' AND o.created_at>=?
            GROUP BY p.id, p.name, c.name ORDER BY sold_this_month DESC LIMIT 5
        ");
        $stmtProd->execute([$periodStart]);
        $topProducts = $stmtProd->fetchAll();

        return compact(
            'period','periodLabel',
            'totalRevenue','avgOrderVal','activeUsers',
            'revTrend','orderTrend',
            'ordersThisMonth','revenueThisMonth',
            'chartData','topCategories','totalCatSold',
            'topSellers','topProducts'
        );
    }
    if ($page === 'admin_settings') {
        return ['sysSettings' => get_system_settings($pdo)];
    }

    return [];
}

function render_view(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../views/' . $view;
}

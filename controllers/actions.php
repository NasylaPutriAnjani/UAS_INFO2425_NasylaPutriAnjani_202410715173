<?php
declare(strict_types=1);

function handle_action(PDO $pdo, ?string $action, string $page): void
{
    if ($action === 'register') {
        $role = in_array($_POST['role'] ?? 'buyer', ['buyer', 'seller'], true) ? $_POST['role'] : 'buyer';
        $status = $role === 'seller' ? 'pending' : 'active';
        $stmt = $pdo->prepare('INSERT INTO users (name,email,password,role,status) VALUES (?,?,?,?,?)');
        $stmt->execute([$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $role, $status]);
        $id = (int) $pdo->lastInsertId();
        if ($role === 'seller') {
            $pdo->prepare('INSERT INTO seller_verifications (seller_id) VALUES (?)')->execute([$id]);
        }
        log_activity($pdo, "Registrasi {$role}: {$_POST['email']}");
        flash($role === 'seller' ? 'Akun seller dibuat dan menunggu approval admin.' : 'Registrasi berhasil, silakan login.');
        redirect('home', ['auth' => 'masuk']);
    }

    if ($action === 'login') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'], $user['password']) && $user['status'] === 'active') {
            $_SESSION['user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
            log_activity($pdo, "Login {$user['role']}: {$user['email']}");
            redirect($user['role'] === 'admin' ? 'admin' : ($user['role'] === 'seller' ? 'seller' : 'buyer'));
        }
        flash('Login gagal. Cek email/password atau status akun.', 'error');
        redirect('home', ['auth' => 'masuk']);
    }

    if ($page === 'logout') {
        session_destroy();
        session_start();
        flash('Logout berhasil.');
        redirect('home');
    }

    if ($action === 'add_cart') {
        require_role('buyer');
        $stmt = $pdo->prepare('INSERT INTO carts (buyer_id,product_id,qty) VALUES (?,?,1) ON DUPLICATE KEY UPDATE qty = qty + 1');
        $stmt->execute([current_user()['id'], $_POST['product_id']]);
        flash('Buku ditambahkan ke keranjang.');
        redirect('catalog');
    }

    if ($action === 'update_cart') {
        require_role('buyer');
        foreach ($_POST['qty'] ?? [] as $cartId => $qty) {
            $pdo->prepare('UPDATE carts SET qty=? WHERE id=? AND buyer_id=?')->execute([max(1, (int) $qty), $cartId, current_user()['id']]);
        }
        flash('Keranjang diperbarui.');
        redirect('cart');
    }

    if ($action === 'remove_cart') {
        require_role('buyer');
        $pdo->prepare('DELETE FROM carts WHERE id=? AND buyer_id=?')->execute([$_POST['cart_id'], current_user()['id']]);
        flash('Item dihapus.');
        redirect('cart');
    }

    if ($action === 'checkout') {
        require_role('buyer');
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT c.*, p.name, p.price, p.stock FROM carts c JOIN products p ON p.id=c.product_id WHERE c.buyer_id=? FOR UPDATE');
        $stmt->execute([current_user()['id']]);
        $items = $stmt->fetchAll();
        if (!$items) {
            $pdo->rollBack();
            flash('Keranjang masih kosong.', 'error');
            redirect('cart');
        }
        foreach ($items as $item) {
            if ((int) $item['stock'] < (int) $item['qty']) {
                $pdo->rollBack();
                flash('Stock tidak cukup untuk ' . $item['name'], 'error');
                redirect('cart');
            }
        }
        $shipping = shipping_cost($_POST['city']);
        $subtotal = array_sum(array_map(fn($item) => (int) $item['price'] * (int) $item['qty'], $items));
        $pdo->prepare('INSERT INTO shipping_addresses (buyer_id,recipient_name,phone,address,city,postal_code) VALUES (?,?,?,?,?,?)')
            ->execute([current_user()['id'], $_POST['recipient_name'], $_POST['phone'], $_POST['address'], $_POST['city'], $_POST['postal_code']]);
        $invoice = next_invoice($pdo);
        $pdo->prepare('INSERT INTO orders (buyer_id,shipping_address_id,invoice_number,total,shipping_cost) VALUES (?,?,?,?,?)')
            ->execute([current_user()['id'], $pdo->lastInsertId(), $invoice, $subtotal + $shipping, $shipping]);
        $orderId = (int) $pdo->lastInsertId();
        foreach ($items as $item) {
            $line = (int) $item['price'] * (int) $item['qty'];
            $pdo->prepare('INSERT INTO order_items (order_id,product_id,qty,price,subtotal) VALUES (?,?,?,?,?)')
                ->execute([$orderId, $item['product_id'], $item['qty'], $item['price'], $line]);
            $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id=?')->execute([$item['qty'], $item['product_id']]);
        }
        $pdo->prepare('INSERT INTO payments (order_id,method,proof) VALUES (?,?,?)')->execute([$orderId, $_POST['method'], upload_file('proof', 'payments')]);
        $pdo->prepare('DELETE FROM carts WHERE buyer_id=?')->execute([current_user()['id']]);
        notify_user($pdo, current_user()['id'], "Pesanan {$invoice} dibuat. Status pending.");
        log_activity($pdo, "Checkout invoice {$invoice}");
        $pdo->commit();
        flash("Checkout berhasil. Invoice {$invoice} dibuat otomatis.");
        redirect('tracking');
    }

    if ($action === 'save_product') {
        require_role('seller');
        $image = upload_file('image', 'products');
        $pdo->prepare('INSERT INTO products (seller_id,category_id,name,description,price,stock,image,status) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([current_user()['id'], $_POST['category_id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $image, $_POST['status']]);
        flash('Produk ditambahkan.');
        redirect('seller_products');
    }

    if ($action === 'delete_product') {
        require_role('seller');
        $pdo->prepare('DELETE FROM products WHERE id=? AND seller_id=?')->execute([$_POST['id'], current_user()['id']]);
        flash('Produk dihapus.');
        redirect('seller_products');
    }

    if ($action === 'seller_order_status') {
        require_role('seller');
        $stmt = $pdo->prepare('UPDATE orders o SET o.status=?, o.receipt_number=COALESCE(?,o.receipt_number) WHERE o.id=? AND EXISTS (SELECT 1 FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=o.id AND p.seller_id=?)');
        $stmt->execute([$_POST['status'], $_POST['receipt_number'] ?: null, $_POST['order_id'], current_user()['id']]);
        flash('Status pesanan diperbarui.');
        redirect('seller_orders');
    }

    if ($action === 'approve_seller') {
        require_role('admin');
        $pdo->prepare("UPDATE users SET status='active' WHERE id=? AND role='seller'")->execute([$_POST['seller_id']]);
        $pdo->prepare("UPDATE seller_verifications SET status='approved', approved_by=?, approved_at=NOW() WHERE seller_id=?")->execute([current_user()['id'], $_POST['seller_id']]);
        flash('Seller disetujui.');
        redirect('admin_users');
    }

    if ($action === 'ban_user') {
        require_role('admin');
        $pdo->prepare("UPDATE users SET status='banned' WHERE id=? AND role<>'admin'")->execute([$_POST['user_id']]);
        flash('User diban.');
        redirect('admin_users');
    }

    if ($action === 'save_category') {
        require_role('admin');
        $pdo->prepare('INSERT INTO categories (name,description) VALUES (?,?)')->execute([$_POST['name'], $_POST['description']]);
        flash('Kategori tersimpan.');
        redirect('admin_categories');
    }
}

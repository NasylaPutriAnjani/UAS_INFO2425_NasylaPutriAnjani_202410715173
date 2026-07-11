<?php

declare(strict_types=1);

function handle_action(PDO $pdo, ?string $action, string $page): void
{
    // ── JSON endpoint: GET index.php?action=get_product&id=X ──
    if ($action === 'get_product') {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('
            SELECT p.*, c.name category, u.name seller_name, u.avatar seller_avatar,
                   COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) as avg_rating,
                   COALESCE((SELECT COUNT(*) FROM reviews WHERE product_id=p.id), 0) as review_count,
                   COALESCE((SELECT SUM(qty) FROM order_items WHERE product_id=p.id), 0) as sold_count
            FROM products p
            JOIN categories c ON c.id = p.category_id
            JOIN users u ON u.id = p.seller_id
            WHERE p.id = ? AND p.status = "active"
        ');
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) { http_response_code(404); echo json_encode(['error' => 'Not found']); exit; }

        // Reviews breakdown
        $revStmt = $pdo->prepare('SELECT r.*, u.name buyer_name FROM reviews r JOIN users u ON u.id=r.buyer_id WHERE r.product_id=? ORDER BY r.id DESC LIMIT 5');
        $revStmt->execute([$id]);
        $reviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);

        $breakdown = [5=>0,4=>0,3=>0,2=>0,1=>0];
        foreach ($reviews as $rv) { $breakdown[(int)$rv['rating']] = ($breakdown[(int)$rv['rating']] ?? 0) + 1; }

        // Wishlist state
        $inWishlist = false;
        $cu = current_user();
        if ($cu && $cu['role'] === 'buyer') {
            $wStmt = $pdo->prepare('SELECT id FROM wishlists WHERE buyer_id=? AND product_id=?');
            $wStmt->execute([$cu['id'], $id]);
            $inWishlist = (bool)$wStmt->fetch();
        }

        header('Content-Type: application/json');
        echo json_encode([
            'product'    => $product,
            'reviews'    => $reviews,
            'breakdown'  => $breakdown,
            'in_wishlist'=> $inWishlist,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        exit;
    }

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
            $_SESSION['user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role'], 'avatar' => $user['avatar'] ?? null];
            log_activity($pdo, "Login {$user['role']}: {$user['email']}");
            redirect('home');
        }
        flash('Login gagal. Cek email/password akun.', 'error');
        redirect('home', ['auth' => 'masuk']);
    }

    if ($action === 'logout' || $page === 'logout') {
        session_destroy();
        session_start();
        redirect('home');
    }

    if ($action === 'add_cart') {
        require_role('buyer');
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        $stmt = $pdo->prepare('INSERT INTO carts (buyer_id,product_id,qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)');
        $stmt->execute([current_user()['id'], $_POST['product_id'], $qty]);
        flash('Buku ditambahkan ke keranjang.');
        $redirectPage = $_POST['redirect'] ?? 'catalog';
        redirect($redirectPage === 'checkout' ? 'checkout' : 'catalog');
    }

    if ($action === 'update_cart') {
        require_role('buyer');
        $qtyPayload = $_POST['qty'] ?? [];
        if (!is_array($qtyPayload) && isset($_POST['cart_id'])) {
            $qtyPayload = [$_POST['cart_id'] => $qtyPayload];
        }
        foreach ($qtyPayload as $cartId => $qty) {
            $qty = max(1, (int)$qty);
            $pdo->prepare('UPDATE carts SET qty=? WHERE id=? AND buyer_id=?')->execute([$qty, (int)$cartId, current_user()['id']]);
        }
        flash('Keranjang diperbarui.');
        $redirectPage = $_POST['redirect'] ?? 'cart';
        redirect($redirectPage === 'buyer_cart' ? 'buyer_cart' : 'cart');
    }

    if ($action === 'remove_cart') {
        require_role('buyer');
        $pdo->prepare('DELETE FROM carts WHERE id=? AND buyer_id=?')->execute([$_POST['cart_id'], current_user()['id']]);
        flash('Item dihapus.');
        $redirectPage = $_POST['redirect'] ?? 'cart';
        redirect($redirectPage === 'buyer_cart' ? 'buyer_cart' : 'cart');
    }

    if ($action === 'toggle_wishlist') {
        require_role('buyer');
        $userId = current_user()['id'];
        $productId = (int) $_POST['product_id'];
        
        $stmt = $pdo->prepare('SELECT id FROM wishlists WHERE buyer_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        if ($stmt->fetch()) {
            $pdo->prepare('DELETE FROM wishlists WHERE buyer_id = ? AND product_id = ?')->execute([$userId, $productId]);
            flash('Buku dihapus dari wishlist. ✅');
        } else {
            $pdo->prepare('INSERT INTO wishlists (buyer_id, product_id) VALUES (?, ?)')->execute([$userId, $productId]);
            flash('Buku ditambahkan ke wishlist. ❤️');
        }
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        redirect('buyer_wishlist');
    }

    if ($action === 'update_account') {
        require_login();
        $userId = current_user()['id'];
        
        $actionType = $_POST['action_type'] ?? 'save';
        
        if ($actionType === 'request_delete_account') {
            $pdo->prepare('UPDATE users SET delete_requested = 1 WHERE id = ?')->execute([$userId]);
            log_activity($pdo, "User #{$userId} mengirim permintaan penghapusan akun.");
            flash('Permintaan penghapusan akun telah dikirim ke Admin. ⏳');
            redirect('account_settings');
            return;
        }

        if ($actionType === 'cancel_delete_account') {
            $pdo->prepare('UPDATE users SET delete_requested = 0 WHERE id = ?')->execute([$userId]);
            log_activity($pdo, "User #{$userId} membatalkan permintaan penghapusan akun.");
            flash('Permintaan penghapusan akun dibatalkan.');
            redirect('account_settings');
            return;
        }

        if ($actionType === 'password_update') {
            $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $dbUser = $stmt->fetch();

            $currentPwd = trim($_POST['password_current'] ?? '');
            $newPwd     = trim($_POST['password'] ?? '');
            $confirmPwd = trim($_POST['password_confirm'] ?? '');

            if (empty($currentPwd) || empty($newPwd)) {
                flash('Harap isi semua kolom password.', 'error');
                redirect('account_settings');
                return;
            }
            if (!password_verify($currentPwd, $dbUser['password'])) {
                flash('Password saat ini tidak sesuai.', 'error');
                redirect('account_settings');
                return;
            }
            if (strlen($newPwd) < 8) {
                flash('Password baru minimal 8 karakter.', 'error');
                redirect('account_settings');
                return;
            }
            if ($newPwd !== $confirmPwd) {
                flash('Konfirmasi password baru tidak cocok.', 'error');
                redirect('account_settings');
                return;
            }
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($newPwd, PASSWORD_DEFAULT), $userId]);
            log_activity($pdo, "User #{$userId} mengganti password.");
            flash('Password berhasil diperbarui. ✅');
            redirect('account_settings');
            return;
        }

        // Fetch fresh current user data from DB (specifically password hash and current avatar)
        $stmt = $pdo->prepare('SELECT password, avatar FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $dbUser = $stmt->fetch();

        // 1. Basic Info Fields
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        
        if ($firstName === '' && isset($_POST['name'])) {
            $name = trim($_POST['name']);
            // Fallback parsing back to first/last name for consistency
            $nameParts = explode(' ', $name);
            $firstName = $nameParts[0] ?? '';
            $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';
        } else {
            $name = trim($firstName . ' ' . $lastName);
        }
        
        $phone = trim($_POST['phone'] ?? '');
        $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
        $alternatePhone = trim($_POST['alternate_phone'] ?? '');

        if (empty($name)) {
            flash('Nama tidak boleh kosong.', 'error');
            redirect('account_settings');
            return;
        }

        // 2. Profile Picture Upload / Delete
        $avatarPath = $dbUser['avatar'] ?? null;
        
        // Delete avatar if requested
        if (($_POST['delete_avatar'] ?? '0') === '1') {
            if ($avatarPath && file_exists(__DIR__ . '/../' . $avatarPath)) {
                @unlink(__DIR__ . '/../' . $avatarPath);
            }
            $avatarPath = null;
        }

        // Upload new avatar if provided
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = $_FILES['avatar']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
            if (in_array($fileExtension, $allowedExtensions, true)) {
                $uploadDir = __DIR__ . '/../uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Delete old avatar if uploading a new one
                if ($avatarPath && file_exists(__DIR__ . '/../' . $avatarPath)) {
                    @unlink(__DIR__ . '/../' . $avatarPath);
                }
                
                $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $avatarPath = 'uploads/avatars/' . $newFileName;
                }
            } else {
                flash('Format gambar avatar harus PNG, JPG, JPEG, atau WEBP.', 'error');
                redirect('account_settings');
                return;
            }
        }

        // Update basic info in DB
        $stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ?, dob = ?, alternate_phone = ?, avatar = ? WHERE id = ?');
        $stmt->execute([$name, $phone, $dob, $alternatePhone, $avatarPath, $userId]);
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['avatar'] = $avatarPath;

        // 3. Password Update (if entered)
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');
        $currentPassword = trim($_POST['current_password'] ?? '');

        if (!empty($password)) {
            if (empty($currentPassword)) {
                flash('Masukkan password saat ini untuk mengganti password.', 'error');
                redirect('account_settings');
                return;
            }
            if (!password_verify($currentPassword, $dbUser['password'])) {
                flash('Password saat ini salah.', 'error');
                redirect('account_settings');
                return;
            }
            if (strlen($password) < 6) {
                flash('Password baru minimal 6 karakter.', 'error');
                redirect('account_settings');
                return;
            }
            if ($password !== $confirm) {
                flash('Konfirmasi password baru tidak cocok.', 'error');
                redirect('account_settings');
                return;
            }
            
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $userId]);
        }

        log_activity($pdo, "Update akun user #{$userId}");
        flash('Pengaturan profil berhasil disimpan. ✅');
        
        // Dynamic redirect back to the originating settings page
        $redirectPage = $_GET['page'] ?? 'account_settings';
        if (!in_array($redirectPage, ['account_settings', 'buyer_account'], true)) {
            $redirectPage = 'account_settings';
        }
        redirect($redirectPage);
        return;
    }

    if ($action === 'submit_review') {
        require_role('buyer');
        $pdo->prepare('INSERT INTO reviews (buyer_id, product_id, rating, comment) VALUES (?, ?, ?, ?)')
            ->execute([current_user()['id'], $_POST['product_id'], $_POST['rating'], $_POST['comment']]);
        flash('Review berhasil dikirim. Terima kasih!');
        redirect('buyer_orders');
    }

    if ($action === 'checkout') {
        require_role('buyer');
        $phone = trim((string)($_POST['phone'] ?? ''));
        $postalCode = trim((string)($_POST['postal_code'] ?? ''));
        if ($phone === '' || !ctype_digit($phone) || $postalCode === '' || !ctype_digit($postalCode)) {
            flash('Nomor telepon dan kode pos hanya boleh berisi angka.', 'error');
            redirect('checkout');
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT c.*, p.name, p.price, p.stock, p.seller_id FROM carts c JOIN products p ON p.id=c.product_id WHERE c.buyer_id=? FOR UPDATE');
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
            ->execute([current_user()['id'], $_POST['recipient_name'], $phone, $_POST['address'], $_POST['city'], $postalCode]);
        $shippingAddressId = (int) $pdo->lastInsertId();
        $invoice = next_invoice($pdo);
        $pdo->prepare('INSERT INTO orders (buyer_id,shipping_address_id,invoice_number,total,shipping_cost,status) VALUES (?,?,?,?,?,?)')
            ->execute([current_user()['id'], $shippingAddressId, $invoice, $subtotal + $shipping, $shipping, 'paid']);
        $orderId = (int) $pdo->lastInsertId();
        foreach ($items as $item) {
            $line = (int) $item['price'] * (int) $item['qty'];
            $pdo->prepare('INSERT INTO order_items (order_id,product_id,qty,price,subtotal) VALUES (?,?,?,?,?)')
                ->execute([$orderId, $item['product_id'], $item['qty'], $item['price'], $line]);
            $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id=?')->execute([$item['qty'], $item['product_id']]);
        }
        $pdo->prepare('INSERT INTO payments (order_id,method,proof) VALUES (?,?,?)')->execute([$orderId, $_POST['method'], upload_file('proof', 'payments')]);
        $pdo->prepare('DELETE FROM carts WHERE buyer_id=?')->execute([current_user()['id']]);
        notify_user($pdo, current_user()['id'], "Pesanan {$invoice} dibuat dan bukti pembayaran terkirim. Menunggu konfirmasi penjual.");
        $sellerItems = [];
        foreach ($items as $item) {
            $sellerId = (int)($item['seller_id'] ?? 0);
            if ($sellerId <= 0) {
                continue;
            }
            $sellerItems[$sellerId][] = $item['name'] . ' x' . (int)$item['qty'];
        }
        foreach ($sellerItems as $sellerId => $names) {
            notify_user(
                $pdo,
                $sellerId,
                "Pesanan masuk {$invoice}: " . implode(', ', $names) . ". Bukti pembayaran sudah dikirim, menunggu konfirmasi."
            );
        }
        log_activity($pdo, "Checkout invoice {$invoice}");
        $pdo->commit();
        flash("Checkout berhasil. Invoice {$invoice}.");
        redirect('tracking');
    }

    if ($action === 'save_product') {
        require_role('seller');
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $image = upload_file('image', 'products');

        if ($id) {
            // Update
            if ($image) {
                $stmt = $pdo->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=?, status=? WHERE id=? AND seller_id=?');
                $stmt->execute([$_POST['category_id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $image, $_POST['status'], $id, current_user()['id']]);
            } else {
                $stmt = $pdo->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, status=? WHERE id=? AND seller_id=?');
                $stmt->execute([$_POST['category_id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['status'], $id, current_user()['id']]);
            }
            flash('Produk berhasil diperbarui.');
        } else {
            // Create
            $pdo->prepare('INSERT INTO products (seller_id,category_id,name,description,price,stock,image,status) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([current_user()['id'], $_POST['category_id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $image ?: '', $_POST['status']]);
            flash('Produk ditambahkan.');
        }
        redirect('seller_products');
    }

    if ($action === 'delete_product') {
        require_role('seller');
        $pdo->prepare('DELETE FROM products WHERE id=? AND seller_id=?')->execute([$_POST['id'], current_user()['id']]);
        flash('Produk dihapus.');
        redirect('seller_products');
    }

    if ($action === 'reply_review') {
        require_role('seller');
        $reviewId = (int)$_POST['review_id'];
        $sellerReply = trim($_POST['seller_reply']);
        
        // Verify that the review is for a product owned by this seller
        $stmt = $pdo->prepare('
            SELECT r.id 
            FROM reviews r
            JOIN products p ON p.id = r.product_id
            WHERE r.id = ? AND p.seller_id = ?
        ');
        $stmt->execute([$reviewId, current_user()['id']]);
        
        if ($stmt->fetch()) {
            $update = $pdo->prepare('UPDATE reviews SET seller_reply = ? WHERE id = ?');
            $update->execute([$sellerReply, $reviewId]);
            flash('Balasan ulasan berhasil dikirim.');
        } else {
            flash('Gagal membalas ulasan. Akses ditolak.', 'error');
        }
        redirect('seller_reviews');
    }

    if ($action === 'seller_order_status') {
        require_role('seller');
        $stmt = $pdo->prepare('UPDATE orders o SET o.status=?, o.receipt_number=COALESCE(?,o.receipt_number) WHERE o.id=? AND EXISTS (SELECT 1 FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=o.id AND p.seller_id=?)');
        $stmt->execute([$_POST['status'], $_POST['receipt_number'] ?: null, $_POST['order_id'], current_user()['id']]);
        flash('Status pesanan diperbarui.');
        redirect('seller_orders');
    }

    if ($action === 'mark_read_seller') {
        require_role('seller');
        $notifId = (int)($_POST['notification_id'] ?? 0);
        if ($notifId > 0) {
            $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?')
                ->execute([$notifId, current_user()['id']]);
        }
        redirect('seller_notifications');
    }

    if ($action === 'mark_all_read_seller') {
        require_role('seller');
        $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')
            ->execute([current_user()['id']]);
        flash('Semua notifikasi ditandai sudah dibaca.');
        redirect('seller_notifications');
    }

    if ($action === 'mark_all_read_admin') {
        require_role('admin');
        $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')
            ->execute([current_user()['id']]);
        flash('Semua notifikasi admin ditandai sudah dibaca.');
        redirect('admin_notifications');
    }

    if ($action === 'mark_all_read_buyer') {
        require_role('buyer');
        $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')
            ->execute([current_user()['id']]);
        flash('Semua notifikasi pembeli ditandai sudah dibaca.');
        redirect('buyer_notifications');
    }

    if ($action === 'approve_seller') {
        require_role('admin');
        $pdo->prepare("UPDATE users SET status='active' WHERE id=? AND role='seller'")->execute([$_POST['seller_id']]);
        $pdo->prepare("UPDATE seller_verifications SET status='approved', approved_by=?, approved_at=NOW() WHERE seller_id=?")->execute([current_user()['id'], $_POST['seller_id']]);
        flash('Seller disetujui.');
        redirect('admin_users');
    }

    if ($action === 'suspend_user') {
        require_role('admin');
        $pdo->prepare("UPDATE users SET status='suspended' WHERE id=? AND role<>'admin'")->execute([$_POST['user_id']]);
        flash('User berhasil di-suspend.');
        redirect('admin_users');
    }

    if ($action === 'activate_user') {
        require_role('admin');
        $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$_POST['user_id']]);
        flash('User berhasil diaktifkan.');
        redirect('admin_users');
    }

    if ($action === 'delete_user') {
        require_role('admin');
        $userId = $_POST['user_id'];
        
        // Delete user's avatar if exists
        $stmt = $pdo->prepare('SELECT avatar FROM users WHERE id = ? AND role<>"admin"');
        $stmt->execute([$userId]);
        $avatar = $stmt->fetchColumn();
        if ($avatar && file_exists(__DIR__ . '/../' . $avatar)) {
            @unlink(__DIR__ . '/../' . $avatar);
        }
        
        $pdo->prepare('DELETE FROM users WHERE id = ? AND role<>"admin"')->execute([$userId]);
        flash('User telah dihapus permanen.');
        redirect('admin_users');
    }
    if ($action === 'approve_delete_user') {
        require_role('admin');
        $userId = $_POST['user_id'];
        
        // Delete user's avatar if exists
        $stmt = $pdo->prepare('SELECT avatar FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $avatar = $stmt->fetchColumn();
        if ($avatar && file_exists(__DIR__ . '/../' . $avatar)) {
            @unlink(__DIR__ . '/../' . $avatar);
        }
        
        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
        flash('Permintaan hapus akun disetujui. Akun telah dihapus.');
        redirect('admin_users');
    }

    if ($action === 'reject_delete_user') {
        require_role('admin');
        $pdo->prepare('UPDATE users SET delete_requested = 0 WHERE id = ?')->execute([$_POST['user_id']]);
        flash('Permintaan hapus akun ditolak.');
        redirect('admin_users');
    }

    if ($action === 'save_category') {
        require_role('admin');
        $pdo->prepare('INSERT INTO categories (name,description) VALUES (?,?)')->execute([$_POST['name'], $_POST['description']]);
        flash('Kategori tersimpan.');
        redirect('admin_categories');
    }

    if ($action === 'delete_category') {
        require_role('admin');
        try {
            $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$_POST['category_id']]);
            flash('Kategori berhasil dihapus.');
        } catch (PDOException $e) {
            flash('Gagal menghapus kategori. Pastikan tidak ada produk yang masih menggunakan kategori ini.', 'error');
        }
        redirect('admin_categories');
    }

    if ($action === 'save_system_settings') {
        require_role('admin');

        $allowed = [
            'currency', 'timezone', 'min_order', 'ppn_rate',
            'ppn_included', 'low_stock_alert', 'low_stock_threshold', 'show_stock_display',
        ];

        $stmt = $pdo->prepare('INSERT INTO system_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

        foreach ($allowed as $key) {
            // Checkboxes send nothing when unchecked, so default to '0'
            $value = $_POST[$key] ?? '0';
            $stmt->execute([$key, trim((string)$value)]);
        }

        log_activity($pdo, 'Admin memperbarui pengaturan sistem.');
        flash('Pengaturan sistem berhasil disimpan! ✅');
        redirect('admin_settings');
    }

}

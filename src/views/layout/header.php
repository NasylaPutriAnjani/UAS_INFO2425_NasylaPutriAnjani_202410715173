<?php
$user = current_user();
$activePage = $_GET['page'] ?? 'home';
$searchQuery = (string)($_GET['q'] ?? '');
$navCounts = user_nav_counts($pdo);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RubbyBooks</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="src/assets/css/main.css?v=<?= time() ?>">
  <?php
  $roleStylesheetMap = [
    'admin'  => 'src/assets/css/admin/admin.css',
    'buyer'  => 'src/assets/css/buyer/buyer.css',
    'seller' => 'src/assets/css/seller/seller.css',
  ];
  $roleStylesheet = $roleStylesheetMap[$user['role'] ?? ''] ?? null;
  if ($roleStylesheet): ?>
    <link rel="stylesheet" href="<?= e($roleStylesheet) ?>?v=<?= time() ?>">
  <?php endif; ?>
</head>

<body>
  <header class="topbar">
    <div class="nav-brand" onclick="goHome()">
      <div class="brand-icon">📚</div>
      Rubby<span class="b2">Books<span class="dot">.</span></span>
    </div>

    <button class="mobile-menu-btn" type="button" onclick="toggleMobileMenu()" aria-label="Buka menu">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="nav-center" id="nav-center">
      <div id="nav-links">
        <button class="nav-link<?= $activePage === 'home' ? ' active' : '' ?>" onclick="goHome()">Beranda</button>
        <div class="nav-item-drop">
          <button class="nav-link<?= $activePage === 'catalog' ? ' active' : '' ?>" onclick="showPage('catalog');setActive(this)">
            Katalog <span class="arr" aria-hidden="true">▾</span>
          </button>
          <div class="nav-drop-menu">
            <a href="index.php?page=catalog" class="drop-link">Semua Kategori</a>
            <?php
            $headerCats = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 10")->fetchAll();
            foreach ($headerCats as $c):
            ?>
              <a href="index.php?page=catalog&category=<?= (int)$c['id'] ?>" class="drop-link"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <button class="nav-link" onclick="scrollToHomeSection('section-cara-kerja')">Cara Kerja</button>
        <button class="nav-link" onclick="scrollToHomeSection('section-testimoni')">Tentang Kami</button>
      </div>
    </nav>

    <div class="nav-right">
      <div class="search-box" id="nav-search">
        <svg class="s-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="text" value="<?= e($searchQuery) ?>" placeholder="Cari judul, penulis...">
      </div>

      <div id="nav-guest" <?= $user ? ' style="display:none"' : '' ?>>
        <div style="display:flex;align-items:center;gap:8px">
          <button class="btn-icon-nav" onclick="openCart()" title="Keranjang">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <?php if ($navCounts['cart'] > 0): ?><span class="cart-count" id="cart-badge"><?= (int)$navCounts['cart'] ?></span><?php endif; ?>
          </button>
          <button class="btn-cta-nav" onclick="openAuth()">Masuk / Daftar</button>
        </div>
      </div>

      <div id="nav-loggedin" <?= $user ? '' : ' style="display:none"' ?>>
        <div style="display:flex;align-items:center;gap:8px">
          <button class="btn-icon-nav" id="nav-cart-btn" onclick="openCart()" title="Keranjang"<?= !$user || $user['role'] !== 'buyer' ? ' style="display:none"' : '' ?>>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <?php if ($navCounts['cart'] > 0): ?><span class="cart-count" id="cart-badge-auth"><?= (int)$navCounts['cart'] ?></span><?php endif; ?>
          </button>
          <button class="btn-icon-nav" id="nav-bell-btn" onclick="goToNotifications()" title="Notifikasi"<?= !$user ? ' style="display:none"' : '' ?>>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <?php if ($navCounts['notifications'] > 0): ?><span class="cart-count nav-notif-count"><?= (int)$navCounts['notifications'] ?></span><?php endif; ?>
          </button>
          <div class="nav-user-wrap">
            <div class="nav-user-chip" id="nav-user-chip" onclick="toggleUserDropdown()" title="<?= $user ? e($user['name']) : 'Akun' ?>">
              <?php if ($user && !empty($user['avatar'])): ?>
                <div class="nav-user-avatar nav-user-avatar--role user-photo-avatar" id="nav-avatar-icon" data-role="<?= e($user['role'] ?? 'buyer') ?>"><img src="<?= e(asset($user['avatar'])) ?>" alt="Foto profil"></div>
              <?php else: ?>
                <div class="nav-user-avatar nav-user-avatar--role" id="nav-avatar-icon" data-role="<?= $user ? e($user['role']) : 'buyer' ?>"><?= $user ? role_icon($user['role'] ?? 'buyer') : 'A' ?></div>
              <?php endif; ?>
              <div class="nav-user-info">
                <span class="nav-user-name" id="nav-username"><?= $user ? e(role_chip_label($user)) : 'Akun Saya' ?></span>
                <span class="nav-user-role" id="nav-userrole"><?= $user ? e(role_chip_sublabel($user['role'])) : 'RubbyBooks' ?></span>
              </div>
              <div class="nav-role-dot" id="nav-role-dot"></div>
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-left:2px;opacity:.5"><polyline points="6 9 12 15 18 9" /></svg>
            </div>
            <div class="nav-user-dropdown" id="userDropdown"></div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
  <nav class="mobile-nav-panel" id="mobileNavPanel" aria-label="Menu mobile">
    <div class="mobile-nav-head">
      <span>Menu</span>
      <button type="button" class="mobile-nav-close" onclick="closeMobileMenu()" aria-label="Tutup menu">x</button>
    </div>
    <button class="mobile-nav-link<?= $activePage === 'home' ? ' active' : '' ?>" onclick="goHome()">Beranda</button>
    <a class="mobile-nav-link<?= $activePage === 'catalog' ? ' active' : '' ?>" href="index.php?page=catalog">Katalog</a>
    <div class="mobile-nav-section">
      <?php
      $mobileHeaderCats = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 10")->fetchAll();
      foreach ($mobileHeaderCats as $c):
      ?>
        <a href="index.php?page=catalog&category=<?= (int)$c['id'] ?>" class="mobile-nav-sub"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <button class="mobile-nav-link" onclick="scrollToHomeSection('section-cara-kerja');closeMobileMenu()">Cara Kerja</button>
    <button class="mobile-nav-link" onclick="scrollToHomeSection('section-testimoni');closeMobileMenu()">Tentang Kami</button>
  </nav>

  <?php require __DIR__ . '/../auth/auth_modal.php'; ?>

  <div class="overlay" id="cartOverlay" onclick="closeCart()"></div>
  <div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-head">
      <h3>Keranjang Belanja</h3>
      <button class="close-btn" onclick="closeCart()">x</button>
    </div>
    <?php
    $drawerItems = cart_items($pdo);
    $drawerSubtotal = 0;
    ?>
    <div class="cart-items">
      <?php if (empty($drawerItems)): ?>
        <div style="padding:40px 20px;text-align:center;color:#666;">Keranjang belanja masih kosong.</div>
      <?php else: ?>
        <?php foreach ($drawerItems as $di):
          $drawerSubtotal += (int)$di['price'] * (int)$di['qty'];
          $bcClass = 'bc' . (($di['id'] % 6) + 1);
          $drawerCoverStyle = !empty($di['image'])
            ? "background-image:url('" . e(asset($di['image'])) . "');background-size:cover;background-position:center;font-size:0;"
            : '';
        ?>
          <div class="cart-item">
            <div class="ci-cover <?= $bcClass ?>" style="<?= $drawerCoverStyle ?>"><?= empty($di['image']) ? e($di['name']) : '' ?></div>
            <div class="ci-info">
              <div class="ci-title"><?= e($di['name']) ?></div>
              <div class="ci-qty">
                <form action="index.php?action=update_cart" method="POST" style="display:inline-flex;align-items:center;gap:8px;">
                  <input type="hidden" name="cart_id" value="<?= (int)$di['cart_id'] ?>">
                  <button type="submit" name="qty" value="<?= (int)$di['qty'] - 1 ?>" class="qty-btn" <?= $di['qty'] <= 1 ? 'onclick="return confirm(\'Hapus item dari keranjang?\')"' : '' ?>>-</button>
                  <span class="qty-val"><?= (int)$di['qty'] ?></span>
                  <button type="submit" name="qty" value="<?= (int)$di['qty'] + 1 ?>" class="qty-btn">+</button>
                </form>
              </div>
            </div>
            <div class="ci-price"><?= rupiah($di['price']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="cart-footer">
      <div class="cart-total-row"><span>Subtotal (<?= count($drawerItems) ?> item)</span><span><?= rupiah($drawerSubtotal) ?></span></div>
      <div class="cart-total-row grand"><span>Total</span><span><?= rupiah($drawerSubtotal) ?></span></div>
      <?php if (!empty($drawerItems)): ?>
        <a href="index.php?page=checkout" class="btn-checkout" style="display:block;text-align:center;text-decoration:none;">Lanjut ke Checkout</a>
      <?php else: ?>
        <button class="btn-checkout" style="opacity:0.5;cursor:not-allowed;" disabled>Lanjut ke Checkout</button>
      <?php endif; ?>
    </div>
  </div>

  <div class="toast" id="toast">
    <span class="toast-icon">OK</span>
    <span id="toast-msg">Berhasil!</span>
  </div>

  <?php if ($flash = take_flash()): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
          showToast(<?= json_encode($flash['message'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
        }, 100);
      });
    </script>
  <?php endif; ?>

  <main class="rb-main">

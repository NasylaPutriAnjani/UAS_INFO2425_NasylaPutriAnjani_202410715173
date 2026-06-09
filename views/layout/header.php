<?php $user = current_user(); $activePage = $_GET['page'] ?? 'home'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RubbyBooks</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="<?= $user && $user['role'] !== 'buyer' ? 'theme-' . e($user['role']) : '' ?>">
<header class="topbar">
  <div class="nav-brand" onclick="showPage('home')">
    <div class="brand-icon">📚</div>
    Rubby<span class="b2">Books<span class="dot">.</span></span>
  </div>

  <nav class="nav-center" id="nav-center">
    <!-- Buyer nav links (default) -->
    <div id="nav-links-buyer">
      <button class="nav-link active" onclick="showPage('home');setActive(this)">Beranda</button>
      <button class="nav-link" onclick="showPage('catalog');setActive(this)">Katalog</button>
      <button class="nav-link" onclick="showPage('checkout');setActive(this)">Checkout</button>
      <button class="nav-link" onclick="showPage('tracking');setActive(this)">Lacak Pesanan</button>
    </div>
    <!-- Seller nav links (shown when seller logged in) -->
    <div id="nav-links-seller" style="display:none">
      <button class="nav-link" onclick="_showPageDirect('seller');setActive(this)">📊 Dashboard</button>
      <button class="nav-link" onclick="_showPageDirect('seller');setActive(this)">📦 Produk</button>
      <button class="nav-link" onclick="showToast('📋 Pesanan Masuk');setActive(this)">📋 Pesanan</button>
      <button class="nav-link" onclick="showToast('📈 Laporan Penjualan');setActive(this)">📈 Laporan</button>
      <button class="nav-link" onclick="showToast('⚙️ Pengaturan Toko');setActive(this)">⚙️ Toko</button>
    </div>
    <!-- Admin nav links (shown when admin logged in) -->
    <div id="nav-links-admin" style="display:none">
      <button class="nav-link" onclick="_showPageDirect('admin');setActive(this)">🖥️ Panel Admin</button>
      <button class="nav-link" onclick="showToast('👥 Manajemen User');setActive(this)">👥 Users</button>
      <button class="nav-link" onclick="showToast('🏪 Manajemen Toko');setActive(this)">🏪 Toko</button>
      <button class="nav-link" onclick="showToast('📊 Laporan Platform');setActive(this)">📊 Laporan</button>
      <button class="nav-link" onclick="showToast('🛡️ Keamanan');setActive(this)">🛡️ Keamanan</button>
    </div>
  </nav>

  <div class="nav-right">
    <!-- Search: visible for buyer/guest only -->
    <div class="search-box" id="nav-search">
      <svg class="s-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="Cari judul, penulis…">
    </div>
    <!-- Guest state -->
    <div id="nav-guest">
      <div style="display:flex;align-items:center;gap:10px">
        <button class="btn-cart-nav" onclick="openCart()">
          🛒 Keranjang
          <span class="cart-count" id="cart-badge">3</span>
        </button>
        <button class="btn-cta-nav" onclick="openAuth()">Masuk / Daftar</button>
      </div>
    </div>
    <!-- Logged-in state — single role chip, cart only for buyer -->
    <div id="nav-loggedin" style="display:none">
      <div style="display:flex;align-items:center;gap:10px">
        <!-- Cart: buyer only -->
        <button class="btn-cart-nav" id="nav-cart-btn" onclick="openCart()" style="display:none">
          🛒 Keranjang
          <span class="cart-count" id="cart-badge-auth">3</span>
        </button>
        <!-- Single active role chip -->
        <div class="nav-user-wrap">
          <div class="nav-user-chip" id="nav-user-chip" onclick="toggleUserDropdown()">
            <div class="nav-user-avatar" id="nav-avatar-initials">SR</div>
            <div class="nav-user-info">
              <span class="nav-user-name" id="nav-username">Akun Saya</span>
              <span class="nav-user-role" id="nav-userrole">RubbyBooks</span>
            </div>
            <div class="nav-role-dot" id="nav-role-dot"></div>
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-left:2px;opacity:.5"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <!-- Dynamic dropdown — populated by JS per role -->
          <div class="nav-user-dropdown" id="userDropdown"></div>
        </div>
      </div>
    </div>
  </div>
</header>
<?php require __DIR__ . '/../auth/auth_modal.php'; ?>

<div class="overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-drawer-head">
    <h3>🛒 Keranjang Belanja</h3>
    <button class="close-btn" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-items">
    <div class="cart-item">
      <div class="ci-cover bc1">Atomic Habits</div>
      <div class="ci-info">
        <div class="ci-title">Atomic Habits</div>
        <div class="ci-author">James Clear</div>
        <div class="ci-qty">
          <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
          <span class="qty-val">1</span>
          <button class="qty-btn" onclick="changeQty(this,1)">+</button>
        </div>
      </div>
      <div class="ci-price">Rp 89.000</div>
    </div>
    <div class="cart-item">
      <div class="ci-cover bc2">Laskar Pelangi</div>
      <div class="ci-info">
        <div class="ci-title">Laskar Pelangi</div>
        <div class="ci-author">Andrea Hirata</div>
        <div class="ci-qty">
          <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
          <span class="qty-val">2</span>
          <button class="qty-btn" onclick="changeQty(this,1)">+</button>
        </div>
      </div>
      <div class="ci-price">Rp 130.000</div>
    </div>
    <div class="cart-item">
      <div class="ci-cover bc3">Midnight Library</div>
      <div class="ci-info">
        <div class="ci-title">The Midnight Library</div>
        <div class="ci-author">Matt Haig</div>
        <div class="ci-qty">
          <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
          <span class="qty-val">1</span>
          <button class="qty-btn" onclick="changeQty(this,1)">+</button>
        </div>
      </div>
      <div class="ci-price">Rp 76.000</div>
    </div>
  </div>
  <div class="cart-footer">
    <div class="cart-total-row"><span>Subtotal (4 item)</span><span>Rp 295.000</span></div>
    <div class="cart-total-row"><span>Ongkos Kirim</span><span>Rp 18.000</span></div>
    <div class="cart-total-row"><span>Diskon</span><span class="discount-val">−Rp 18.000</span></div>
    <div class="cart-total-row grand"><span>Total</span><span>Rp 295.000</span></div>
    <button class="btn-checkout" onclick="closeCart();showPage('checkout')">Lanjut ke Checkout →</button>
  </div>
</div>

<div class="toast" id="toast">
  <span class="toast-icon">✅</span>
  <span id="toast-msg">Berhasil!</span>
</div>

<?php if ($flash = take_flash()): ?>
    <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
<main class="rb-main">
<?php
$buyerMenu = $buyerMenu ?? 'buyer';
$user = $user ?? current_user();
$sidebar = $buyerSidebar ?? ['cartCount' => 0, 'unreadNotifications' => 0];
$firstName = explode(' ', trim($user['name'] ?? 'Pembeli'))[0];
?>
<aside class="dash-sidebar buyer-sidebar">
  <div class="sidebar-store-profile">
    <?= user_avatar_html($user, 'sidebar-store-avatar', 'B') ?>
    <div>
      <div class="sidebar-store-name"><?= e($firstName) ?></div>
      <div class="sidebar-store-status">Pembeli Aktif</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-group">
      <div class="sidebar-group-label">Menu Pembeli</div>
      
      <a href="index.php?page=buyer" class="sidebar-item<?= $buyerMenu === 'buyer' ? ' active' : '' ?>">
        <span class="si">🏠</span> Beranda
      </a>
      
      <a href="index.php?page=buyer_wishlist" class="sidebar-item<?= $buyerMenu === 'wishlist' ? ' active' : '' ?>">
        <span class="si">❤️</span> Wishlist
      </a>
      
      <a href="index.php?page=buyer_cart" class="sidebar-item<?= $buyerMenu === 'cart' ? ' active' : '' ?>">
        <span class="si">🛒</span> Keranjang
        <?php if ($sidebar['cartCount'] > 0): ?>
          <span class="sidebar-badge"><?= (int)$sidebar['cartCount'] ?></span>
        <?php endif; ?>
      </a>
      
      <a href="index.php?page=buyer_orders" class="sidebar-item<?= $buyerMenu === 'orders' ? ' active' : '' ?>">
        <span class="si">📦</span> Pesanan Saya
      </a>
      
      <a href="index.php?page=buyer_reviews" class="sidebar-item<?= $buyerMenu === 'reviews' ? ' active' : '' ?>">
        <span class="si">⭐</span> Review Saya
      </a>
      
      <a href="index.php?page=buyer_notifications" class="sidebar-item<?= $buyerMenu === 'notifications' ? ' active' : '' ?>">
        <span class="si">🔔</span> Notifikasi
        <?php if ($sidebar['unreadNotifications'] > 0): ?>
          <span class="sidebar-badge warn"><?= (int)$sidebar['unreadNotifications'] ?></span>
        <?php endif; ?>
      </a>
    </div>

    <div class="sidebar-group">
      <div class="sidebar-group-label">Pengaturan</div>
      <a href="index.php?page=account_settings" class="sidebar-item<?= $buyerMenu === 'account_settings' ? ' active' : '' ?>">
        <span class="si">⚙️</span> Pengaturan Akun
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <button type="button" class="sidebar-item" onclick="doLogout()" style="color:#dc2626;width:100%">
      <span class="si">🚪</span> Keluar
    </button>
  </div>
</aside>

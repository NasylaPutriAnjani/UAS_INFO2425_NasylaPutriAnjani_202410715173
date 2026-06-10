<?php
$buyerMenu = $buyerMenu ?? 'buyer';
$menuItems = buyer_menu_items();
$user = current_user();
$sidebar = $buyerSidebar ?? ['cartCount' => 0, 'unreadNotifications' => 0];
$firstName = explode(' ', trim($user['name'] ?? 'Pembeli'))[0];
?>
<aside class="dash-sidebar buyer-sidebar">
  <div class="sidebar-store-profile">
    <div class="sidebar-store-avatar"><?= role_icon('buyer') ?></div>
    <div>
      <div class="sidebar-store-name"><?= e($firstName) ?></div>
      <div class="sidebar-store-status">Pembeli Aktif</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-group">
      <div class="sidebar-group-label">Menu Pembeli</div>
      <?php foreach ($menuItems as $key => $item): ?>
        <?php
        $isActive = $buyerMenu === $key;
        $href = 'index.php?page=' . urlencode($item['page']);
        $badge = null;
        if ($key === 'cart' && $sidebar['cartCount'] > 0) {
            $badge = $sidebar['cartCount'];
        }
        if ($key === 'notifications' && $sidebar['unreadNotifications'] > 0) {
            $badge = $sidebar['unreadNotifications'];
        }
        ?>
        <a href="<?= e($href) ?>" class="sidebar-item<?= $isActive ? ' active' : '' ?>">
          <span class="si"><?= $item['icon'] ?></span>
          <?= e($item['label']) ?>
          <?php if ($badge): ?>
            <span class="sidebar-badge<?= $key === 'notifications' ? ' warn' : '' ?>"><?= (int) $badge ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>

  <div class="sidebar-footer">
    <button type="button" class="sidebar-item" onclick="doLogout()" style="color:#dc2626;width:100%">
      <span class="si">🚪</span> Keluar
    </button>
  </div>
</aside>

<?php
/**
 * Admin sidebar partial – include in every admin view.
 * Set $adminActivePage before requiring this file.
 * e.g.  $adminActivePage = 'admin_users';
 */
$adminActivePage = $adminActivePage ?? $_GET['page'] ?? 'admin';
$adminMenuGroups = [
  'Overview' => [
    ['page' => 'admin', 'icon' => '📊', 'label' => 'Dashboard'],
    ['page' => 'admin_analytics', 'icon' => '📈', 'label' => 'Analitik'],
  ],
  'Manajemen' => [
    ['page' => 'admin_users', 'icon' => '👥', 'label' => 'Kelola User'],
    ['page' => 'admin_categories', 'icon' => '🏷️', 'label' => 'Kelola Kategori'],
    ['page' => 'admin_products', 'icon' => '📚', 'label' => 'Kelola Produk'],
    ['page' => 'admin_orders', 'icon' => '🛒', 'label' => 'Semua Pesanan'],
  ],
  'Sistem' => [
    ['page' => 'account_settings', 'icon' => '⚙️', 'label' => 'Pengaturan Akun'],
    ['page' => 'admin_settings', 'icon' => '🛠️', 'label' => 'Pengaturan Sistem'],
  ],
];
?>
<aside class="dash-sidebar admin-sidebar">
  <div class="sidebar-store-profile">
    <div class="sidebar-store-avatar" style="background:linear-gradient(135deg,var(--accent),var(--accent-deep));font-size:18px">🖥️</div>
    <div>
      <div class="sidebar-store-name">Control Center</div>
      <div class="sidebar-store-status">Super Admin · v2.0</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php foreach ($adminMenuGroups as $groupLabel => $items): ?>
      <div class="sidebar-group">
        <div class="sidebar-group-label"><?= e($groupLabel) ?></div>
        <?php foreach ($items as $item): ?>
          <a
            href="index.php?page=<?= e($item['page']) ?>"
            class="sidebar-item<?= $adminActivePage === $item['page'] ? ' active' : '' ?>"
          >
            <span class="si"><?= $item['icon'] ?></span>
            <span class="sidebar-text"><?= e($item['label']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-footer">
    <button type="button" class="sidebar-item" onclick="doLogout()"><span class="si">🚪</span><span class="sidebar-text">Keluar</span></button>
  </div>
</aside>

<div id="page-admin_notifications" class="page active admin-page">
  <div class="dash-layout admin-layout">

    <!-- SIDEBAR -->
    <?php $adminActivePage = 'admin_notifications'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>🔔 Notifikasi Admin</h2>
          <p>Pembaruan sistem dan aktivitas platform</p>
        </div>
        <div class="dash-topbar-right">
          <?php if (!empty($notifications)): ?>
          <form method="POST" action="index.php?page=admin_notifications">
            <input type="hidden" name="action" value="mark_all_read_admin">
            <button type="submit" class="btn-admin-ghost">
              <span>✅</span> Tandai Semua Dibaca
            </button>
          </form>
          <?php endif; ?>
        </div>
      </div>
      <div class="dash-body">
        <div style="display:flex; flex-direction:column; gap:16px; max-width: 800px;">
          <?php if (empty($notifications)): ?>
            <div style="text-align:center; padding:60px 20px; background:#fff; border:1px solid var(--border-soft); border-radius:16px;">
              <div style="font-size:48px; margin-bottom:16px;">🔔</div>
              <p style="font-size:14px; color:var(--ink-muted);">Tidak ada notifikasi sistem saat ini.</p>
            </div>
          <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:12px;">
              <?php foreach ($notifications as $notif): 
                $isRead = (int)($notif['is_read'] ?? 0) === 1;
                $createdAt = !empty($notif['created_at']) ? date('d M Y, H:i', strtotime($notif['created_at'])) : '-';
                $msg = $notif['message'] ?? '';
                
                // Classify icon/color based on message keywords
                $icon = '🔔';
                $colorClass = 'blue';
                $borderClass = '';
                if (stripos($msg, 'verifikasi') !== false || stripos($msg, 'seller') !== false) {
                  $icon = '🏪';
                  $colorClass = 'orange';
                } elseif (stripos($msg, 'produk') !== false || stripos($msg, 'buku') !== false) {
                  $icon = '📦';
                  $colorClass = 'green';
                } elseif (stripos($msg, 'bayar') !== false || stripos($msg, 'payment') !== false || stripos($msg, 'transaksi') !== false) {
                  $icon = '💳';
                  $colorClass = 'green-light';
                } elseif (stripos($msg, 'error') !== false || stripos($msg, 'gagal') !== false || stripos($msg, 'penting') !== false) {
                  $icon = '🚨';
                  $colorClass = 'red';
                  $borderClass = ' red-highlight';
                }
              ?>
                <div class="sys-notif-item<?= $borderClass ?>" style="<?= $isRead ? 'opacity: 0.75;' : '' ?>">
                  <div class="sys-notif-icon-wrap <?= $colorClass ?>"><?= $icon ?></div>
                  <div class="sys-notif-content">
                    <div class="sys-notif-meta">
                      <span class="sys-notif-title"><?= $isRead ? 'Notifikasi Terbaca' : 'Notifikasi Baru' ?></span>
                      <span class="sys-notif-time">🕒 <?= $createdAt ?></span>
                    </div>
                    <div class="sys-notif-text"><?= e($msg) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

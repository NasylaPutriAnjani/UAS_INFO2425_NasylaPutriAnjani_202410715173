<?php $buyerMenu = 'notifications'; ?>
<div id="page-buyer-notifications" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>🔔 Notifikasi</h2>
          <p>Pembaruan pesanan dan info penting untukmu</p>
        </div>
      </div>
      <div class="dash-body">
        <div class="buyer-panel">
          <?php if (empty($notifications)): ?>
            <div class="buyer-empty">
              <div class="buyer-empty-icon">🔔</div>
              <p>Tidak ada notifikasi saat ini.</p>
            </div>
          <?php else: ?>
            <div class="buyer-notif-list">
              <?php foreach ($notifications as $notif): ?>
                <div class="buyer-notif-item<?= !$notif['is_read'] ? ' unread' : '' ?>">
                  <div class="buyer-notif-dot"></div>
                  <div>
                    <p><?= e($notif['message']) ?></p>
                    <span><?= e(date('d M Y, H:i', strtotime($notif['created_at']))) ?></span>
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

<div id="page-seller_notifications" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <?php
    $currentSellerPage = $_GET['page'] ?? 'seller';
    $sellerIdForSidebar = current_user()['id'];
    $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
    $unreadNotifCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM notifications WHERE user_id = $sellerIdForSidebar AND is_read = 0")->fetchColumn();
    ?>
    <aside class="dash-sidebar seller-sidebar">
      <div class="sidebar-store-profile">
        <div class="sidebar-store-avatar">🏪</div>
        <div>
          <div class="sidebar-store-name"><?= e(current_user()['name'] ?? 'Penjual') ?></div>
          <div class="sidebar-store-status">Toko Aktif</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="sidebar-group">
          <div class="sidebar-group-label">Menu Utama</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller' ? ' active' : '' ?>" onclick="showPage('seller')">
            <span class="si">📊</span> Dashboard
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_products' ? ' active' : '' ?>" onclick="showPage('seller_products')">
            <span class="si">📦</span> Produk Saya
            <span class="sidebar-badge"><?= $activeProductsCount ?></span>
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_orders' ? ' active' : '' ?>" onclick="showPage('seller_orders')">
            <span class="si">🛒</span> Pesanan Masuk
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_reviews' ? ' active' : '' ?>" onclick="showPage('seller_reviews')">
            <span class="si">💬</span> Ulasan & Rating
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_notifications' ? ' active' : '' ?>" onclick="showPage('seller_notifications')">
            <span class="si">🔔</span> Notifikasi
            <?php if ($unreadNotifCount > 0): ?>
              <span class="sidebar-badge"><?= $unreadNotifCount ?></span>
            <?php endif; ?>
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Keuangan</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_report' ? ' active' : '' ?>" onclick="showPage('seller_report')">
            <span class="si">💰</span> Laporan Penjualan
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Pengaturan</div>
          <button class="sidebar-item" onclick="showPage('account_settings')">
            <span class="si">⚙️</span> Pengaturan Akun
          </button>
        </div>
      </nav>

      <div class="sidebar-footer">
        <button class="sidebar-item" onclick="doLogout()" style="color:#dc2626;width:100%">
          <span class="si">🚪</span> Keluar
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="dash-content">
      <!-- Topbar -->
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>Notifikasi</h2>
          <p>Pembaruan pesanan dan informasi penting untuk toko Anda</p>
        </div>
        <div class="dash-topbar-right">
          <?php if (!empty($notifications)): ?>
          <form method="POST" action="index.php?page=seller_notifications">
            <input type="hidden" name="action" value="mark_all_read_seller">
            <button type="submit" class="btn-dash-ghost">
              <span>✅</span> Tandai Semua Dibaca
            </button>
          </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">

        <?php if (empty($notifications)): ?>
          <!-- Empty State -->
          <div class="notif-empty-state">
            <div class="notif-empty-icon">🔔</div>
            <div class="notif-empty-title">Tidak Ada Notifikasi</div>
            <div class="notif-empty-desc">Semua notifikasi terkait pesanan, ulasan, dan aktivitas toko akan muncul di sini.</div>
            <button class="btn-dash-primary" onclick="showPage('seller')">
              Kembali ke Dashboard
            </button>
          </div>

        <?php else: ?>

          <!-- Unread count summary -->
          <?php if ($unreadNotifCount > 0): ?>
          <div class="notif-summary-bar">
            <span class="notif-summary-dot"></span>
            <span><b><?= $unreadNotifCount ?> notifikasi belum dibaca</b> — klik notifikasi untuk menandai sudah dibaca.</span>
          </div>
          <?php endif; ?>

          <!-- Notifications List -->
          <div class="notif-list-container">
            <?php foreach ($notifications as $notif):
              $isRead = (int)($notif['is_read'] ?? 0) === 1;
              $createdAt = !empty($notif['created_at']) ? date('d M Y, H:i', strtotime($notif['created_at'])) : '-';
              $msg = $notif['message'] ?? '';

              // Auto-detect icon from message content
              $icon = '🔔';
              if (stripos($msg, 'pesanan') !== false || stripos($msg, 'order') !== false) $icon = '📦';
              elseif (stripos($msg, 'ulasan') !== false || stripos($msg, 'review') !== false) $icon = '💬';
              elseif (stripos($msg, 'bayar') !== false || stripos($msg, 'payment') !== false) $icon = '💳';
              elseif (stripos($msg, 'kirim') !== false || stripos($msg, 'resi') !== false) $icon = '🚚';
              elseif (stripos($msg, 'selesai') !== false || stripos($msg, 'delivered') !== false) $icon = '✅';
              elseif (stripos($msg, 'batal') !== false || stripos($msg, 'cancel') !== false) $icon = '❌';
              elseif (stripos($msg, 'stok') !== false || stripos($msg, 'stock') !== false) $icon = '⚠️';
            ?>
            <div class="notif-item<?= $isRead ? ' notif-read' : ' notif-unread' ?>">
              <div class="notif-icon-wrap"><?= $icon ?></div>
              <div class="notif-content">
                <div class="notif-msg"><?= e($msg) ?></div>
                <div class="notif-time">🕒 <?= $createdAt ?></div>
              </div>
              <div class="notif-actions">
                <?php if (!$isRead): ?>
                <form method="POST" action="index.php?page=seller_notifications" style="display:inline;">
                  <input type="hidden" name="action" value="mark_read_seller">
                  <input type="hidden" name="notification_id" value="<?= $notif['id'] ?>">
                  <button type="submit" class="notif-read-btn" title="Tandai sudah dibaca">✓</button>
                </form>
                <?php else: ?>
                  <span class="notif-read-label">Dibaca</span>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<style>
/* Empty State */
.notif-empty-state {
  background: #fff;
  border: 1.5px dashed var(--border);
  border-radius: 20px;
  padding: 60px 32px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}
.notif-empty-icon {
  font-size: 52px;
  line-height: 1;
  margin-bottom: 4px;
}
.notif-empty-title {
  font-family: var(--font-serif);
  font-size: 22px;
  font-weight: 700;
  color: var(--ink);
}
.notif-empty-desc {
  font-size: 14px;
  color: #64748b;
  max-width: 360px;
  line-height: 1.6;
}

/* Summary Bar */
.notif-summary-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 12px;
  padding: 12px 18px;
  margin-bottom: 16px;
  font-size: 13.5px;
  color: #1e40af;
}
.notif-summary-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #3b82f6;
  flex-shrink: 0;
  animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(1.3); }
}

/* Notification List */
.notif-list-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.notif-item {
  background: #fff;
  border: 1px solid var(--border-soft);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.notif-item:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,.06);
  transform: translateY(-1px);
}
.notif-item.notif-unread {
  border-left: 4px solid var(--accent);
  background: #fdf4ff;
}
.notif-item.notif-read {
  opacity: 0.75;
}

.notif-icon-wrap {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.notif-unread .notif-icon-wrap {
  background: #fae8ff;
}

.notif-content {
  flex: 1;
  min-width: 0;
}
.notif-msg {
  font-size: 13.5px;
  color: var(--ink);
  font-weight: 500;
  line-height: 1.5;
}
.notif-time {
  font-size: 11.5px;
  color: #94a3b8;
  margin-top: 4px;
}

.notif-actions {
  flex-shrink: 0;
  display: flex;
  align-items: center;
}
.notif-read-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: #f0fdf4;
  border: 1.5px solid #bbf7d0;
  color: #16a34a;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.notif-read-btn:hover {
  background: #16a34a;
  color: #fff;
  border-color: #16a34a;
  transform: scale(1.1);
}
.notif-read-label {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 600;
}
</style>
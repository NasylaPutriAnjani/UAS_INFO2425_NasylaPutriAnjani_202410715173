<div id="page-admin_notifications" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <aside class="dash-sidebar admin-sidebar">
      <div class="sidebar-store-profile">
        <div class="sidebar-store-avatar" style="background:linear-gradient(135deg,var(--accent),var(--accent-deep));font-size:18px">🖥️</div>
        <div>
          <div class="sidebar-store-name">Control Center</div>
          <div class="sidebar-store-status">Super Admin · v2.0</div>
        </div>
      </div>

      <nav class="sidebar-nav" style="flex:1">
        <div class="sidebar-group">
          <div class="sidebar-group-label">Overview</div>
          <button class="sidebar-item" onclick="showPage('admin')">
            <span class="si">📊</span> Dashboard
          </button>
          <button class="sidebar-item" onclick="showToast('📈 Halaman Analitik')">
            <span class="si">📈</span> Analitik
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Manajemen</div>
          <button class="sidebar-item" onclick="showPage('admin_users')">
            <span class="si">👥</span> Kelola User
          </button>
          <button class="sidebar-item" onclick="showPage('admin_categories')">
            <span class="si">🏷️</span> Kelola Kategori
          </button>
          <button class="sidebar-item" onclick="showToast('📚 Kelola Produk')">
            <span class="si">📚</span> Kelola Produk
          </button>
          <button class="sidebar-item" onclick="showToast('🛒 Semua Pesanan')">
            <span class="si">🛒</span> Semua Pesanan
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Sistem</div>
          <button class="sidebar-item" onclick="showPage('account_settings')">
            <span class="si">⚙️</span> Pengaturan Akun
          </button>
        </div>
      </nav>

      <div class="sidebar-footer">
        <button class="sidebar-item" onclick="doLogout()" style="width:100%">
          <span class="si">🚪</span> Keluar
        </button>
      </div>
    </aside>

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
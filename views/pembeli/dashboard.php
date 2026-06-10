<?php $buyerMenu = 'buyer'; ?>
<div id="page-buyer" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>Beranda Pembeli</h2>
          <p>Selamat datang kembali, <b><?= e(explode(' ', current_user()['name'])[0]) ?></b> 👋</p>
        </div>
        <div class="dash-topbar-right">
          <a href="index.php?page=catalog" class="btn-dash-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Jelajahi Buku
          </a>
        </div>
      </div>

      <div class="dash-body">
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:var(--accent-blush)">📦</div>
            </div>
            <div class="metric-val"><?= (int) ($stats['orders_total'] ?? 0) ?></div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-sub"><?= (int) ($stats['orders_active'] ?? 0) ?> pesanan aktif</div>
          </div>
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#eff6ff">🛒</div>
            </div>
            <div class="metric-val"><?= (int) ($stats['cart_items'] ?? 0) ?></div>
            <div class="metric-label">Item di Keranjang</div>
            <div class="metric-sub"><a href="index.php?page=buyer_cart" style="color:var(--accent);font-weight:600">Lihat keranjang →</a></div>
          </div>
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fff7ed">⭐</div>
            </div>
            <div class="metric-val"><?= (int) ($stats['reviews_total'] ?? 0) ?></div>
            <div class="metric-label">Review Diberikan</div>
            <div class="metric-sub">Bagikan pengalaman membaca</div>
          </div>
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fef9c3">🔔</div>
              <?php if (($buyerSidebar['unreadNotifications'] ?? 0) > 0): ?>
                <div class="metric-trend trend-neutral"><?= (int) $buyerSidebar['unreadNotifications'] ?> baru</div>
              <?php endif; ?>
            </div>
            <div class="metric-val"><?= (int) ($buyerSidebar['unreadNotifications'] ?? 0) ?></div>
            <div class="metric-label">Notifikasi Belum Dibaca</div>
            <div class="metric-sub"><a href="index.php?page=buyer_notifications" style="color:var(--accent);font-weight:600">Buka notifikasi →</a></div>
          </div>
        </div>

        <div class="buyer-panels-row">
          <div class="buyer-panel">
            <div class="buyer-panel-head">
              <h4>📦 Pesanan Terbaru</h4>
              <a href="index.php?page=buyer_orders" class="buyer-panel-link">Lihat semua</a>
            </div>
            <?php if (empty($recentOrders)): ?>
              <div class="buyer-empty">
                <div class="buyer-empty-icon">📭</div>
                <p>Belum ada pesanan. Yuk mulai belanja buku favoritmu!</p>
                <a href="index.php?page=catalog" class="btn-dash-primary">Ke Katalog</a>
              </div>
            <?php else: ?>
              <div class="buyer-order-list">
                <?php foreach ($recentOrders as $order): ?>
                  <div class="buyer-order-row">
                    <div>
                      <div class="buyer-order-id"><?= e($order['invoice_number']) ?></div>
                      <div class="buyer-order-date"><?= e(date('d M Y', strtotime($order['created_at']))) ?></div>
                    </div>
                    <div class="buyer-order-status"><?= e(order_status_label($order['status'])) ?></div>
                    <div class="buyer-order-total"><?= rupiah((int) $order['total']) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="buyer-panel">
            <div class="buyer-panel-head">
              <h4>⚡ Akses Cepat</h4>
            </div>
            <div class="buyer-quick-grid">
              <a href="index.php?page=catalog" class="buyer-quick-card">
                <span>📚</span>
                <strong>Katalog Buku</strong>
                <small>Jelajahi koleksi</small>
              </a>
              <a href="index.php?page=buyer_wishlist" class="buyer-quick-card">
                <span>❤️</span>
                <strong>Wishlist</strong>
                <small>Buku tersimpan</small>
              </a>
              <a href="index.php?page=buyer_cart" class="buyer-quick-card">
                <span>🛒</span>
                <strong>Keranjang</strong>
                <small><?= (int) ($stats['cart_items'] ?? 0) ?> item</small>
              </a>
              <a href="index.php?page=buyer_account" class="buyer-quick-card">
                <span>👤</span>
                <strong>Akun Saya</strong>
                <small>Pengaturan profil</small>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

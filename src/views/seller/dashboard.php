<div id="page-seller" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <?php
    $currentSellerPage = $_GET['page'] ?? 'seller';
    $sellerIdForSidebar = current_user()['id'];
    $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
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
          <h2>Dashboard Penjual</h2>
          <p>Selamat datang kembali, <b><?= e(current_user()['name']) ?></b> 👋 — <?= date('l, d F Y') ?></p>
        </div>
        <div class="dash-topbar-right">
          <button class="btn-dash-primary" onclick="showPage('seller_products')">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Produk
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">

        <!-- Alert strip -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="alert-strip">
          <span class="alert-strip-icon">⚠️</span>
          <div class="alert-strip-text"><b><?= count($lowStockProducts) ?> produk</b> hampir kehabisan stok. Segera lakukan restock agar tidak kehilangan pembeli.</div>
          <button class="alert-strip-action" onclick="showPage('seller_products')">Lihat Produk →</button>
        </div>
        <?php endif; ?>

        <!-- Metrics -->
        <div class="metrics-grid">
          <!-- Revenue -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#f0fdf4">💰</div>
              <div class="metric-trend trend-up">-</div>
            </div>
            <div class="metric-val">Rp <?= number_format((float)$revenue, 0, ',', '.') ?></div>
            <div class="metric-label">Total Pendapatan</div>
            <div class="metric-sub">sepanjang waktu</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:100%;background:linear-gradient(90deg,var(--accent),var(--accent-mid))"></div></div>
          </div>
          <!-- Orders -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#eff6ff">📦</div>
              <div class="metric-trend trend-up">-</div>
            </div>
            <div class="metric-val"><?= (int)$totalOrders ?></div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-sub">sepanjang waktu</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:100%;background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div></div>
          </div>
          <!-- Products -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fdf4ff">📚</div>
              <div class="metric-trend trend-neutral"><?= count($lowStockProducts) ?> ⚠️</div>
            </div>
            <div class="metric-val"><?= (int)$activeProductsCount ?></div>
            <div class="metric-label">Produk Aktif</div>
            <div class="metric-sub"><?= count($lowStockProducts) ?> stok hampir habis</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:100%;background:linear-gradient(90deg,#a855f7,#c084fc)"></div></div>
          </div>
          <!-- Rating -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fffbeb">⭐</div>
              <div class="metric-trend trend-up">-</div>
            </div>
            <div class="metric-val"><?= number_format((float)$ratingData['avg_rating'], 1) ?></div>
            <div class="metric-label">Rating Toko</div>
            <div class="metric-sub">dari <?= (int)$ratingData['total_reviews'] ?> ulasan pembeli</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:<?= min(100, (float)$ratingData['avg_rating'] * 20) ?>%;background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div></div>
          </div>
        </div>

        <!-- Charts row -->
        <div class="charts-row">
          <!-- Bar chart -->
          <div class="chart-card">
            <div class="chart-card-head">
              <div>
                <h4>Grafik Penjualan</h4>
                <p>Pendapatan 6 bulan terakhir</p>
              </div>
              <div class="chart-tabs">
                <button class="chart-tab active">Bulan</button>
              </div>
            </div>
            <div class="bar-chart-wrap">
              <?php
                $maxSales = 0;
                foreach ($monthlySales as $m) {
                    if ($m['val'] > $maxSales) $maxSales = $m['val'];
                }
                $gridMax = $maxSales > 0 ? ceil($maxSales / 1000000) * 1000000 : 5000000;
              ?>
              <div class="bar-chart-grid">
                <div class="bar-grid-line"><span class="bar-grid-label"><?= $gridMax / 1000000 ?>jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label"><?= ($gridMax * 0.75) / 1000000 ?>jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label"><?= ($gridMax * 0.5) / 1000000 ?>jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label"><?= ($gridMax * 0.25) / 1000000 ?>jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label">0</span></div>
              </div>
              <div class="bar-chart-bars">
                <?php foreach ($monthlySales as $idx => $m): 
                  $pct = $gridMax > 0 ? ($m['val'] / $gridMax) * 100 : 0;
                  $pct = max(2, min(100, $pct));
                  $isCurrent = ($idx === count($monthlySales) - 1);
                ?>
                <div class="bar-item <?= $isCurrent ? 'bar-current' : '' ?>">
                  <div class="bar-fill" style="height:<?= $pct ?>%;background:<?= $isCurrent ? 'linear-gradient(180deg, var(--accent-mid), var(--accent))' : '#e2e8f0' ?>">
                    <div class="bar-tooltip"><?= $m['label'] ?>: Rp <?= number_format($m['val'], 0, ',', '.') ?></div>
                  </div>
                  <span class="bar-label" <?= $isCurrent ? 'style="color:var(--accent);font-weight:700"' : '' ?>><?= $m['label'] ?></span>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="revenue-summary">
              <div class="rev-item">
                <div class="rev-dot" style="background:var(--accent)"></div>
                <div>
                  <div class="rev-label">Bulan Ini</div>
                  <div class="rev-val">Rp <?= number_format(end($monthlySales)['val'], 0, ',', '.') ?></div>
                </div>
              </div>
              <div class="rev-item">
                <div class="rev-dot" style="background:#e2e8f0"></div>
                <div>
                  <div class="rev-label">Rata-rata</div>
                  <div class="rev-val">Rp <?= number_format($totalSales6Months / 6, 0, ',', '.') ?></div>
                </div>
              </div>
              <div class="rev-item" style="margin-left:auto">
                <div>
                  <div class="rev-label">Total 6 Bulan</div>
                  <div class="rev-val" style="color:var(--accent)">Rp <?= number_format($totalSales6Months, 0, ',', '.') ?></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Donut chart -->
          <div class="chart-card">
            <div class="chart-card-head">
              <div>
                <h4>Kategori Terlaris</h4>
                <p>Distribusi penjualan produk</p>
              </div>
            </div>
            <div class="donut-wrap">
              <div style="position:relative;display:inline-block">
                <svg width="130" height="130" viewBox="0 0 130 130">
                  <circle cx="65" cy="65" r="50" fill="none" stroke="#f1f5f9" stroke-width="18"/>
                  <?php
                    $colors = ['var(--accent)', '#86efac', '#fbbf24', '#e2e8f0'];
                    $totalProducts = array_sum(array_column($categoryDistribution, 'product_count'));
                    $circumference = 2 * M_PI * 50; // ~314.159
                    $currentOffset = 0;
                    
                    foreach ($categoryDistribution as $index => $cat):
                        if ($totalProducts == 0) break;
                        $percentage = $cat['product_count'] / $totalProducts;
                        $dashLength = $percentage * $circumference;
                        $gapLength = $circumference - $dashLength;
                        $dasharray = "$dashLength $gapLength";
                        $dashoffset = -$currentOffset;
                        $currentOffset += $dashLength;
                  ?>
                  <circle cx="65" cy="65" r="50" fill="none" stroke="<?= $colors[$index % 4] ?>" stroke-width="18"
                    stroke-dasharray="<?= $dasharray ?>" stroke-dashoffset="<?= $dashoffset ?>" stroke-linecap="round"
                    transform="rotate(-90 65 65)"/>
                  <?php endforeach; ?>
                  <text x="65" y="60" text-anchor="middle" fill="#1e293b" font-size="18" font-weight="700" font-family="Playfair Display, serif"><?= $totalProducts ?></text>
                  <text x="65" y="75" text-anchor="middle" fill="#94a3b8" font-size="9.5" font-family="Plus Jakarta Sans, sans-serif">produk</text>
                </svg>
              </div>
              <div class="donut-legend-grid">
                <?php foreach ($categoryDistribution as $index => $cat): 
                  $percentage = $totalProducts > 0 ? round(($cat['product_count'] / $totalProducts) * 100) : 0;
                ?>
                <div class="donut-legend-item">
                  <div class="donut-legend-dot" style="background:<?= $colors[$index % 4] ?>"></div>
                  <div class="donut-legend-info">
                    <div class="dn"><?= e(strlen($cat['name']) > 12 ? substr($cat['name'], 0, 10) . '..' : $cat['name']) ?></div>
                    <div class="dv"><?= $percentage ?>%</div>
                  </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($categoryDistribution)): ?>
                  <div style="font-size: 12px; color: #94a3b8;">Belum ada data produk</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom row: Orders + Quick Actions -->
        <div class="bottom-row">
          <!-- Orders table -->
          <div class="orders-card">
            <div class="orders-card-head">
              <div>
                <h4>Pesanan Terbaru</h4>
              </div>
              <a href="#" onclick="event.preventDefault();showPage('seller_orders')">Lihat semua →</a>
            </div>
            <table class="data-table">
              <thead>
                <tr>
                  <th>No. Pesanan</th>
                  <th>Pembeli</th>
                  <th>Buku</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentOrders)): ?>
                <tr>
                  <td colspan="6" class="text-center">Belum ada pesanan terbaru.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($recentOrders as $ro): ?>
                <tr>
                  <td><span class="td-order"><?= e($ro['invoice_number']) ?></span></td>
                  <td>
                    <div class="td-buyer">
                      <div class="td-buyer-avatar"><?= strtoupper(substr($ro['buyer_name'], 0, 2)) ?></div>
                      <span class="td-buyer-name"><?= e($ro['buyer_name']) ?></span>
                    </div>
                  </td>
                  <td><span class="td-book"><?= e($ro['product_names']) ?></span></td>
                  <td><span class="td-total">Rp <?= number_format((float)$ro['seller_total'], 0, ',', '.') ?></span></td>
                  <td>
                    <?php
                    $statusMap = [
                        'pending' => ['Menunggu', 's-pending'],
                        'paid' => ['Dibayar', 's-paid'],
                        'processing' => ['Diproses', 's-paid'],
                        'shipped' => ['Dikirim', 's-ship'],
                        'delivered' => ['Selesai', 's-done'],
                        'cancelled' => ['Batal', 's-cancel']
                    ];
                    $s = $statusMap[$ro['status']] ?? ['Unknown', 's-pending'];
                    ?>
                    <span class="status <?= $s[1] ?>">● <?= $s[0] ?></span>
                  </td>
                  <td><button class="action-btn" onclick="showPage('seller_orders')">Detail</button></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Quick Actions -->
          <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="quick-panel">
              <div class="quick-panel-head">
                <h4>Aksi Cepat</h4>
              </div>
              <div class="quick-actions-list">
                <button class="quick-action-item" onclick="showPage('seller_products')">
                  <div class="qa-icon" style="background:#f0fdf4">📦</div>
                  <div>
                    <div class="qa-title">Tambah Produk</div>
                    <div class="qa-desc">Upload buku baru ke toko</div>
                  </div>
                  <span class="qa-arrow">›</span>
                </button>


                <button class="quick-action-item" onclick="showPage('seller_reviews')">
                  <div class="qa-icon" style="background:#fdf4ff">💬</div>
                  <div>
                    <div class="qa-title">Balas Ulasan</div>
                    <div class="qa-desc">Lihat ulasan terbaru</div>
                  </div>
                  <span class="qa-arrow">›</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Low stock -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="lowstock-card">
          <div class="lowstock-card-head">
            <h4>⚠️ Stok Rendah</h4>
            <a href="#" style="font-size:12px;color:var(--accent);font-weight:600" onclick="event.preventDefault();showPage('seller_products')">Kelola produk →</a>
          </div>
          <?php foreach ($lowStockProducts as $ls): ?>
          <div class="lowstock-row">
            <div class="ls-cover bc1"><?= strtoupper(substr($ls['name'], 0, 2)) ?></div>
            <div class="ls-info">
              <div class="ls-title"><?= e($ls['name']) ?></div>
              <div class="ls-cat">Rp <?= number_format((float)$ls['price'], 0, ',', '.') ?></div>
            </div>
            <div class="ls-stock-indicator">
              <div class="ls-stock-bar"><div class="ls-stock-bar-fill" style="width:<?= min(100, (int)$ls['stock'] * 10) ?>%;background:<?= $ls['stock'] <= 3 ? '#ef4444' : '#f59e0b' ?>"></div></div>
              <div class="ls-stock-num" <?= $ls['stock'] > 3 ? 'style="background:#fff7ed;color:#d97706"' : '' ?>><?= (int)$ls['stock'] ?></div>
              <button class="action-btn warn" onclick="showPage('seller_products')">+ Restock</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>
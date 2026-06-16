<?php
// Fallbacks for IDE static analysis
$period = $period ?? 'week';
$compareLabel = $compareLabel ?? 'vs minggu lalu';
$kpi = $kpi ?? [
    'revenue' => 0,
    'revenue_trend' => 0,
    'orders' => 0,
    'orders_trend' => 0,
    'books' => 0,
    'books_trend' => 0,
];
$chartData = $chartData ?? [];
$bestSellers = $bestSellers ?? [];
?>
<div id="page-seller_report" class="page active">
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
          <h2>Laporan Penjualan</h2>
          <p>Pantau perkembangan bisnis toko Anda secara berkala</p>
        </div>
        <div class="dash-topbar-right">
          <a href="index.php?page=seller_report&period=<?= e($period) ?>&export=csv" class="btn-dash-ghost">
            <span style="font-size: 14px; margin-right: 4px;">📥</span> Unduh Laporan
          </a>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">
        
        <!-- Period Switcher -->
        <div class="period-switcher-row">
          <a href="index.php?page=seller_report&period=week" class="period-tab <?= $period === 'week' ? 'active' : '' ?>">Minggu Ini</a>
          <a href="index.php?page=seller_report&period=month" class="period-tab <?= $period === 'month' ? 'active' : '' ?>">Bulan Ini</a>
          <a href="index.php?page=seller_report&period=year" class="period-tab <?= $period === 'year' ? 'active' : '' ?>">Tahun Ini</a>
        </div>

        <!-- KPI Cards Grid -->
        <div class="metrics-grid">
          <!-- Total Revenue Card -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fdf2f8; color:#db2777;">$</div>
              <?php 
              $revTrend = $kpi['revenue_trend'];
              $revClass = $revTrend >= 0 ? 'trend-up' : 'trend-down';
              $revArrow = $revTrend >= 0 ? '▲' : '▼';
              ?>
              <div class="metric-trend <?= $revClass ?>"><?= $revArrow ?> <?= abs($revTrend) ?>%</div>
            </div>
            <div class="metric-val" style="font-size: 24px;">Rp <?= number_format($kpi['revenue'], 0, ',', '.') ?></div>
            <div class="metric-label">Total Pendapatan</div>
            <div class="metric-sub" style="color: <?= $revTrend >= 0 ? '#16a34a' : '#dc2626' ?>; font-weight: 600;">
              <?= $revTrend >= 0 ? '▲ +' : '▼ -' ?><?= abs($revTrend) ?>% <?= e($compareLabel) ?>
            </div>
          </div>

          <!-- Total Orders Card -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#eff6ff; color:#2563eb;">🛍️</div>
              <?php 
              $ordTrend = $kpi['orders_trend'];
              $ordClass = $ordTrend >= 0 ? 'trend-up' : 'trend-down';
              $ordArrow = $ordTrend >= 0 ? '▲' : '▼';
              ?>
              <div class="metric-trend <?= $ordClass ?>"><?= $ordArrow ?> <?= abs($ordTrend) ?></div>
            </div>
            <div class="metric-val"><?= $kpi['orders'] ?> pesanan</div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-sub" style="color: <?= $ordTrend >= 0 ? '#16a34a' : '#dc2626' ?>; font-weight: 600;">
              <?= $ordTrend >= 0 ? '▲ +' : '▼ -' ?><?= abs($ordTrend) ?> vs periode lalu
            </div>
          </div>

          <!-- Books Sold Card -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#f5f3ff; color:#7c3aed;">📚</div>
              <?php 
              $bookTrend = $kpi['books_trend'];
              $bookClass = $bookTrend >= 0 ? 'trend-up' : 'trend-down';
              $bookArrow = $bookTrend >= 0 ? '▲' : '▼';
              ?>
              <div class="metric-trend <?= $bookClass ?>"><?= $bookArrow ?> <?= abs($bookTrend) ?></div>
            </div>
            <div class="metric-val"><?= $kpi['books'] ?> buku</div>
            <div class="metric-label">Buku Terjual</div>
            <div class="metric-sub" style="color: <?= $bookTrend >= 0 ? '#16a34a' : '#dc2626' ?>; font-weight: 600;">
              <?= $bookTrend >= 0 ? '▲ +' : '▼ -' ?><?= abs($bookTrend) ?> vs periode lalu
            </div>
          </div>
        </div>

        <!-- ── CHART CARD (Dynamic SVG Graph) ── -->
        <div class="chart-big-card">
          <div class="chart-big-head">
            <div>
              <h3>Tren Pendapatan &amp; Pesanan</h3>
              <p>Visualisasi tren penjualan Anda untuk periode terpilih</p>
            </div>
            <div class="chart-legend-row">
              <div class="legend-item"><span class="legend-dot income"></span> Pendapatan</div>
              <div class="legend-item"><span class="legend-dot orders"></span> Jumlah Pesanan</div>
              <a href="index.php?page=seller_report&period=<?= e($period) ?>&export=csv" class="btn-csv-export">📥 Unduh CSV</a>
            </div>
          </div>

          <?php
          // Processing Graph math
          $countPoints = count($chartData);
          $maxRev = 10000; // base minimum
          $maxOrd = 5;     // base minimum
          
          foreach ($chartData as $dp) {
              if ($dp['revenue'] > $maxRev) $maxRev = $dp['revenue'];
              if ($dp['orders'] > $maxOrd) $maxOrd = $dp['orders'];
          }
          
          // Y-axis steps
          $yRevMax = ceil($maxRev / 100000) * 100000;
          $yOrdMax = ceil($maxOrd / 5) * 5;
          if ($yRevMax == 0) $yRevMax = 500000;
          if ($yOrdMax == 0) $yOrdMax = 10;

          // Chart Dimensions
          $w = 900;
          $h = 320;
          $paddingLeft = 70;
          $paddingRight = 50;
          $paddingTop = 30;
          $paddingBottom = 40;

          $chartW = $w - $paddingLeft - $paddingRight;
          $chartH = $h - $paddingTop - $paddingBottom;

          // Compute points coords
          $points = [];
          for ($i = 0; $i < $countPoints; $i++) {
              $dp = $chartData[$i];
              // X coord spacing
              if ($countPoints > 1) {
                  $x = $paddingLeft + ($i * ($chartW / ($countPoints - 1)));
              } else {
                  $x = $paddingLeft + ($chartW / 2);
              }
              
              // Y coord for Revenue (left axis)
              $yRev = $paddingTop + $chartH - (($dp['revenue'] / $yRevMax) * $chartH);
              // Y coord for Orders (right axis)
              $yOrd = $paddingTop + $chartH - (($dp['orders'] / $yOrdMax) * $chartH);
              // Bottom Y baseline for rendering bar/area
              $yBase = $paddingTop + $chartH;

              $points[] = [
                  'x' => $x,
                  'yRev' => $yRev,
                  'yOrd' => $yOrd,
                  'yBase' => $yBase,
                  'label' => $dp['label'],
                  'revenue' => $dp['revenue'],
                  'orders' => $dp['orders']
              ];
          }

          // Build SVG Line Path & Area Path for Revenue
          $linePath = '';
          $areaPath = '';
          if ($countPoints > 0) {
              $linePath = "M " . $points[0]['x'] . " " . $points[0]['yRev'];
              $areaPath = "M " . $points[0]['x'] . " " . $points[0]['yBase'] . " L " . $points[0]['x'] . " " . $points[0]['yRev'];
              
              // Curve drawing (Bezier approximation or straight lines)
              // For straight line aesthetic matching original dashboard:
              for ($i = 1; $i < $countPoints; $i++) {
                  $linePath .= " L " . $points[$i]['x'] . " " . $points[$i]['yRev'];
                  $areaPath .= " L " . $points[$i]['x'] . " " . $points[$i]['yRev'];
              }
              $areaPath .= " L " . $points[$countPoints - 1]['x'] . " " . $points[$countPoints - 1]['yBase'] . " Z";
          }
          ?>

          <div class="svg-container">
            <svg viewBox="0 0 <?= $w ?> <?= $h ?>" width="100%" height="100%" style="overflow: visible;">
              <defs>
                <!-- Area Gradient -->
                <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#db2777" stop-opacity="0.18" />
                  <stop offset="100%" stop-color="#db2777" stop-opacity="0.00" />
                </linearGradient>
                
                <!-- Bar Gradient -->
                <linearGradient id="barGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#93c5fd" stop-opacity="0.8" />
                  <stop offset="100%" stop-color="#eff6ff" stop-opacity="0.3" />
                </linearGradient>
              </defs>

              <!-- Grid Lines -->
              <?php for ($grid = 0; $grid <= 4; $grid++): 
                  $yGrid = $paddingTop + ($chartH * ($grid / 4));
                  $valRev = $yRevMax * (1 - ($grid / 4));
                  $valOrd = $yOrdMax * (1 - ($grid / 4));
              ?>
                <!-- Horizontal grid line -->
                <line x1="<?= $paddingLeft ?>" y1="<?= $yGrid ?>" x2="<?= $w - $paddingRight ?>" y2="<?= $yGrid ?>" stroke="#f1f5f9" stroke-width="1.5" />
                
                <!-- Left Y Axis Labels (Revenue in Thousands/Millions) -->
                <text x="<?= $paddingLeft - 12 ?>" y="<?= $yGrid + 4 ?>" font-size="10.5" fill="#94a3b8" text-anchor="end" font-weight="600">
                  <?= $valRev >= 1000000 ? 'Rp ' . ($valRev / 1000000) . 'M' : ($valRev >= 1000 ? 'Rp ' . ($valRev / 1000) . 'K' : 'Rp ' . $valRev) ?>
                </text>

                <!-- Right Y Axis Labels (Orders count) -->
                <text x="<?= $w - $paddingRight + 12 ?>" y="<?= $yGrid + 4 ?>" font-size="10.5" fill="#3b82f6" text-anchor="start" font-weight="700">
                  <?= round($valOrd) ?>
                </text>
              <?php endfor; ?>

              <!-- X Axis Line -->
              <line x1="<?= $paddingLeft ?>" y1="<?= $paddingTop + $chartH ?>" x2="<?= $w - $paddingRight ?>" y2="<?= $paddingTop + $chartH ?>" stroke="#e2e8f0" stroke-width="1.5" />

              <!-- Draw Bars for Orders -->
              <?php 
              $barW = min(36, $chartW / ($countPoints * 1.8)); // adaptive width
              foreach ($points as $pt): 
                  $barH = $pt['yBase'] - $pt['yOrd'];
                  $bx = $pt['x'] - ($barW / 2);
                  $by = $pt['yOrd'];
              ?>
                <rect x="<?= $bx ?>" y="<?= $by ?>" width="<?= $barW ?>" height="<?= max(2, $barH) ?>" rx="4" fill="url(#barGrad)" stroke="#3b82f6" stroke-width="1.5" style="transition: all 0.3s;" />
              <?php endforeach; ?>

              <!-- Draw Revenue Area Fill -->
              <?php if (!empty($areaPath)): ?>
                <path d="<?= $areaPath ?>" fill="url(#areaGrad)" />
              <?php endif; ?>

              <!-- Draw Revenue Line Chart -->
              <?php if (!empty($linePath)): ?>
                <path d="<?= $linePath ?>" fill="none" stroke="#db2777" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
              <?php endif; ?>

              <!-- Interactive Dots & Tooltip Triggers -->
              <?php foreach ($points as $idx => $pt): ?>
                <!-- Order point indicator dot (blue outline) -->
                <circle cx="<?= $pt['x'] ?>" cy="<?= $pt['yOrd'] ?>" r="4" fill="#fff" stroke="#3b82f6" stroke-width="2" />

                <!-- Revenue point indicator dot (vibrant pink) -->
                <circle cx="<?= $pt['x'] ?>" cy="<?= $pt['yRev'] ?>" r="6" fill="#db2777" stroke="#fff" stroke-width="2.5" class="chart-rev-dot" />

                <!-- Hover trigger area -->
                <rect x="<?= $pt['x'] - 16 ?>" y="<?= $paddingTop ?>" width="32" height="<?= $chartH ?>" fill="transparent" style="cursor: pointer;" class="graph-trigger" data-label="<?= e($pt['label']) ?>" data-rev="Rp <?= number_format($pt['revenue'], 0, ',', '.') ?>" data-ord="<?= $pt['orders'] ?> pesanan" />
                
                <!-- X Axis Labels -->
                <text x="<?= $pt['x'] ?>" y="<?= $paddingTop + $chartH + 20 ?>" font-size="11" fill="#64748b" text-anchor="middle" font-weight="600"><?= e($pt['label']) ?></text>
              <?php endforeach; ?>
            </svg>

            <!-- Embedded Tooltip box -->
            <div id="chart-tooltip" class="chart-tooltip-box" style="display: none;">
              <div class="tooltip-label" id="tt-label">Minggu</div>
              <div class="tooltip-row">
                <span class="tt-dot income"></span>
                <span>Pendapatan: <strong id="tt-rev">Rp 0</strong></span>
              </div>
              <div class="tooltip-row">
                <span class="tt-dot orders"></span>
                <span>Pesanan: <strong id="tt-ord">0</strong></span>
              </div>
            </div>
          </div>
        </div>

        <!-- ── BEST SELLERS TABLE ── -->
        <div class="orders-card" style="margin-top: 24px;">
          <div class="orders-card-head" style="padding: 20px;">
            <div>
              <h4 style="font-family: var(--font-serif); font-size: 16px;">Produk Terlaris</h4>
              <p style="font-size: 12px; color: #94a3b8; font-weight: normal; margin-top: 2px;">Berdasarkan jumlah unit buku yang berhasil terjual</p>
            </div>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th style="width: 60px; text-align: center;">#</th>
                <th>Judul Buku</th>
                <th>Kategori</th>
                <th style="text-align: center;">Terjual</th>
                <th style="text-align: right; padding-right: 24px;">Pendapatan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($bestSellers)): ?>
              <tr>
                <td colspan="5" class="text-center" style="padding: 40px 20px; color: #94a3b8;">Belum ada penjualan produk dalam periode ini.</td>
              </tr>
              <?php else: ?>
              <?php 
              $rank = 1;
              foreach ($bestSellers as $b): 
                  $bcClass = 'bc' . (($b['id'] % 6) + 1);
              ?>
              <tr>
                <td style="text-align: center; font-weight: 800; color: #94a3b8;"><?= $rank++ ?></td>
                <td>
                  <div class="product-info-cell">
                    <div class="product-cell-cover-placeholder <?= $bcClass ?>" style="width: 32px; height: 42px; font-size: 8px;">
                      <?= strtoupper(substr($b['product_name'], 0, 2)) ?>
                    </div>
                    <div class="product-cell-details">
                      <span class="product-cell-title" style="font-size: 13px;"><?= e($b['product_name']) ?></span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="status-badge s-aktif" style="background:#f1f5f9; color:#475569; border: 1px solid #e2e8f0;">
                    <?= e($b['category_name']) ?>
                  </span>
                </td>
                <td style="text-align: center; font-weight: 700; color: var(--ink);"><?= (int)$b['sold_qty'] ?> buku</td>
                <td style="text-align: right; padding-right: 24px; font-weight: 800; color: #db2777; font-family: var(--font-serif);">
                  Rp <?= number_format($b['total_rev'], 0, ',', '.') ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
/* ── Period Switcher ── */
.period-switcher-row {
  display: inline-flex;
  background: #f1f5f9;
  border-radius: 12px;
  padding: 4px;
  margin-bottom: 24px;
  border: 1px solid var(--border-soft);
}
.period-tab {
  padding: 8px 16px;
  font-size: 12.5px;
  font-weight: 700;
  color: #64748b;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
}
.period-tab:hover {
  color: var(--ink);
}
.period-tab.active {
  background: #fff;
  color: var(--ink);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

/* ── KPI trend colors override ── */
.metric-trend.trend-up {
  background: #ecfdf5;
  color: #059669;
}
.metric-trend.trend-down {
  background: #fef2f2;
  color: #dc2626;
}

/* ── Big Chart Card ── */
.chart-big-card {
  background: #fff;
  border: 1px solid var(--border-soft);
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.chart-big-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 14px;
}
.chart-big-head h3 {
  font-family: var(--font-serif);
  font-size: 18px;
  color: var(--ink);
}
.chart-big-head p {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}
.chart-legend-row {
  display: flex;
  align-items: center;
  gap: 20px;
  font-size: 12px;
  font-weight: 700;
  color: var(--ink-mid);
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}
.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 3px;
}
.legend-dot.income { background: #db2777; }
.legend-dot.orders { background: #3b82f6; }
.btn-csv-export {
  background: #f8fafc;
  border: 1px solid var(--border);
  padding: 6px 12px;
  border-radius: 8px;
  color: var(--ink-mid);
  text-decoration: none;
  font-weight: 700;
  font-size: 11.5px;
  transition: all 0.2s;
}
.btn-csv-export:hover {
  border-color: var(--accent-light);
  background: var(--accent-blush);
  color: var(--accent);
}

/* ── SVG Graph ── */
.svg-container {
  position: relative;
  width: 100%;
}
.chart-rev-dot {
  cursor: pointer;
  transition: transform 0.2s;
}
.chart-rev-dot:hover {
  transform: scale(1.4);
}
.graph-trigger:hover ~ .chart-rev-dot {
  transform: scale(1.3);
}

/* ── Tooltip Box ── */
.chart-tooltip-box {
  position: absolute;
  background: rgba(15, 23, 42, 0.95);
  border-radius: 10px;
  padding: 10px 14px;
  color: #fff;
  font-size: 11.5px;
  pointer-events: none;
  z-index: 50;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
  min-width: 150px;
  transform: translate(-50%, -110%);
  transition: opacity 0.15s, left 0.1s, top 0.1s;
}
.tooltip-label {
  font-weight: 800;
  border-bottom: 1px solid rgba(255,255,255,0.15);
  padding-bottom: 6px;
  margin-bottom: 6px;
  font-size: 11px;
  color: #94a3b8;
  text-transform: uppercase;
}
.tooltip-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}
.tt-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.tt-dot.income { background: #db2777; }
.tt-dot.orders { background: #3b82f6; }
</style>

<script>
// Graph Interactive Tooltip Handler
document.addEventListener('DOMContentLoaded', () => {
  const triggers = document.querySelectorAll('.graph-trigger');
  const tooltip = document.getElementById('chart-tooltip');
  const ttLabel = document.getElementById('tt-label');
  const ttRev = document.getElementById('tt-rev');
  const ttOrd = document.getElementById('tt-ord');
  const svgContainer = document.querySelector('.svg-container');

  if (triggers.length > 0 && tooltip && svgContainer) {
    triggers.forEach(trigger => {
      trigger.addEventListener('mousemove', (e) => {
        const rect = svgContainer.getBoundingClientRect();
        // Calculate relative coordinate inside container
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Populate details
        ttLabel.textContent = trigger.getAttribute('data-label');
        ttRev.textContent = trigger.getAttribute('data-rev');
        ttOrd.textContent = trigger.getAttribute('data-ord');

        // Position tooltip
        tooltip.style.left = x + 'px';
        tooltip.style.top = y + 'px';
        tooltip.style.display = 'block';
      });

      trigger.addEventListener('mouseleave', () => {
        tooltip.style.display = 'none';
      });
    });
  }
});
</script>

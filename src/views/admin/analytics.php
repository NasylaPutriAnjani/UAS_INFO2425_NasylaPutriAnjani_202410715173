<?php
$fmt = fn(int $n): string => $n >= 1000000
    ? 'Rp ' . number_format($n/1000000, 1) . 'jt'
    : ($n >= 1000 ? 'Rp ' . number_format($n/1000, 0) . 'k' : 'Rp ' . number_format($n, 0, ',', '.'));

$pctBadge = function(float $pct): string {
    $up    = $pct >= 0;
    $bg    = $up ? '#dcfce7' : '#fee2e2';
    $clr   = $up ? '#166534' : '#991b1b';
    $arrow = $up ? '↗' : '↘';
    return "<span style=\"background:{$bg};color:{$clr};font-size:11px;font-weight:700;padding:4px 8px;border-radius:20px;\">{$arrow} " . abs($pct) . "%</span>";
};

$barTotals = array_column($chartData, 'total');
$barMax    = max($barTotals ?: [1]);
$catColors = ['#ec4899','#3b82f6','#eab308','#10b981','#8b5cf6'];
$avatarBg  = ['#1f2937','#be185d','#0369a1','#047857','#7c3aed','#b45309'];

// Build filter URL helper
$buildUrl = fn(array $params): string => 'index.php?' . http_build_query(array_merge(
    ['page' => 'admin_analytics', 'period' => $period],
    $params
));
?>
<div id="page-admin_analytics" class="page active admin-page">
  <div class="dash-layout admin-layout">

    <!-- SIDEBAR -->
    <?php $adminActivePage = 'admin_analytics'; require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="dash-content admin-analytics-content" style="background:#f9fafb;padding:24px;overflow-y:auto;">

      <!-- ── TOPBAR ── -->
      <div class="admin-analytics-head" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:22px;font-weight:700;color:#111827;display:flex;align-items:center;gap:10px;">
            <span style="background:var(--accent);color:#fff;padding:6px 8px;border-radius:8px;font-size:16px;">📊</span>
            Analitik Performa & Penjualan
          </h2>
          <p style="color:#6b7280;font-size:13px;margin-top:4px;">Statistik mendalam untuk platform RubbyBooks · Per <?= date('d F Y') ?></p>
        </div>

        <!-- ── Period filter + Export ── -->
        <div class="admin-analytics-actions" style="display:flex;align-items:center;gap:10px;">
          <!-- Period dropdown -->
          <div style="position:relative;">
            <button onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'"
                    style="display:flex;align-items:center;gap:8px;padding:9px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;color:#374151;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
              📅 <?= htmlspecialchars($periodLabel) ?> <span style="font-size:10px;color:#9ca3af;">▾</span>
            </button>
            <div style="display:none;position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.1);min-width:180px;z-index:999;overflow:hidden;">
              <?php foreach (['daily'=>'Daily (Minggu Ini)','30days'=>'Last 30 Days','1year'=>'Last 1 Year','all'=>'All Time'] as $val=>$label): ?>
              <a href="<?= $buildUrl(['period'=>$val]) ?>"
                 style="display:block;padding:11px 16px;font-size:13px;color:<?= $period===$val?'var(--accent)':'#374151' ?>;font-weight:<?= $period===$val?'700':'400' ?>;text-decoration:none;background:<?= $period===$val?'#fdf2f8':'transparent' ?>;">
                <?= $period===$val?'✓ ':'' ?><?= $label ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Export -->
          <a href="<?= $buildUrl(['period' => $period, 'export' => 'csv']) ?>" class="admin-analytics-export" style="display:flex;align-items:center;gap:7px;padding:9px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;color:#374151;font-size:13px;font-weight:500;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.05);text-decoration:none;">
            Export CSV
          </a>
          <button onclick="showToast('📥 Fitur export akan segera tersedia!')" style="display:flex;align-items:center;gap:7px;padding:9px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;color:#374151;font-size:13px;font-weight:500;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            📥 Export
          </button>
        </div>
      </div>

      <!-- ── KPI CARDS ── -->
      <div class="admin-analytics-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">

        <div style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.07);position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#ec4899,#be185d);"></div>
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <div style="width:38px;height:38px;border-radius:50%;background:#fce7f3;color:#be185d;display:flex;align-items:center;justify-content:center;font-size:18px;">💵</div>
            <?= $pctBadge($revTrend) ?>
          </div>
          <div style="font-size:12px;color:#6b7280;font-weight:500;margin-bottom:4px;">Total Revenue</div>
          <div style="font-size:22px;font-weight:700;color:#111827;"><?= $fmt($totalRevenue) ?></div>
          <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Periode: <?= htmlspecialchars($periodLabel) ?></div>
        </div>

        <div style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.07);position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#3b82f6,#1d4ed8);"></div>
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <div style="width:38px;height:38px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:18px;">🛒</div>
            <?= $pctBadge($orderTrend) ?>
          </div>
          <div style="font-size:12px;color:#6b7280;font-weight:500;margin-bottom:4px;">Avg. Order Value</div>
          <div style="font-size:22px;font-weight:700;color:#111827;"><?= $fmt($avgOrderVal) ?></div>
          <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Pesanan bulan ini: <?= number_format($ordersThisMonth) ?></div>
        </div>

        <?php $convRate = $activeUsers > 0 ? round($ordersThisMonth / $activeUsers * 100, 1) : 0; ?>
        <div style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.07);position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#eab308,#a16207);"></div>
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <div style="width:38px;height:38px;border-radius:50%;background:#fef3c7;color:#a16207;display:flex;align-items:center;justify-content:center;font-size:18px;">📊</div>
            <span style="background:#dbeafe;color:#1d4ed8;font-size:11px;font-weight:700;padding:4px 8px;border-radius:20px;">Live</span>
          </div>
          <div style="font-size:12px;color:#6b7280;font-weight:500;margin-bottom:4px;">Conversion Rate</div>
          <div style="font-size:22px;font-weight:700;color:#111827;"><?= $convRate ?>%</div>
          <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Order / active user</div>
        </div>

        <div style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.07);position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#10b981,#047857);"></div>
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <div style="width:38px;height:38px;border-radius:50%;background:#d1fae5;color:#047857;display:flex;align-items:center;justify-content:center;font-size:18px;">👥</div>
            <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:700;padding:4px 8px;border-radius:20px;">Aktif</span>
          </div>
          <div style="font-size:12px;color:#6b7280;font-weight:500;margin-bottom:4px;">Active Users</div>
          <div style="font-size:22px;font-weight:700;color:#111827;"><?= number_format($activeUsers) ?></div>
          <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Status active</div>
        </div>

      </div>

      <!-- ── TREND CHART ── -->
      <div class="admin-analytics-card" style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.07);margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
          <div>
            <h3 style="font-size:16px;font-weight:700;color:#111827;margin-bottom:2px;">Tren Penjualan & Pendapatan</h3>
            <p style="font-size:12px;color:#6b7280;">Perbandingan penjualan dalam periode <?= htmlspecialchars($periodLabel) ?></p>
          </div>
          <span style="font-size:12px;color:#9ca3af;">📊 <?= htmlspecialchars($periodLabel) ?></span>
        </div>

        <!-- Bar chart -->
        <?php if (!empty($chartData)): ?>
        <div style="height:190px;display:flex;align-items:flex-end;gap:<?= count($chartData) > 20 ? '3px' : '8px' ?>;position:relative;padding-top:24px;">
          <div style="position:absolute;top:0;left:0;right:0;border-bottom:1px dashed #f3f4f6;"></div>
          <div style="position:absolute;top:50%;left:0;right:0;border-bottom:1px dashed #f3f4f6;"></div>
          <div style="position:absolute;bottom:0;left:0;right:0;border-bottom:2px solid #e5e7eb;"></div>
          <?php foreach ($chartData as $i => $bar):
            $pct = $barMax > 0 ? max(4, round($bar['total'] / $barMax * 100)) : 4;
            $isLast = $i === count($chartData) - 1;
            $barCol = $bar['total'] > 0 ? ($isLast ? 'var(--accent-deep)' : 'var(--accent)') : '#e5e7eb';
          ?>
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;height:100%;justify-content:flex-end;min-width:0;">
            <?php if ($bar['total'] > 0 && count($chartData) <= 14): ?>
            <div style="font-size:9px;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= $fmt($bar['total']) ?></div>
            <?php endif; ?>
            <div title="<?= $bar['label'] ?>: <?= $fmt($bar['total']) ?>"
                 style="width:100%;height:<?= $pct ?>%;background:<?= $barCol ?>;border-radius:3px 3px 0 0;opacity:<?= $bar['total'] > 0 ? '0.9' : '1' ?>;cursor:pointer;"
                 onmouseenter="this.style.opacity='1';this.style.transform='scaleY(1.03)'"
                 onmouseleave="this.style.opacity='<?= $bar['total'] > 0 ? '0.9' : '1' ?>';this.style.transform='scaleY(1)'">
            </div>
            <?php if (count($chartData) <= 20): ?>
            <div style="font-size:9px;color:#9ca3af;white-space:nowrap;overflow:hidden;"><?= $bar['label'] ?></div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div style="height:180px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;">Belum ada data untuk periode ini</div>
        <?php endif; ?>
      </div>

      <!-- ── BOTTOM GRID ── -->
      <div class="admin-analytics-bottom" style="display:grid;grid-template-columns:1fr 2fr;gap:24px;">

        <!-- Kategori Terlaris -->
        <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
          <h3 style="font-size:15px;font-weight:700;color:#111827;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">Kategori Terlaris</h3>
          <?php if (!empty($topCategories)):
            $stops = []; $cur = 0;
            foreach ($topCategories as $idx => $cat) {
                $p = round($cat['sold'] / $totalCatSold * 100);
                $stops[] = "{$catColors[$idx % count($catColors)]} {$cur}% " . ($cur + $p) . "%";
                $cur += $p;
            }
          ?>
          <div style="position:relative;width:160px;height:160px;margin:16px auto;border-radius:50%;background:conic-gradient(<?= implode(',',$stops) ?>);display:flex;align-items:center;justify-content:center;">
            <div style="width:108px;height:108px;background:#fff;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;">
              <span style="font-size:18px;font-weight:700;color:#111827;"><?= array_sum(array_column($topCategories,'sold')) ?></span>
              <span style="font-size:10px;color:#6b7280;">item terjual</span>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;gap:10px;margin-top:16px;">
            <?php foreach ($topCategories as $idx => $cat):
              $pct = round($cat['sold'] / $totalCatSold * 100);
            ?>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:8px;height:8px;border-radius:50%;background:<?= $catColors[$idx % count($catColors)] ?>;flex-shrink:0;"></div>
                <span style="color:#4b5563;"><?= e($cat['name']) ?></span>
              </div>
              <div style="display:flex;align-items:center;gap:6px;">
                <span style="color:#9ca3af;font-size:11px;"><?= $cat['sold'] ?></span>
                <strong style="color:#111827;min-width:32px;text-align:right;"><?= $pct ?>%</strong>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
            <div style="text-align:center;color:#9ca3af;font-size:13px;padding:60px 0;">Belum ada data<br>untuk periode ini</div>
          <?php endif; ?>
        </div>

        <!-- Right column -->
        <div style="display:flex;flex-direction:column;gap:24px;">

          <!-- Penjual Teratas -->
          <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
              <h3 style="font-size:15px;font-weight:700;color:#111827;">🏆 Penjual Teratas</h3>
              <button onclick="showPage('admin_users')" style="font-size:12px;color:var(--accent);font-weight:600;background:none;border:none;cursor:pointer;">Lihat Semua →</button>
            </div>
            <?php if (!empty($topSellers)): ?>
            <div style="display:grid;grid-template-columns:repeat(<?= min(count($topSellers),3) ?>,1fr);gap:14px;">
              <?php $medals=['🥇','🥈','🥉']; foreach ($topSellers as $idx => $seller):
                $initials = strtoupper(implode('', array_map(fn($w)=>$w[0]??'', array_slice(explode(' ',$seller['name']),0,2))));
              ?>
              <div style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:16px 12px;border:1px solid #f3f4f6;border-radius:10px;text-align:center;position:relative;">
                <span style="position:absolute;top:8px;right:8px;font-size:15px;"><?= $medals[$idx] ?? '' ?></span>
                <div style="width:44px;height:44px;border-radius:50%;background:<?= $avatarBg[$idx % count($avatarBg)] ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;"><?= e($initials) ?></div>
                <div>
                  <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:2px;"><?= e($seller['name']) ?></div>
                  <div style="font-size:11px;color:#9ca3af;"><?= number_format($seller['sold']) ?> terjual</div>
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--accent);"><?= $fmt((int)$seller['revenue']) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
              <div style="text-align:center;color:#9ca3af;font-size:13px;padding:30px 0;">Belum ada penjual dengan transaksi selesai</div>
            <?php endif; ?>
          </div>

          <!-- Produk Terlaris -->
          <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.07);flex:1;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
              <h3 style="font-size:15px;font-weight:700;color:#111827;">🚀 Produk Terlaris</h3>
              <span style="font-size:11px;color:#9ca3af;"><?= htmlspecialchars($periodLabel) ?></span>
            </div>
            <?php if (!empty($topProducts)): ?>
            <table style="width:100%;border-collapse:collapse;">
              <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                  <th style="padding:8px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">#</th>
                  <th style="padding:8px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">Produk</th>
                  <th style="padding:8px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">Kategori</th>
                  <th style="padding:8px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;text-transform:uppercase;letter-spacing:0.5px;">Terjual</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($topProducts as $rank => $prod): ?>
                <tr style="border-bottom:1px solid #f9fafb;">
                  <td style="padding:13px 8px;">
                    <span style="width:22px;height:22px;border-radius:50%;background:<?= $rank===0?'#fbbf24':($rank===1?'#d1d5db':($rank===2?'#c97c3a':'#f3f4f6')) ?>;color:<?= $rank<3?'#fff':'#6b7280' ?>;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;"><?= $rank+1 ?></span>
                  </td>
                  <td style="padding:13px 8px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div style="width:20px;height:28px;background:#e5e7eb;border-radius:2px;flex-shrink:0;"></div>
                      <span style="font-size:13px;font-weight:500;color:#374151;"><?= e($prod['name']) ?></span>
                    </div>
                  </td>
                  <td style="padding:13px 8px;font-size:12px;color:#6b7280;"><?= e($prod['category']) ?></td>
                  <td style="padding:13px 8px;text-align:right;">
                    <span style="font-size:14px;font-weight:700;color:var(--accent);"><?= number_format($prod['sold_this_month']) ?></span>
                    <span style="font-size:11px;color:#9ca3af;"> item</span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php else: ?>
              <div style="text-align:center;color:#9ca3af;font-size:13px;padding:30px 0;">Belum ada produk terjual pada periode ini</div>
            <?php endif; ?>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

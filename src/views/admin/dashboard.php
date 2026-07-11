<div id="page-admin" class="page active admin-page">
  <div class="dash-layout admin-layout">

    <!-- SIDEBAR -->
    <?php $adminActivePage = 'admin'; require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="dash-content">

      <!-- Admin topbar -->
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <div class="admin-topbar-title">
            <h2>🖥️ Platform Control Center</h2>
            <p>Statistik &amp; Manajemen Platform RubbyBooks &middot; <?= date('l, d F Y') ?></p>
          </div>
        </div>
        <div class="admin-topbar-right">
          <button class="btn-admin-ghost" onclick="showPage('admin_analytics')">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Lihat Analitik
          </button>
        </div>
      </div>

      <!-- Admin body -->
      <div class="admin-body">

        <!-- ── PLATFORM KPI BANNER (row 1 — 4 kartu) ── -->
        <div class="platform-kpi-row">

          <!-- Total Penjual -->
          <div class="pkpi-card pkpi-sellers">
            <div class="pkpi-mini-top">
              <span class="pkpi-mini-icon">🏪</span>
              <span class="pkpi-trend pkpi-up">▲ +<?= (int)$stats['sellers_this_month'] ?> bulan ini</span>
            </div>
            <div class="pkpi-mini-val"><?= (int)$stats['total_sellers'] ?></div>
            <div class="pkpi-mini-label">Total Penjual</div>
            <div class="pkpi-mini-sub">
              <span class="pkpi-ok">✅ <?= (int)$stats['verified_sellers'] ?> Verified</span>
              <span class="pkpi-warn">⏳ <?= (int)$stats['pending_sellers'] ?> Pending</span>
            </div>
          </div>

          <!-- Verifikasi Seller -->
          <div class="pkpi-card pkpi-verif">
            <div class="pkpi-mini-top">
              <span class="pkpi-mini-icon">🔍</span>
              <span class="pkpi-trend <?= $stats['verification_queue'] > 0 ? 'pkpi-warn' : 'pkpi-up' ?>">
                <?= $stats['verification_queue'] > 0 ? '⚠ Perlu Tindakan' : '✓ Bersih' ?>
              </span>
            </div>
            <div class="pkpi-mini-val" style="color:#d97706"><?= (int)$stats['verification_queue'] ?></div>
            <div class="pkpi-mini-label">Antrian Verifikasi</div>
            <div class="pkpi-mini-sub">
              <button class="pkpi-verif-btn" onclick="showPage('admin_users')">Tinjau →</button>
            </div>
          </div>

          <!-- Total Transaksi -->
          <div class="pkpi-card pkpi-trx">
            <div class="pkpi-mini-top">
              <span class="pkpi-mini-icon">🛒</span>
              <span class="pkpi-trend pkpi-up">▲ <?= (int)$stats['orders_today'] ?> hari ini</span>
            </div>
            <div class="pkpi-mini-val"><?= number_format((int)$stats['total_orders'], 0, ',', '.') ?></div>
            <div class="pkpi-mini-label">Total Transaksi</div>
            <div class="pkpi-mini-sub">
              <span class="pkpi-ok">✅ <?= (int)$stats['completed_orders'] ?></span>
              <span class="pkpi-neutral">🚚 <?= (int)$stats['shipped_orders'] ?></span>
              <span class="pkpi-warn">⏳ <?= (int)$stats['pending_orders'] ?></span>
            </div>
          </div>

          <!-- Pendapatan Platform -->
          <div class="pkpi-card pkpi-revenue">
            <div class="pkpi-mini-top">
              <span class="pkpi-mini-icon">💸</span>
              <span class="pkpi-trend pkpi-up">▲ Aktif</span>
            </div>
            <div class="pkpi-mini-val">
              <?= $stats['revenue_month'] >= 1000000 ? 'Rp ' . round($stats['revenue_month'] / 1000000, 1) . 'jt' : rupiah((int)$stats['revenue_month']) ?>
            </div>
            <div class="pkpi-mini-label">Pendapatan Bulan Ini</div>
            <div class="pkpi-mini-sub">
              <span>YTD: <b style="color:var(--accent)">Rp <?= round($stats['revenue_ytd'] / 1000000, 1) ?>jt</b></span>
            </div>
          </div>

        </div>

        <!-- ── ALERT: Pending Verifikasi ── -->
        <?php if (!empty($pendingSellersList)): 
          $countPending = count($pendingSellersList);
          $namesStr = implode(', ', array_slice($pendingSellersList, 0, 2));
          if ($countPending > 2) {
              $namesStr .= ', dan ' . ($countPending - 2) . ' toko lainnya';
          }
        ?>
        <div class="pending-verif-card">
          <span class="pv-icon">🏪</span>
          <div class="pv-text">
            <strong><?= $countPending ?> Penjual Menunggu Verifikasi</strong>
            <p><?= e($namesStr) ?> menunggu persetujuan Anda.</p>
          </div>
          <button class="pv-action" onclick="showPage('admin_users')">Tinjau Sekarang →</button>
        </div>
        <?php endif; ?>

        <!-- Charts -->
        <div class="admin-charts-row">
          <!-- Revenue bar chart -->
          <div class="admin-chart-card">
            <div class="admin-chart-head">
              <div>
                <h4>Pendapatan Platform (6 Bulan Terakhir)</h4>
                <p>Total pendapatan kotor per bulan</p>
              </div>
              <span class="admin-chart-tag">Pendapatan</span>
            </div>
            
            <?php
              $totals = array_map(fn($item) => $item['total'], $monthlyRevenue);
              $maxVal = max($totals) ?: 1;
              $totalSum = array_sum($totals);
              $avgVal = $totalSum / count($monthlyRevenue);
            ?>

            <div class="admin-bar-chart">
              <?php foreach ($monthlyRevenue as $idx => $m): 
                $height = ($m['total'] / $maxVal) * 100;
                $isCurrent = ($idx === count($monthlyRevenue) - 1);
                
                $barBg = $isCurrent 
                  ? 'linear-gradient(180deg,var(--accent-mid),var(--accent))' 
                  : ($idx >= 3 ? 'var(--accent-light)' : '#e2e8f0');
                
                $lblStyle = $isCurrent ? ' style="color:var(--accent);font-weight:700"' : '';
                $valStr = $m['total'] >= 1000000 ? 'Rp ' . round($m['total']/1000000, 1) . 'jt' : rupiah((int)$m['total']);
              ?>
              <div class="admin-bar-item<?= $isCurrent ? ' admin-bar-current' : '' ?>">
                <div class="admin-bar-fill" style="height:<?= $height ?>%;background:<?= $barBg ?>">
                  <div class="admin-bar-tip"><?= e($m['label']) ?>: <?= $valStr ?><?= $isCurrent ? ' ✨' : '' ?></div>
                </div>
                <span class="admin-bar-lbl"<?= $lblStyle ?>><?= e($m['label']) ?></span>
              </div>
              <?php endforeach; ?>
            </div>
            
            <div class="admin-chart-summary">
              <div class="admin-rev-item">
                <div class="admin-rev-dot" style="background:var(--accent)"></div>
                <div>
                  <div class="admin-rev-label">Bulan Ini</div>
                  <div class="admin-rev-val">
                    <?= rupiah((int)end($monthlyRevenue)['total']) ?>
                  </div>
                </div>
              </div>
              <div class="admin-rev-item">
                <div class="admin-rev-dot" style="background:#e2e8f0"></div>
                <div>
                  <div class="admin-rev-label">Rata-rata (6 Bln)</div>
                  <div class="admin-rev-val"><?= rupiah((int)$avgVal) ?></div>
                </div>
              </div>
              <div class="admin-rev-item" style="margin-left:auto">
                <div>
                  <div class="admin-rev-label">Total YTD</div>
                  <div class="admin-rev-val" style="color:var(--accent)"><?= rupiah((int)$stats['revenue_ytd']) ?></div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Tables row: Orders + Users -->
        <div class="admin-tables-row">
          <!-- Orders table -->
          <div class="admin-table-card">
            <div class="admin-table-head">
              <div>
                <h4>Pesanan Terkini</h4>
                <p>Semua transaksi platform</p>
              </div>
              <a href="#" class="admin-table-link" onclick="showToast('📋 Semua pesanan')">Lihat semua →</a>
            </div>
            <div class="admin-table-responsive">
              <table class="admin-data-table">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Pembeli</th>
                    <th>Penjual</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--ink-muted);">Belum ada transaksi.</td></tr>
                  <?php else: ?>
                    <?php foreach ($recentOrders as $order): 
                      $statusClass = match ($order['status']) {
                        'delivered' => 'sp-done',
                        'shipped' => 'sp-ship',
                        'paid', 'processing' => 'sp-paid',
                        'cancelled' => 'sp-inactive',
                        default => 'sp-pending',
                      };
                      $statusLabel = match ($order['status']) {
                        'delivered' => '● Selesai',
                        'shipped' => '● Dikirim',
                        'paid' => '● Dibayar',
                        'processing' => '● Diproses',
                        'cancelled' => '● Batal',
                        default => '● Pending',
                      };
                    ?>
                    <tr>
                      <td><span class="td-mono">#<?= e($order['invoice_number']) ?></span></td>
                      <td><?= e($order['buyer_name']) ?></td>
                      <td style="color:#64748b;font-size:12px"><?= e($order['seller_names'] ?: 'RubbyBooks') ?></td>
                      <td><span class="td-amount"><?= rupiah((int)$order['total']) ?></span></td>
                      <td><span class="td-method"><?= e($order['payment_method'] ?? 'Transfer') ?></span></td>
                      <td><span class="status-pill <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                      <td>
                        <button class="admin-action-btn" onclick="showToast('📋 Detail pesanan #<?= e($order['invoice_number']) ?>')">Detail</button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Users table + Activity -->
          <div style="display:flex;flex-direction:column;gap:16px">
            <div class="admin-table-card">
              <div class="admin-table-head">
                <div>
                  <h4>Manajemen User</h4>
                  <p>Pengguna terdaftar terbaru</p>
                </div>
                <a href="#" class="admin-table-link" onclick="showPage('admin_users')">Lihat semua →</a>
              </div>
              <div class="admin-table-responsive">
                <table class="admin-data-table">
                  <thead>
                    <tr>
                      <th>Pengguna</th>
                      <th>Peran</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recentUsers)): ?>
                      <tr><td colspan="4" style="text-align:center;color:var(--ink-muted);">Belum ada user.</td></tr>
                    <?php else: ?>
                      <?php foreach ($recentUsers as $u): 
                        $initials = strtoupper(substr($u['name'], 0, 2));
                        $bgGradient = match($u['role']) {
                            'admin' => 'linear-gradient(135deg,var(--accent),var(--accent-deep))',
                            'seller' => 'linear-gradient(135deg,#10b981,#059669)',
                            default => 'linear-gradient(135deg,#3b82f6,#2563eb)'
                        };
                        
                        $roleClass = match($u['role']) {
                            'admin' => 'sp-done',
                            'seller' => 'sp-seller',
                            default => 'sp-buyer'
                        };
                        $roleLabel = match($u['role']) {
                            'admin' => '🔐 Admin',
                            'seller' => '🏪 Penjual',
                            default => '🛒 Pembeli'
                        };
  
                        $statusClass = match($u['status']) {
                            'active' => 'sp-active',
                            'pending' => 'sp-verify',
                            default => 'sp-inactive'
                        };
                        $statusLabel = match($u['status']) {
                            'active' => '● Aktif',
                            'pending' => '⏳ Verifikasi',
                            default => '● Banned'
                        };
                      ?>
                      <tr>
                        <td>
                          <div class="td-user">
                            <div class="td-user-avatar" style="background:<?= $bgGradient ?>"><?= e($initials) ?></div>
                            <div>
                              <div class="td-user-name"><?= e($u['name']) ?></div>
                              <div class="td-user-email"><?= e($u['email']) ?></div>
                            </div>
                          </div>
                        </td>
                        <td><span class="status-pill <?= $roleClass ?>"><?= $roleLabel ?></span></td>
                        <td><span class="status-pill <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                        <td>
                          <?php if ($u['role'] === 'seller' && $u['status'] === 'pending'): ?>
                            <form method="post" action="index.php?action=approve_seller" style="display:inline;">
                              <input type="hidden" name="seller_id" value="<?= $u['id'] ?>">
                              <button type="submit" class="admin-action-btn success">Verifikasi</button>
                            </form>
                          <?php else: ?>
                            <button class="admin-action-btn" onclick="showPage('admin_users')">Kelola</button>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Activity feed -->
            <div class="activity-card">
              <div class="activity-head">
                <h4>Aktivitas Terbaru</h4>
              </div>
              <div class="activity-list">
                <?php if (empty($recentLogs)): ?>
                  <div style="padding:20px;text-align:center;color:var(--ink-muted);font-size:12.5px;">Belum ada log aktivitas.</div>
                <?php else: ?>
                  <?php foreach ($recentLogs as $log): 
                    $dotColor = '#3b82f6';
                    if (stripos($log['activity'], 'login') !== false) $dotColor = '#10b981';
                    if (stripos($log['activity'], 'registrasi') !== false) $dotColor = '#f59e0b';
                    if (stripos($log['activity'], 'checkout') !== false) $dotColor = '#a855f7';
                    if (stripos($log['activity'], 'update') !== false) $dotColor = '#3b82f6';
                    
                    $timeDiff = time() - strtotime($log['created_at']);
                    if ($timeDiff < 60) {
                        $timeStr = 'Baru saja';
                    } elseif ($timeDiff < 3600) {
                        $timeStr = round($timeDiff / 60) . ' menit lalu';
                    } elseif ($timeDiff < 86400) {
                        $timeStr = round($timeDiff / 3600) . ' jam lalu';
                    } else {
                        $timeStr = date('d M Y', strtotime($log['created_at']));
                    }
                  ?>
                  <div class="activity-item">
                    <div class="activity-dot-wrap">
                      <div class="activity-dot" style="background:<?= $dotColor ?>"></div>
                      <div class="activity-line"></div>
                    </div>
                    <div class="activity-content">
                      <div class="activity-text"><?= e($log['activity']) ?></div>
                      <div class="activity-time"><?= $timeStr ?></div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

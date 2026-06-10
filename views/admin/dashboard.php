<div id="page-admin" class="page active">
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
          <button class="sidebar-item active">
            <span class="si">📊</span> Dashboard
          </button>
          <button class="sidebar-item" onclick="showToast('📈 Halaman Analitik')">
            <span class="si">📈</span> Analitik
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Manajemen</div>
          <button class="sidebar-item" onclick="showToast('👥 Kelola User')">
            <span class="si">👥</span> Kelola User
            <span class="sidebar-badge warn">3</span>
          </button>
          <button class="sidebar-item" onclick="showToast('📚 Kelola Produk')">
            <span class="si">📚</span> Kelola Produk
          </button>
          <button class="sidebar-item" onclick="showToast('🗂️ Kategori')">
            <span class="si">🗂️</span> Kategori
          </button>
          <button class="sidebar-item" onclick="showToast('🛒 Semua Pesanan')">
            <span class="si">🛒</span> Semua Pesanan
            <span class="sidebar-badge">12</span>
          </button>
          <button class="sidebar-item" onclick="showToast('💳 Pembayaran')">
            <span class="si">💳</span> Pembayaran
          </button>
          <button class="sidebar-item" onclick="showToast('⭐ Kelola Ulasan')">
            <span class="si">⭐</span> Ulasan
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Sistem</div>
          <button class="sidebar-item" onclick="showToast('🔔 Notifikasi')">
            <span class="si">🔔</span> Notifikasi
          </button>
          <button class="sidebar-item" onclick="showToast('⚙️ Pengaturan Platform')">
            <span class="si">⚙️</span> Pengaturan
          </button>
          <button class="sidebar-item" onclick="showToast('🛡️ Log Keamanan')">
            <span class="si">🛡️</span> Keamanan
          </button>
        </div>
      </nav>

      <div class="sidebar-footer">
        <button class="sidebar-item" onclick="doLogout()" style="width:100%">
          <span class="si">🚪</span> Keluar
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="dash-content" style="background:#f1f5f9">

      <!-- Admin topbar -->
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <div class="admin-topbar-title">
            <h2>🖥️ Platform Control Center</h2>
            <p>Statistik &amp; Manajemen Platform RubbyBooks &middot; Jumat, 5 Juni 2026</p>
          </div>
        </div>
        <div class="admin-topbar-right">
          <button class="btn-admin-ghost" onclick="showToast('📊 Laporan diunduh!')">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export Laporan
          </button>
          <button class="btn-admin-primary" onclick="showToast('👤 Tambah user baru')">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
          </button>
        </div>
      </div>

      <!-- Admin body -->
      <div class="admin-body">

        <!-- ── PLATFORM KPI BANNER (row 1 — 6 kartu besar) ── -->
        <div class="platform-kpi-row">

          <!-- Total User -->
          <div class="pkpi-card pkpi-users">
            <div class="pkpi-top">
              <div class="pkpi-icon-wrap">👥</div>
              <div class="pkpi-trend pkpi-up">▲ +312 minggu ini</div>
            </div>
            <div class="pkpi-val">86.420</div>
            <div class="pkpi-label">Total Pengguna</div>
            <div class="pkpi-breakdown">
              <span>🛒 85.900 Pembeli</span>
              <span>🏪 512 Penjual</span>
              <span>🔐 8 Admin</span>
            </div>
            <div class="pkpi-bar"><div class="pkpi-bar-fill" style="width:93%;background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div></div>
          </div>

          <!-- Total Seller -->
          <div class="pkpi-card pkpi-sellers">
            <div class="pkpi-top">
              <div class="pkpi-icon-wrap">🏪</div>
              <div class="pkpi-trend pkpi-up">▲ +18 bulan ini</div>
            </div>
            <div class="pkpi-val">512</div>
            <div class="pkpi-label">Total Penjual</div>
            <div class="pkpi-breakdown">
              <span class="pkpi-ok">✅ 509 Terverifikasi</span>
              <span class="pkpi-warn">⏳ 3 Pending</span>
            </div>
            <div class="pkpi-bar"><div class="pkpi-bar-fill" style="width:62%;background:linear-gradient(90deg,var(--accent),var(--accent-light))"></div></div>
          </div>

          <!-- Verifikasi Seller -->
          <div class="pkpi-card pkpi-verif">
            <div class="pkpi-top">
              <div class="pkpi-icon-wrap">🔍</div>
              <div class="pkpi-trend pkpi-warn">⚠ Perlu Tindakan</div>
            </div>
            <div class="pkpi-val" style="color:#d97706">3</div>
            <div class="pkpi-label">Antrian Verifikasi</div>
            <div class="pkpi-verif-list">
              <div class="pkpi-verif-item">🏪 Toko Buku Nusantara <span>2j lalu</span></div>
              <div class="pkpi-verif-item">📚 Pustaka Ilmu <span>5j lalu</span></div>
              <div class="pkpi-verif-item">✍️ Karya Mandiri <span>1h lalu</span></div>
            </div>
            <button class="pkpi-verif-btn" onclick="showToast('✅ Membuka halaman verifikasi penjual')">Tinjau Sekarang →</button>
          </div>

          <!-- Total Transaksi -->
          <div class="pkpi-card pkpi-trx">
            <div class="pkpi-top">
              <div class="pkpi-icon-wrap">🛒</div>
              <div class="pkpi-trend pkpi-up">▲ 47 hari ini</div>
            </div>
            <div class="pkpi-val">1.284</div>
            <div class="pkpi-label">Total Transaksi</div>
            <div class="pkpi-breakdown">
              <span class="pkpi-ok">✅ 1.241 Selesai</span>
              <span class="pkpi-neutral">🚚 38 Dikirim</span>
              <span class="pkpi-warn">⏳ 5 Pending</span>
            </div>
            <div class="pkpi-bar"><div class="pkpi-bar-fill" style="width:71%;background:linear-gradient(90deg,var(--accent),var(--accent-mid))"></div></div>
          </div>

          <!-- Pendapatan Platform -->
          <div class="pkpi-card pkpi-revenue">
            <div class="pkpi-top">
              <div class="pkpi-icon-wrap">💸</div>
              <div class="pkpi-trend pkpi-up">▲ 23% vs bulan lalu</div>
            </div>
            <div class="pkpi-val">Rp 48jt</div>
            <div class="pkpi-label">Pendapatan Bulan Ini</div>
            <div class="pkpi-breakdown">
              <span>YTD: <b style="color:var(--accent)">Rp 174jt</b></span>
              <span>Fee rata: <b>3.2%</b></span>
            </div>
            <div class="pkpi-bar"><div class="pkpi-bar-fill" style="width:86%;background:linear-gradient(90deg,var(--accent),var(--accent-mid))"></div></div>
          </div>

          <!-- Status Sistem -->
          <div class="pkpi-card pkpi-system">
            <div class="pkpi-top">
              <div class="pkpi-icon-wrap">🖥️</div>
              <div class="pkpi-trend pkpi-ok" style="color:#15803d;background:#dcfce7">● Semua Normal</div>
            </div>
            <div class="pkpi-val" style="color:#15803d;font-size:20px;margin-bottom:8px">Operasional</div>
            <div class="pkpi-label">Status Sistem</div>
            <div class="pkpi-sys-list">
              <div class="pkpi-sys-row">
                <span class="pkpi-sys-dot" style="background:#22c55e"></span>
                <span class="pkpi-sys-name">Server</span>
                <span class="pkpi-sys-val">Online</span>
              </div>
              <div class="pkpi-sys-row">
                <span class="pkpi-sys-dot" style="background:#22c55e"></span>
                <span class="pkpi-sys-name">Uptime</span>
                <span class="pkpi-sys-val">99.98%</span>
              </div>
              <div class="pkpi-sys-row">
                <span class="pkpi-sys-dot" style="background:#22c55e"></span>
                <span class="pkpi-sys-name">Resp. Avg.</span>
                <span class="pkpi-sys-val">142ms</span>
              </div>
              <div class="pkpi-sys-row">
                <span class="pkpi-sys-dot" style="background:#3b82f6"></span>
                <span class="pkpi-sys-name">DB</span>
                <span class="pkpi-sys-val">Sehat</span>
              </div>
            </div>
          </div>

        </div>

        <!-- ── ALERT: Pending Verifikasi ── -->
        <div class="pending-verif-card">
          <span class="pv-icon">🏪</span>
          <div class="pv-text">
            <strong>3 Penjual Menunggu Verifikasi</strong>
            <p>Toko Buku Nusantara, Pustaka Ilmu, dan 1 toko lainnya menunggu persetujuan Anda.</p>
          </div>
          <button class="pv-action" onclick="showToast('✅ Halaman verifikasi penjual')">Tinjau Sekarang →</button>
        </div>

        <!-- Charts -->
        <div class="admin-charts-row">
          <!-- Revenue bar chart -->
          <div class="admin-chart-card">
            <div class="admin-chart-head">
              <div>
                <h4>Pendapatan Platform 2025</h4>
                <p>Total pendapatan per bulan (dalam jutaan Rp)</p>
              </div>
              <span class="admin-chart-tag">Jutaan Rp</span>
            </div>
            <div class="admin-bar-chart">
              <div class="admin-bar-item">
                <div class="admin-bar-fill" style="height:37%;background:#e2e8f0">
                  <div class="admin-bar-tip">Jan: Rp 18jt</div>
                </div>
                <span class="admin-bar-lbl">Jan</span>
              </div>
              <div class="admin-bar-item">
                <div class="admin-bar-fill" style="height:48%;background:#e2e8f0">
                  <div class="admin-bar-tip">Feb: Rp 23jt</div>
                </div>
                <span class="admin-bar-lbl">Feb</span>
              </div>
              <div class="admin-bar-item">
                <div class="admin-bar-fill" style="height:44%;background:#e2e8f0">
                  <div class="admin-bar-tip">Mar: Rp 21jt</div>
                </div>
                <span class="admin-bar-lbl">Mar</span>
              </div>
              <div class="admin-bar-item">
                <div class="admin-bar-fill" style="height:60%;background:#c4b5fd">
                  <div class="admin-bar-tip">Apr: Rp 29jt</div>
                </div>
                <span class="admin-bar-lbl">Apr</span>
              </div>
              <div class="admin-bar-item">
                <div class="admin-bar-fill" style="height:73%;background:#a78bfa">
                  <div class="admin-bar-tip">Mei: Rp 35jt</div>
                </div>
                <span class="admin-bar-lbl">Mei</span>
              </div>
              <div class="admin-bar-item admin-bar-current">
                <div class="admin-bar-fill" style="height:100%;background:linear-gradient(180deg,var(--accent-mid),var(--accent))">
                  <div class="admin-bar-tip">Jun: Rp 48jt ✨</div>
                </div>
                <span class="admin-bar-lbl" style="color:var(--accent);font-weight:700">Jun</span>
              </div>
            </div>
            <div class="admin-chart-summary">
              <div class="admin-rev-item">
                <div class="admin-rev-dot" style="background:var(--accent)"></div>
                <div>
                  <div class="admin-rev-label">Bulan Ini</div>
                  <div class="admin-rev-val">Rp 48jt</div>
                </div>
              </div>
              <div class="admin-rev-item">
                <div class="admin-rev-dot" style="background:#e2e8f0"></div>
                <div>
                  <div class="admin-rev-label">Rata-rata</div>
                  <div class="admin-rev-val">Rp 29jt</div>
                </div>
              </div>
              <div class="admin-rev-item" style="margin-left:auto">
                <div>
                  <div class="admin-rev-label">Total YTD</div>
                  <div class="admin-rev-val" style="color:var(--accent)">Rp 174jt</div>
                </div>
              </div>
            </div>
          </div>

          <!-- User distribution donut -->
          <div class="admin-chart-card">
            <div class="admin-chart-head">
              <div>
                <h4>Distribusi Pengguna</h4>
                <p>Berdasarkan peran akun</p>
              </div>
            </div>
            <div class="admin-donut-wrap">
              <svg width="130" height="130" viewBox="0 0 130 130">
                <circle cx="65" cy="65" r="50" fill="none" stroke="#f1f5f9" stroke-width="18"/>
                <!-- Pembeli 99% -->
                <circle cx="65" cy="65" r="50" fill="none" stroke="#3b82f6" stroke-width="18"
                  stroke-dasharray="311" stroke-dashoffset="31.4" stroke-linecap="round"
                  transform="rotate(-90 65 65)"/>
                <!-- Penjual 0.6% -->
                <circle cx="65" cy="65" r="50" fill="none" stroke="var(--accent)" stroke-width="18"
                  stroke-dasharray="2" stroke-dashoffset="-280" stroke-linecap="round"
                  transform="rotate(-90 65 65)"/>
                <text x="65" y="60" text-anchor="middle" fill="#0f172a" font-size="16" font-weight="700" font-family="Playfair Display, serif">86K</text>
                <text x="65" y="76" text-anchor="middle" fill="#94a3b8" font-size="9.5" font-family="Plus Jakarta Sans, sans-serif">pengguna</text>
              </svg>
              <div class="admin-donut-legend">
                <div class="admin-donut-row">
                  <div class="admin-donut-row-left">
                    <div class="admin-donut-dot" style="background:#3b82f6"></div>
                    <span class="admin-donut-name">Pembeli</span>
                  </div>
                  <div>
                    <span class="admin-donut-count">85.900</span>
                    <span class="admin-donut-pct"> · 99.4%</span>
                  </div>
                </div>
                <div class="admin-donut-row">
                  <div class="admin-donut-row-left">
                    <div class="admin-donut-dot" style="background:var(--accent)"></div>
                    <span class="admin-donut-name">Penjual</span>
                  </div>
                  <div>
                    <span class="admin-donut-count">512</span>
                    <span class="admin-donut-pct"> · 0.59%</span>
                  </div>
                </div>
                <div class="admin-donut-row">
                  <div class="admin-donut-row-left">
                    <div class="admin-donut-dot" style="background:#f1f5f9"></div>
                    <span class="admin-donut-name">Admin</span>
                  </div>
                  <div>
                    <span class="admin-donut-count">8</span>
                    <span class="admin-donut-pct"> · &lt;0.01%</span>
                  </div>
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
                <tr>
                  <td><span class="td-mono">#RB-001</span></td>
                  <td>Bimo W.</td>
                  <td style="color:#64748b;font-size:12px">Toko Sari</td>
                  <td><span class="td-amount">Rp 89.000</span></td>
                  <td><span class="td-method">GoPay</span></td>
                  <td><span class="status-pill sp-done">● Selesai</span></td>
                  <td><button class="admin-action-btn" onclick="showToast('📋 Detail pesanan')">Detail</button></td>
                </tr>
                <tr>
                  <td><span class="td-mono">#RB-002</span></td>
                  <td>Devi M.</td>
                  <td style="color:#64748b;font-size:12px">Buku Nusantara</td>
                  <td><span class="td-amount">Rp 245.000</span></td>
                  <td><span class="td-method">Transfer</span></td>
                  <td><span class="status-pill sp-ship">● Dikirim</span></td>
                  <td><button class="admin-action-btn" onclick="showToast('📋 Detail pesanan')">Detail</button></td>
                </tr>
                <tr>
                  <td><span class="td-mono">#RB-003</span></td>
                  <td>Rizal A.</td>
                  <td style="color:#64748b;font-size:12px">Toko Sari</td>
                  <td><span class="td-amount">Rp 130.000</span></td>
                  <td><span class="td-method">OVO</span></td>
                  <td><span class="status-pill sp-paid">● Dibayar</span></td>
                  <td><button class="admin-action-btn" onclick="showToast('📋 Detail pesanan')">Detail</button></td>
                </tr>
                <tr>
                  <td><span class="td-mono">#RB-004</span></td>
                  <td>Putri K.</td>
                  <td style="color:#64748b;font-size:12px">Buku Impian</td>
                  <td><span class="td-amount">Rp 78.000</span></td>
                  <td><span class="td-method">DANA</span></td>
                  <td><span class="status-pill sp-pending">● Pending</span></td>
                  <td><button class="admin-action-btn danger" onclick="showToast('⚠️ Pesanan dibatalkan!')">Batalkan</button></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Users table + Activity -->
          <div style="display:flex;flex-direction:column;gap:16px">
            <div class="admin-table-card">
              <div class="admin-table-head">
                <div>
                  <h4>Manajemen User</h4>
                  <p>Pengguna terdaftar</p>
                </div>
                <a href="#" class="admin-table-link" onclick="showToast('👥 Semua user')">Lihat semua →</a>
              </div>
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
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-user-avatar" style="background:linear-gradient(135deg,var(--accent),var(--accent-deep))">SR</div>
                        <div>
                          <div class="td-user-name">Sari Rahayu</div>
                          <div class="td-user-email">sari@email.com</div>
                        </div>
                      </div>
                    </td>
                    <td><span class="status-pill sp-seller">🏪 Penjual</span></td>
                    <td><span class="status-pill sp-active">● Aktif</span></td>
                    <td><button class="admin-action-btn" onclick="showToast('👤 Kelola user Sari')">Kelola</button></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-user-avatar" style="background:linear-gradient(135deg,#1e40af,#2563eb)">BW</div>
                        <div>
                          <div class="td-user-name">Bimo Wicaksono</div>
                          <div class="td-user-email">bimo@email.com</div>
                        </div>
                      </div>
                    </td>
                    <td><span class="status-pill sp-buyer">🛒 Pembeli</span></td>
                    <td><span class="status-pill sp-active">● Aktif</span></td>
                    <td><button class="admin-action-btn" onclick="showToast('👤 Kelola user Bimo')">Kelola</button></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-user-avatar" style="background:linear-gradient(135deg,#d97706,#b45309)">DL</div>
                        <div>
                          <div class="td-user-name">Dewi Lestari</div>
                          <div class="td-user-email">dewi@email.com</div>
                        </div>
                      </div>
                    </td>
                    <td><span class="status-pill sp-seller">🏪 Penjual</span></td>
                    <td><span class="status-pill sp-verify">⏳ Verifikasi</span></td>
                    <td><button class="admin-action-btn success" onclick="showToast('✅ Penjual diverifikasi!')">Verifikasi</button></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Activity feed -->
            <div class="activity-card">
              <div class="activity-head">
                <h4>Aktivitas Terbaru</h4>
              </div>
              <div class="activity-list">
                <div class="activity-item">
                  <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#22c55e"></div>
                    <div class="activity-line"></div>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text"><b>Transaksi baru</b> dari Bimo W. — Rp 89.000</div>
                    <div class="activity-time">2 menit lalu</div>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#f59e0b"></div>
                    <div class="activity-line"></div>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text"><b>Toko baru</b> Pustaka Ilmu menunggu verifikasi</div>
                    <div class="activity-time">15 menit lalu</div>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#3b82f6"></div>
                    <div class="activity-line"></div>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text"><b>312 pengguna baru</b> bergabung minggu ini</div>
                    <div class="activity-time">1 jam lalu</div>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:var(--accent)"></div>
                    <div class="activity-line"></div>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text"><b>Laporan bulanan</b> Juni sudah tersedia</div>
                    <div class="activity-time">3 jam lalu</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
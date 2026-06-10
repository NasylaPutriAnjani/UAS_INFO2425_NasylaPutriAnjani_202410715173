<div id="page-seller" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <aside class="dash-sidebar seller-sidebar">
      <div class="sidebar-store-profile">
        <div class="sidebar-store-avatar">🏪</div>
        <div>
          <div class="sidebar-store-name">Toko Buku Sari</div>
          <div class="sidebar-store-status">Toko Aktif</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="sidebar-group">
          <div class="sidebar-group-label">Menu Utama</div>
          <button class="sidebar-item active">
            <span class="si">📊</span> Dashboard
          </button>
          <button class="sidebar-item" onclick="showToast('📦 Halaman Produk Saya')">
            <span class="si">📦</span> Produk Saya
            <span class="sidebar-badge">24</span>
          </button>
          <button class="sidebar-item" onclick="showToast('🛒 Pesanan Masuk')">
            <span class="si">🛒</span> Pesanan Masuk
            <span class="sidebar-badge warn">7</span>
          </button>
          <button class="sidebar-item" onclick="showToast('💬 Ulasan & Rating')">
            <span class="si">💬</span> Ulasan & Rating
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Keuangan</div>
          <button class="sidebar-item" onclick="showToast('💰 Laporan Penjualan')">
            <span class="si">💰</span> Laporan Penjualan
          </button>
          <button class="sidebar-item" onclick="showToast('🏦 Penarikan Dana')">
            <span class="si">🏦</span> Penarikan Dana
          </button>
          <button class="sidebar-item" onclick="showToast('🎟️ Promosi & Voucher')">
            <span class="si">🎟️</span> Promosi & Voucher
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Pengaturan</div>
          <button class="sidebar-item" onclick="showToast('⚙️ Pengaturan Toko')">
            <span class="si">⚙️</span> Pengaturan Toko
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
          <p>Selamat datang kembali, <b>Toko Buku Sari</b> 👋 — Kamis, 5 Juni 2025</p>
        </div>
        <div class="dash-topbar-right">
          <select class="dash-period-select">
            <option>Bulan Ini</option>
            <option>30 Hari Terakhir</option>
            <option>Kuartal Ini</option>
            <option>Tahun Ini</option>
          </select>
          <button class="btn-dash-primary" onclick="showToast('📦 Form tambah produk dibuka!')">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Produk
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">

        <!-- Alert strip -->
        <div class="alert-strip">
          <span class="alert-strip-icon">⚠️</span>
          <div class="alert-strip-text"><b>3 produk</b> hampir kehabisan stok. Segera lakukan restock agar tidak kehilangan pembeli.</div>
          <button class="alert-strip-action" onclick="showToast('📦 Menuju halaman stok...')">Lihat Produk →</button>
        </div>

        <!-- Metrics -->
        <div class="metrics-grid">
          <!-- Revenue -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#f0fdf4">💰</div>
              <div class="metric-trend trend-up">▲ 12%</div>
            </div>
            <div class="metric-val">Rp 4,8jt</div>
            <div class="metric-label">Pendapatan Bulan Ini</div>
            <div class="metric-sub">vs Rp 4,3jt bulan lalu</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:78%;background:linear-gradient(90deg,var(--accent),var(--accent-mid))"></div></div>
          </div>
          <!-- Orders -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#eff6ff">📦</div>
              <div class="metric-trend trend-up">▲ 8</div>
            </div>
            <div class="metric-val">142</div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-sub">8 pesanan baru hari ini</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:62%;background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div></div>
          </div>
          <!-- Products -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fdf4ff">📚</div>
              <div class="metric-trend trend-neutral">3 ⚠️</div>
            </div>
            <div class="metric-val">24</div>
            <div class="metric-label">Produk Aktif</div>
            <div class="metric-sub">3 stok hampir habis</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:45%;background:linear-gradient(90deg,#a855f7,#c084fc)"></div></div>
          </div>
          <!-- Rating -->
          <div class="metric-card">
            <div class="metric-card-top">
              <div class="metric-icon-wrap" style="background:#fffbeb">⭐</div>
              <div class="metric-trend trend-up">Sangat Baik</div>
            </div>
            <div class="metric-val">4.87</div>
            <div class="metric-label">Rating Toko</div>
            <div class="metric-sub">dari 328 ulasan pembeli</div>
            <div class="metric-bar"><div class="metric-bar-fill" style="width:97%;background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div></div>
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
                <button class="chart-tab" onclick="showToast('📈 Tampilan Mingguan')">Minggu</button>
              </div>
            </div>
            <div class="bar-chart-wrap">
              <div class="bar-chart-grid">
                <div class="bar-grid-line"><span class="bar-grid-label">5jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label">3,75jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label">2,5jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label">1,25jt</span></div>
                <div class="bar-grid-line"><span class="bar-grid-label">0</span></div>
              </div>
              <div class="bar-chart-bars">
                <div class="bar-item">
                  <div class="bar-fill" style="height:42%;background:#e2e8f0">
                    <div class="bar-tooltip">Des: Rp 2,1jt</div>
                  </div>
                  <span class="bar-label">Des</span>
                </div>
                <div class="bar-item">
                  <div class="bar-fill" style="height:32%;background:#e2e8f0">
                    <div class="bar-tooltip">Jan: Rp 1,6jt</div>
                  </div>
                  <span class="bar-label">Jan</span>
                </div>
                <div class="bar-item">
                  <div class="bar-fill" style="height:54%;background:#e2e8f0">
                    <div class="bar-tooltip">Feb: Rp 2,7jt</div>
                  </div>
                  <span class="bar-label">Feb</span>
                </div>
                <div class="bar-item">
                  <div class="bar-fill" style="height:44%;background:#e2e8f0">
                    <div class="bar-tooltip">Mar: Rp 2,2jt</div>
                  </div>
                  <span class="bar-label">Mar</span>
                </div>
                <div class="bar-item">
                  <div class="bar-fill" style="height:66%;background:#bbf7d0">
                    <div class="bar-tooltip">Apr: Rp 3,3jt</div>
                  </div>
                  <span class="bar-label">Apr</span>
                </div>
                <div class="bar-item bar-current">
                  <div class="bar-fill" style="height:96%;background:linear-gradient(180deg, var(--accent-mid), var(--accent))">
                    <div class="bar-tooltip">Mei: Rp 4,8jt ✨</div>
                  </div>
                  <span class="bar-label" style="color:var(--accent);font-weight:700">Mei</span>
                </div>
              </div>
            </div>
            <div class="revenue-summary">
              <div class="rev-item">
                <div class="rev-dot" style="background:var(--accent)"></div>
                <div>
                  <div class="rev-label">Bulan Ini</div>
                  <div class="rev-val">Rp 4,8jt</div>
                </div>
              </div>
              <div class="rev-item">
                <div class="rev-dot" style="background:#e2e8f0"></div>
                <div>
                  <div class="rev-label">Rata-rata</div>
                  <div class="rev-val">Rp 2,8jt</div>
                </div>
              </div>
              <div class="rev-item" style="margin-left:auto">
                <div>
                  <div class="rev-label">Total 6 Bulan</div>
                  <div class="rev-val" style="color:var(--accent)">Rp 16,7jt</div>
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
                  <!-- Pengembangan Diri 40% -->
                  <circle cx="65" cy="65" r="50" fill="none" stroke="var(--accent)" stroke-width="18"
                    stroke-dasharray="125.7 188.5" stroke-dashoffset="31.4" stroke-linecap="round"
                    transform="rotate(-90 65 65)"/>
                  <!-- Sastra 22% -->
                  <circle cx="65" cy="65" r="50" fill="none" stroke="#86efac" stroke-width="18"
                    stroke-dasharray="69.1 245.1" stroke-dashoffset="-94.2" stroke-linecap="round"
                    transform="rotate(-90 65 65)"/>
                  <!-- Keuangan 13% -->
                  <circle cx="65" cy="65" r="50" fill="none" stroke="#fbbf24" stroke-width="18"
                    stroke-dasharray="40.8 273.3" stroke-dashoffset="-163.4" stroke-linecap="round"
                    transform="rotate(-90 65 65)"/>
                  <!-- Lainnya 25% -->
                  <circle cx="65" cy="65" r="50" fill="none" stroke="#e2e8f0" stroke-width="18"
                    stroke-dasharray="78.5 235.6" stroke-dashoffset="-204.2" stroke-linecap="round"
                    transform="rotate(-90 65 65)"/>
                  <text x="65" y="60" text-anchor="middle" fill="#1e293b" font-size="18" font-weight="700" font-family="Playfair Display, serif">24</text>
                  <text x="65" y="75" text-anchor="middle" fill="#94a3b8" font-size="9.5" font-family="Plus Jakarta Sans, sans-serif">produk</text>
                </svg>
              </div>
              <div class="donut-legend-grid">
                <div class="donut-legend-item">
                  <div class="donut-legend-dot" style="background:var(--accent)"></div>
                  <div class="donut-legend-info">
                    <div class="dn">Pengembangan</div>
                    <div class="dv">40%</div>
                  </div>
                </div>
                <div class="donut-legend-item">
                  <div class="donut-legend-dot" style="background:#86efac"></div>
                  <div class="donut-legend-info">
                    <div class="dn">Sastra</div>
                    <div class="dv">22%</div>
                  </div>
                </div>
                <div class="donut-legend-item">
                  <div class="donut-legend-dot" style="background:#fbbf24"></div>
                  <div class="donut-legend-info">
                    <div class="dn">Keuangan</div>
                    <div class="dv">13%</div>
                  </div>
                </div>
                <div class="donut-legend-item">
                  <div class="donut-legend-dot" style="background:#e2e8f0"></div>
                  <div class="donut-legend-info">
                    <div class="dn">Lainnya</div>
                    <div class="dv">25%</div>
                  </div>
                </div>
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
              <a href="#" onclick="showToast('📋 Semua pesanan')">Lihat semua →</a>
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
                <tr>
                  <td><span class="td-order">#RB-001</span></td>
                  <td>
                    <div class="td-buyer">
                      <div class="td-buyer-avatar">BW</div>
                      <span class="td-buyer-name">Bimo W.</span>
                    </div>
                  </td>
                  <td><span class="td-book">Atomic Habits × 1</span></td>
                  <td><span class="td-total">Rp 89.000</span></td>
                  <td><span class="status s-paid">● Dibayar</span></td>
                  <td><button class="action-btn" onclick="showToast('📦 Pesanan dikonfirmasi & dikirim!')">Kirim</button></td>
                </tr>
                <tr>
                  <td><span class="td-order">#RB-002</span></td>
                  <td>
                    <div class="td-buyer">
                      <div class="td-buyer-avatar">SR</div>
                      <span class="td-buyer-name">Sari R.</span>
                    </div>
                  </td>
                  <td><span class="td-book">Bumi Manusia × 2</span></td>
                  <td><span class="td-total">Rp 170.000</span></td>
                  <td><span class="status s-pending">● Menunggu</span></td>
                  <td><button class="action-btn" onclick="showToast('✅ Pesanan dikonfirmasi!')">Konfirmasi</button></td>
                </tr>
                <tr>
                  <td><span class="td-order">#RB-003</span></td>
                  <td>
                    <div class="td-buyer">
                      <div class="td-buyer-avatar">DL</div>
                      <span class="td-buyer-name">Dewi L.</span>
                    </div>
                  </td>
                  <td><span class="td-book">Rich Dad + Sapiens</span></td>
                  <td><span class="td-total">Rp 190.000</span></td>
                  <td><span class="status s-ship">● Dikirim</span></td>
                  <td><button class="action-btn" onclick="showToast('📋 Detail pesanan')">Detail</button></td>
                </tr>
                <tr>
                  <td><span class="td-order">#RB-004</span></td>
                  <td>
                    <div class="td-buyer">
                      <div class="td-buyer-avatar">AF</div>
                      <span class="td-buyer-name">Ahmad F.</span>
                    </div>
                  </td>
                  <td><span class="td-book">Ikigai × 1</span></td>
                  <td><span class="td-total">Rp 78.000</span></td>
                  <td><span class="status s-done">● Selesai</span></td>
                  <td><button class="action-btn" onclick="showToast('📋 Detail pesanan')">Detail</button></td>
                </tr>
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
                <button class="quick-action-item" onclick="showToast('📦 Form tambah produk')">
                  <div class="qa-icon" style="background:#f0fdf4">📦</div>
                  <div>
                    <div class="qa-title">Tambah Produk</div>
                    <div class="qa-desc">Upload buku baru ke toko</div>
                  </div>
                  <span class="qa-arrow">›</span>
                </button>
                <button class="quick-action-item" onclick="showToast('🎟️ Buat promo & voucher')">
                  <div class="qa-icon" style="background:#fffbeb">🎟️</div>
                  <div>
                    <div class="qa-title">Buat Promosi</div>
                    <div class="qa-desc">Diskon, flash sale, voucher</div>
                  </div>
                  <span class="qa-arrow">›</span>
                </button>
                <button class="quick-action-item" onclick="showToast('🏦 Tarik saldo toko')">
                  <div class="qa-icon" style="background:#eff6ff">🏦</div>
                  <div>
                    <div class="qa-title">Tarik Dana</div>
                    <div class="qa-desc">Saldo: Rp 4.800.000</div>
                  </div>
                  <span class="qa-arrow">›</span>
                </button>
                <button class="quick-action-item" onclick="showToast('💬 Balas ulasan pembeli')">
                  <div class="qa-icon" style="background:#fdf4ff">💬</div>
                  <div>
                    <div class="qa-title">Balas Ulasan</div>
                    <div class="qa-desc">4 ulasan belum dibalas</div>
                  </div>
                  <span class="qa-arrow">›</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Low stock -->
        <div class="lowstock-card">
          <div class="lowstock-card-head">
            <h4>⚠️ Stok Rendah</h4>
            <a href="#" style="font-size:12px;color:var(--accent);font-weight:600" onclick="showToast('📦 Halaman produk')">Kelola produk →</a>
          </div>
          <div class="lowstock-row">
            <div class="ls-cover bc1">AH</div>
            <div class="ls-info">
              <div class="ls-title">Atomic Habits</div>
              <div class="ls-cat">Pengembangan Diri · Rp 89.000</div>
            </div>
            <div class="ls-stock-indicator">
              <div class="ls-stock-bar"><div class="ls-stock-bar-fill" style="width:15%;background:#ef4444"></div></div>
              <div class="ls-stock-num">3</div>
              <button class="action-btn warn" onclick="showToast('📦 Form restock Atomic Habits')">+ Restock</button>
            </div>
          </div>
          <div class="lowstock-row">
            <div class="ls-cover bc3">ML</div>
            <div class="ls-info">
              <div class="ls-title">The Midnight Library</div>
              <div class="ls-cat">Fiksi Internasional · Rp 76.000</div>
            </div>
            <div class="ls-stock-indicator">
              <div class="ls-stock-bar"><div class="ls-stock-bar-fill" style="width:25%;background:#f59e0b"></div></div>
              <div class="ls-stock-num" style="background:#fff7ed;color:#d97706">5</div>
              <button class="action-btn warn" onclick="showToast('📦 Form restock Midnight Library')">+ Restock</button>
            </div>
          </div>
          <div class="lowstock-row">
            <div class="ls-cover bc5">PM</div>
            <div class="ls-info">
              <div class="ls-title">Psychology of Money</div>
              <div class="ls-cat">Psikologi & Keuangan · Rp 92.000</div>
            </div>
            <div class="ls-stock-indicator">
              <div class="ls-stock-bar"><div class="ls-stock-bar-fill" style="width:35%;background:#f59e0b"></div></div>
              <div class="ls-stock-num" style="background:#fff7ed;color:#d97706">7</div>
              <button class="action-btn warn" onclick="showToast('📦 Form restock Psychology of Money')">+ Restock</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
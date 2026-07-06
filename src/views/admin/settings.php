<?php
// $sysSettings is injected by page_data()
$s = $sysSettings ?? [];
?>
<div id="page-admin_settings" class="page active">
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
          <button class="sidebar-item" onclick="showPage('admin_analytics')">
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
          <button class="sidebar-item active" onclick="showPage('admin_settings')">
            <span class="si">🛠️</span> Pengaturan Sistem
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
    <div class="dash-content">
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <div class="admin-topbar-title">
            <h2 style="font-size: 24px; color: var(--ink-dark); margin-bottom: 4px;">Pengaturan Sistem</h2>
            <p style="color: var(--ink-muted); font-size: 14px; margin: 0;">Konfigurasi preferensi global, transaksi, dan logistik untuk toko Anda.</p>
          </div>
        </div>
      </div>

      <div class="admin-body">
        <form method="POST" action="index.php?action=save_system_settings">

          <div style="display: flex; gap: 24px; align-items: flex-start; max-width: 960px; width: 100%;">

            <!-- LEFT SETTINGS SIDEBAR (category nav) -->
            <div style="flex: 0 0 220px; background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); padding: 12px; display: flex; flex-direction: column; gap: 4px;">
              <div style="padding: 12px 16px; border-radius: 8px; background: var(--rose-blush); color: var(--rose-deep); font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                <span>🏪</span> Pengaturan Toko
              </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div style="flex: 1; display: flex; flex-direction: column; gap: 20px;">

              <!-- ─── CARD 1: Mata Uang & Lokalisasi ─── -->
              <div style="background:#fff; border-radius:12px; border:1px solid var(--border-soft); overflow:hidden;">
                <div style="padding:16px 24px; border-bottom:1px solid var(--border-soft); border-top:3px solid var(--rose-deep); display:flex; align-items:center; gap:12px;">
                  <span style="font-size:20px">🌐</span>
                  <h3 style="margin:0; font-size:16px; color:var(--ink-dark);">Mata Uang & Lokalisasi</h3>
                </div>
                <div style="padding:24px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                  <div>
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--ink-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">MATA UANG UTAMA</label>
                    <select name="currency" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:6px; color:var(--ink-dark); font-size:14px; outline:none; background:#fff;">
                      <option value="IDR" <?= ($s['currency'] ?? 'IDR') === 'IDR' ? 'selected' : '' ?>>IDR - Rupiah Indonesia</option>
                      <option value="USD" <?= ($s['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD - US Dollar</option>
                    </select>
                  </div>
                  <div>
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--ink-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">ZONA WAKTU</label>
                    <select name="timezone" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:6px; color:var(--ink-dark); font-size:14px; outline:none; background:#fff;">
                      <option value="Asia/Jakarta"  <?= ($s['timezone'] ?? 'Asia/Jakarta')  === 'Asia/Jakarta'  ? 'selected' : '' ?>>(GMT+07:00) WIB - Jakarta</option>
                      <option value="Asia/Makassar" <?= ($s['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>(GMT+08:00) WITA - Makassar</option>
                      <option value="Asia/Jayapura" <?= ($s['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>(GMT+09:00) WIT - Jayapura</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- ─── CARD 2: Manajemen Stok ─── -->
              <div style="background:#fff; border-radius:12px; border:1px solid var(--border-soft); overflow:hidden;">
                <div style="padding:16px 24px; border-bottom:1px solid var(--border-soft); display:flex; align-items:center; gap:12px;">
                  <span style="font-size:20px">📦</span>
                  <h3 style="margin:0; font-size:16px; color:var(--ink-dark);">Manajemen Stok</h3>
                </div>
                <div style="padding:24px; display:flex; flex-direction:column; gap:24px;">

                  <!-- Toggle: low stock alert -->
                  <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                      <div style="font-size:14px; font-weight:600; color:var(--ink-dark); margin-bottom:4px;">Peringatan Stok Rendah Otomatis</div>
                      <div style="font-size:13px; color:var(--ink-muted);">Kirim notifikasi ke admin ketika stok produk mencapai batas minimum.</div>
                    </div>
                    <label class="sys-toggle">
                      <input type="checkbox" name="low_stock_alert" value="1" <?= ($s['low_stock_alert'] ?? '1') === '1' ? 'checked' : '' ?>>
                      <span class="sys-toggle-track"></span>
                    </label>
                  </div>

                  <!-- Threshold input -->
                  <div style="max-width:280px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--ink-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">AMBANG BATAS STOK RENDAH</label>
                    <div style="position:relative; display:flex; align-items:center;">
                      <input type="number" name="low_stock_threshold" min="1" value="<?= (int)($s['low_stock_threshold'] ?? 10) ?>"
                        style="width:100%; padding:10px 50px 10px 14px; border:1px solid var(--border); border-radius:6px; color:var(--ink-dark); font-size:14px; outline:none;">
                      <span style="position:absolute; right:14px; color:var(--ink-muted); font-size:13px; pointer-events:none;">unit</span>
                    </div>
                  </div>

                  <div style="border-top:1px solid var(--border-soft); padding-top:24px;">
                    <!-- Toggle: show stock display -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                      <div>
                        <div style="font-size:14px; font-weight:600; color:var(--ink-dark); margin-bottom:4px;">Tampilkan Sisa Stok di Toko</div>
                        <div style="font-size:13px; color:var(--ink-muted);">Pelanggan dapat melihat jumlah sisa buku saat stok menipis (dibawah 5 unit).</div>
                      </div>
                      <label class="sys-toggle">
                        <input type="checkbox" name="show_stock_display" value="1" <?= ($s['show_stock_display'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="sys-toggle-track"></span>
                      </label>
                    </div>
                  </div>

                </div>
              </div>

              <!-- ─── CARD 3: Pengaturan Transaksi ─── -->
              <div style="background:#fff; border-radius:12px; border:1px solid var(--border-soft); overflow:hidden;">
                <div style="padding:16px 24px; border-bottom:1px solid var(--border-soft); display:flex; align-items:center; gap:12px;">
                  <span style="font-size:20px">🧾</span>
                  <h3 style="margin:0; font-size:16px; color:var(--ink-dark);">Pengaturan Transaksi</h3>
                </div>
                <div style="padding:24px; display:flex; flex-direction:column; gap:24px;">

                  <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                      <label style="display:block; font-size:11px; font-weight:600; color:var(--ink-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">MINIMUM NILAI PESANAN</label>
                      <div style="position:relative;">
                        <span style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:14px; pointer-events:none;">Rp</span>
                        <input type="number" name="min_order" min="0" value="<?= (int)($s['min_order'] ?? 50000) ?>"
                          style="width:100%; padding:10px 14px 10px 38px; border:1px solid var(--border); border-radius:6px; color:var(--ink-dark); font-size:14px; outline:none;">
                      </div>
                      <div style="font-size:12px; color:var(--ink-muted); margin-top:6px;">Set 0 untuk menonaktifkan.</div>
                    </div>
                    <div>
                      <label style="display:block; font-size:11px; font-weight:600; color:var(--ink-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">PAJAK PERTAMBAHAN NILAI (PPN)</label>
                      <div style="position:relative;">
                        <input type="number" name="ppn_rate" min="0" max="100" value="<?= (int)($s['ppn_rate'] ?? 11) ?>"
                          style="width:100%; padding:10px 40px 10px 14px; border:1px solid var(--border); border-radius:6px; color:var(--ink-dark); font-size:14px; outline:none;">
                        <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:14px; pointer-events:none;">%</span>
                      </div>
                    </div>
                  </div>

                  <div style="border-top:1px solid var(--border-soft); padding-top:24px;">
                    <!-- Toggle: ppn included -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                      <div>
                        <div style="font-size:14px; font-weight:600; color:var(--ink-dark); margin-bottom:4px;">Harga Termasuk Pajak</div>
                        <div style="font-size:13px; color:var(--ink-muted);">Jika diaktifkan, harga produk di etalase sudah meliputi kalkulasi PPN.</div>
                      </div>
                      <label class="sys-toggle">
                        <input type="checkbox" name="ppn_included" value="1" <?= ($s['ppn_included'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="sys-toggle-track"></span>
                      </label>
                    </div>
                  </div>

                </div>
              </div>

              <!-- ─── SAVE BUTTON ─── -->
              <div style="display:flex; gap:12px; padding:4px 0 32px;">
                <button type="submit"
                  style="background: var(--rose-deep); color:#fff; border:none; padding:12px 28px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 2px 8px rgba(var(--rose-rgb,200,50,80),0.25);"
                  onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                  <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                  Simpan Perubahan
                </button>
                <a href="index.php?page=admin_settings"
                  style="background:#fff; color:var(--ink-mid); border:1px solid var(--border); padding:12px 22px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; text-decoration:none; display:flex; align-items:center; gap:8px;">
                  ✕ Reset
                </a>
              </div>

            </div><!-- /RIGHT -->
          </div><!-- /flex -->

        </form>
      </div>
    </div>
  </div>
</div>

<style>
/* Toggle switch */
.sys-toggle {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
  flex-shrink: 0;
}
.sys-toggle input { opacity: 0; width: 0; height: 0; }
.sys-toggle-track {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background: var(--border);
  border-radius: 24px;
  transition: 0.3s;
}
.sys-toggle input:checked + .sys-toggle-track {
  background: var(--rose-deep);
}
.sys-toggle-track::before {
  content: '';
  position: absolute;
  height: 18px; width: 18px;
  left: 3px; bottom: 3px;
  background: #fff;
  border-radius: 50%;
  transition: 0.3s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}
.sys-toggle input:checked + .sys-toggle-track::before {
  transform: translateX(20px);
}
</style>

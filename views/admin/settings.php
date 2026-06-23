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

    <!-- MAIN CONTENT -->
    <div class="dash-content">

      <!-- Admin topbar with Tabs -->
      <div class="admin-topbar">
        <div class="admin-topbar-left" style="display:flex; align-items:center; gap:32px;">
          <div class="admin-topbar-title">
            <h2>⚙️ Pengaturan Sistem</h2>
          </div>
          <!-- Navigation Tabs -->
          <div class="admin-settings-tabs">
            <span class="settings-tab active">Umum</span>
            <span class="settings-tab" onclick="showToast('🛡️ Halaman Keamanan')">Keamanan</span>
            <span class="settings-tab" onclick="showToast('🔌 Halaman Integrasi')">Integrasi</span>
          </div>
        </div>
      </div>

      <!-- Admin body -->
      <div class="admin-body">
        
        <form action="index.php?action=update_admin_settings" method="POST" class="settings-grid">
          
          <!-- LEFT COLUMN: Forms -->
          <div class="settings-left-col">
            
            <!-- Profil Administrator Card -->
            <div class="settings-card">
              <div class="settings-card-head">
                <div>
                  <h3 class="settings-card-title">Profil Administrator</h3>
                  <p class="settings-card-subtitle">Kelola informasi identitas akun utama Anda.</p>
                </div>
                <button type="submit" class="btn-admin-primary btn-save-profile">
                  Simpan Profil <span style="margin-left:4px">→</span>
                </button>
              </div>
              
              <div class="settings-card-body">
                <div class="profile-setup-section">
                  <!-- Avatar area -->
                  <div class="avatar-setup-wrap">
                    <div class="avatar-default-box">
                      <!-- Standard admin emoji icon placeholder -->
                      <span class="avatar-default-icon">👤</span>
                    </div>
                    <div class="avatar-edit-badge" onclick="showToast('📸 Fitur ganti foto profil')">
                      ✏️
                    </div>
                  </div>
                  
                  <!-- Form Fields Right of Avatar -->
                  <div class="profile-fields-inline">
                    <div class="form-row">
                      <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="settings-input" value="<?= e($admin['name'] ?? 'Administrator Utama') ?>" required>
                      </div>
                      <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="title" class="settings-input" value="<?= e($admin['title'] ?? 'Super Admin Rubby') ?>">
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="form-row" style="margin-top: 16px;">
                  <div class="form-group">
                    <label>Email Administrator</label>
                    <input type="email" name="email" class="settings-input" value="<?= e($admin['email'] ?? 'admin@rubbybooks.id') ?>" required>
                  </div>
                  <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="phone" class="settings-input" value="<?= e($admin['phone'] ?? '+62 812-3456-7890') ?>">
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Konfigurasi Platform Card -->
            <div class="settings-card" style="margin-top: 20px;">
              <div class="settings-card-head" style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
                <div>
                  <h3 class="settings-card-title">Konfigurasi Platform</h3>
                  <p class="settings-card-subtitle">Pengaturan global untuk identitas toko buku digital.</p>
                </div>
              </div>
              
              <div class="settings-card-body">
                <div class="form-group">
                  <label>Nama Situs / Brand</label>
                  <input type="text" name="site_name" class="settings-input" value="<?= e($settings['site_name'] ?? 'Rubby Books Official') ?>" required>
                </div>
                
                <div class="form-row" style="margin-top: 16px;">
                  <div class="form-group">
                    <label>Email Kontak Support</label>
                    <input type="email" name="support_email" class="settings-input" value="<?= e($settings['support_email'] ?? 'support@rubbybooks.id') ?>" required>
                  </div>
                  <div class="form-group">
                    <label>Zona Waktu</label>
                    <select name="timezone" class="settings-select">
                      <option value="Asia/Jakarta (WIB)" <?= ($settings['timezone'] ?? '') === 'Asia/Jakarta (WIB)' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                      <option value="Asia/Makassar (WITA)" <?= ($settings['timezone'] ?? '') === 'Asia/Makassar (WITA)' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                      <option value="Asia/Jayapura (WIT)" <?= ($settings['timezone'] ?? '') === 'Asia/Jayapura (WIT)' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                    </select>
                  </div>
                </div>
                
                <!-- Toggle Switches -->
                <div class="toggle-list" style="margin-top: 24px;">
                  <div class="toggle-item">
                    <div class="toggle-text">
                      <div class="toggle-label">Mode Maintenance</div>
                      <div class="toggle-sub">Hanya admin yang bisa mengakses platform saat aktif.</div>
                    </div>
                    <label class="switch-control">
                      <input type="checkbox" name="maintenance_mode" value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                      <span class="switch-slider"></span>
                    </label>
                  </div>
                  
                  <div class="toggle-item" style="margin-top: 16px;">
                    <div class="toggle-text">
                      <div class="toggle-label">Registrasi User Baru</div>
                      <div class="toggle-sub">Izinkan pengunjung baru untuk mendaftar akun.</div>
                    </div>
                    <label class="switch-control">
                      <input type="checkbox" name="user_registration" value="1" <?= ($settings['user_registration'] ?? '1') === '1' ? 'checked' : '' ?>>
                      <span class="switch-slider"></span>
                    </label>
                  </div>
                </div>
                
              </div>
            </div>
            
          </div>
          
          <!-- RIGHT COLUMN: System Notifications -->
          <div class="settings-right-col">
            
            <div class="settings-card right-notif-card" style="height: 100%; display: flex; flex-direction: column;">
              <div class="settings-card-head" style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 class="settings-card-title">Notifikasi Sistem</h3>
                <span class="notif-count-badge">5 Baru</span>
              </div>
              
              <div class="settings-notif-list" style="flex: 1; display: flex; flex-direction: column; gap: 12px;">
                
                <!-- Notif 1 -->
                <div class="sys-notif-item">
                  <div class="sys-notif-icon-wrap green">
                    💾
                  </div>
                  <div class="sys-notif-content">
                    <div class="sys-notif-meta">
                      <span class="sys-notif-title">Vendor Baru Terdaftar</span>
                      <span class="sys-notif-time">2 Menit</span>
                    </div>
                    <p class="sys-notif-text">"Pustaka Ilmu" baru saja mendaftar sebagai vendor buku. Perlu verifikasi.</p>
                    <span class="sys-notif-status-pill">Pending</span>
                  </div>
                </div>
                
                <!-- Notif 2 -->
                <div class="sys-notif-item red-highlight">
                  <div class="sys-notif-icon-wrap red">
                    ⚠
                  </div>
                  <div class="sys-notif-content">
                    <div class="sys-notif-meta">
                      <span class="sys-notif-title">Database Latency</span>
                      <span class="sys-notif-time">15 Menit</span>
                    </div>
                    <p class="sys-notif-text">Terdeteksi lonjakan beban pada server database US-West-1. Monitoring aktif.</p>
                  </div>
                </div>
                
                <!-- Notif 3 -->
                <div class="sys-notif-item">
                  <div class="sys-notif-icon-wrap blue">
                    📦
                  </div>
                  <div class="sys-notif-content">
                    <div class="sys-notif-meta">
                      <span class="sys-notif-title">Pesanan Besar Masuk</span>
                      <span class="sys-notif-time">1 Jam</span>
                    </div>
                    <p class="sys-notif-text">ID #78292: Pembelian 150 eks. "Sejarah Nusantara" telah dibayar.</p>
                  </div>
                </div>
                
                <!-- Notif 4 -->
                <div class="sys-notif-item">
                  <div class="sys-notif-icon-wrap orange">
                    📅
                  </div>
                  <div class="sys-notif-content">
                    <div class="sys-notif-meta">
                      <span class="sys-notif-title">Stok Menipis</span>
                      <span class="sys-notif-time">3 Jam</span>
                    </div>
                    <p class="sys-notif-text">Buku "Algoritma Pemrograman" tersisa 5 eks. Hubungi vendor.</p>
                  </div>
                </div>
                
                <!-- Notif 5 -->
                <div class="sys-notif-item">
                  <div class="sys-notif-icon-wrap green-light">
                    ✅
                  </div>
                  <div class="sys-notif-content">
                    <div class="sys-notif-meta">
                      <span class="sys-notif-title">Backup Sukses</span>
                      <span class="sys-notif-time">Kemarin</span>
                    </div>
                    <p class="sys-notif-text">Cadangan database mingguan telah berhasil disimpan ke Cloud Storage.</p>
                  </div>
                </div>
                
              </div>
              
              <!-- Footer Action -->
              <div class="sys-notif-footer" style="margin-top: auto; padding-top: 16px; text-align: center; border-top: 1px solid var(--border-soft);">
                <a href="#" class="sys-notif-link" onclick="showPage('admin_notifications'); return false;">Lihat Semua Riwayat</a>
              </div>
              
            </div>
            
          </div>
          
        </form>

      </div>
    </div>
  </div>
</div>

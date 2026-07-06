<div id="page-admin_categories" class="page active">
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
          <button class="sidebar-item active" onclick="showPage('admin_categories')">
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
          <button class="sidebar-item" onclick="showPage('admin_settings')">
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

      <!-- Admin topbar -->
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <div class="admin-topbar-title">
            <h2>🏷️ Kelola Kategori</h2>
            <p>Manajemen kategori buku untuk klasifikasi katalog</p>
          </div>
        </div>
        <div class="admin-topbar-right" style="display:flex; align-items:center; gap:16px;">
          <form method="GET" action="index.php" style="display:flex; gap:8px;">
            <input type="hidden" name="page" value="admin_categories">
            <input type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari kategori..." style="padding:8px 12px; border:1px solid var(--border-soft); border-radius:6px; font-size:14px; width:250px;" required>
            <button class="btn-primary" style="padding:8px 16px;">Cari</button>
            <?php if (!empty($q)): ?>
              <a href="index.php?page=admin_categories" class="btn-secondary" style="padding:8px 12px; text-decoration:none;">Reset</a>
            <?php endif; ?>
          </form>
          <span style="font-size:12px;color:var(--ink-muted);">Total: <strong><?= count($categories) ?> kategori</strong></span>
        </div>
      </div>

      <!-- Admin body -->
      <div class="admin-body">

        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px; align-items: start;">
          <!-- Kiri: Tambah Kategori -->
          <div class="admin-table-card" style="padding: 24px;">
            <h4 style="margin-bottom: 8px;">Tambah Kategori Baru</h4>
            <p style="font-size: 13px; color: var(--ink-muted); margin-bottom: 20px;">Tambahkan klasifikasi kategori baru untuk katalog buku.</p>
            <form method="post" action="index.php?page=admin_categories">
              <input type="hidden" name="action" value="save_category">
              <div class="form-group" style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom: 6px; font-weight:600; font-size:13px; color:var(--ink);">Nama Kategori</label>
                <input class="settings-input" name="name" required style="width:100%;">
              </div>
              <div class="form-group" style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom: 6px; font-weight:600; font-size:13px; color:var(--ink);">Deskripsi</label>
                <textarea class="settings-input" name="description" style="width:100%; height:80px; resize:none;"></textarea>
              </div>
              <button class="btn-admin-primary" style="width:100%;">Simpan Kategori</button>
            </form>
          </div>

          <!-- Kanan: Daftar Kategori -->
          <div class="admin-table-card">
            <div class="admin-table-head">
              <div>
                <h4>Daftar Kategori</h4>
                <p>Semua kategori buku yang aktif</p>
              </div>
            </div>
            <div class="admin-table-responsive">
              <table class="admin-data-table">
                <thead>
                  <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th style="width:80px; text-align:center;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($categories as $category): ?>
                    <tr>
                      <td style="font-weight:600; color:var(--accent);"><?= e($category['name']) ?></td>
                      <td style="color:var(--ink-muted);"><?= e($category['description']) ?></td>
                      <td style="text-align:center;">
                        <form method="post" style="display:inline;" onsubmit="return confirm('Hapus kategori <?= e($category['name']) ?>? (Pastikan tidak ada produk yang menggunakan kategori ini)')">
                          <input type="hidden" name="action" value="delete_category">
                          <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                          <button class="admin-action-btn danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
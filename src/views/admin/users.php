<div id="page-admin_users" class="page active">
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
          <button class="sidebar-item active" onclick="showPage('admin_users')">
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
            <h2>👥 Kelola User</h2>
            <p>Manajemen akun pembeli, penjual, dan verifikasi</p>
          </div>
        </div>
        <div class="admin-topbar-right" style="display:flex; align-items:center; gap:16px;">
          <form method="GET" action="index.php" style="display:flex; gap:8px;">
            <input type="hidden" name="page" value="admin_users">
            <input type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari nama/email..." style="padding:8px 12px; border:1px solid var(--border-soft); border-radius:6px; font-size:14px; width:250px;" required>
            <button class="btn-primary" style="padding:8px 16px;">Cari</button>
            <?php if (!empty($q)): ?>
              <a href="index.php?page=admin_users" class="btn-secondary" style="padding:8px 12px; text-decoration:none;">Reset</a>
            <?php endif; ?>
          </form>
          <span style="font-size:12px;color:var(--ink-muted);">Total: <strong><?= count($users) ?> pengguna</strong></span>
        </div>
      </div>

      <!-- Admin body -->
      <div class="admin-body">

        <!-- Pending deletion requests alert -->
        <?php $pendingDeletes = array_filter($users, fn($u) => !empty($u['delete_requested'])); ?>
        <?php if (!empty($pendingDeletes)): ?>
        <div class="pending-verif-card" style="border-top:4px solid #ef4444; background:linear-gradient(135deg,#fff1f2,#fce7f3);">
          <span class="pv-icon">🗑️</span>
          <div class="pv-text">
            <strong style="color:#991b1b;"><?= count($pendingDeletes) ?> permintaan penghapusan akun menunggu persetujuan</strong>
            <p style="color:#dc2626;">Tinjau dan proses permintaan hapus akun dari pengguna.</p>
          </div>
        </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="admin-table-card">
          <div class="admin-table-head">
            <div>
              <h4>Daftar Pengguna</h4>
              <p>Semua akun yang terdaftar di platform RubbyBooks</p>
            </div>
          </div>
          <div class="admin-table-responsive">
            <table class="admin-data-table">
              <thead>
                <tr>
                  <th>Pengguna</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Bergabung</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $userRow): ?>
                <?php
                  $roleClass = match($userRow['role']) {
                    'seller' => 'sp-seller',
                    'admin'  => 'sp-active',
                    default  => 'sp-buyer',
                  };
                  $statusClass = match($userRow['status']) {
                    'active' => 'sp-active',
                    'suspended' => 'sp-inactive',
                    'banned' => 'sp-inactive',
                    default  => 'sp-verify',
                  };
                  $avatarColors = ['#be185d','#7c3aed','#0369a1','#047857','#b45309','#9f1239'];
                  $avatarColor = $avatarColors[$userRow['id'] % count($avatarColors)];
                  $initials = strtoupper(substr($userRow['name'], 0, 2));
                ?>
                <tr>
                  <td>
                    <div class="td-user">
                      <div class="td-user-avatar" style="background:<?= $avatarColor ?>"><?= $initials ?></div>
                      <div>
                        <div class="td-user-name">
                          <?= e($userRow['name']) ?>
                          <?php if (!empty($userRow['delete_requested'])): ?>
                            <span style="background:#fef3c7;color:#92400e;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px;margin-left:6px;">MINTA HAPUS</span>
                          <?php endif; ?>
                        </div>
                        <div class="td-user-email"><?= e($userRow['email']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="status-pill <?= $roleClass ?>"><?= ucfirst(e($userRow['role'])) ?></span></td>
                  <td><span class="status-pill <?= $statusClass ?>"><?= ucfirst(e($userRow['status'])) ?></span></td>
                  <td style="font-size:12px;color:var(--ink-muted);"><?= !empty($userRow['created_at']) ? date('d M Y', strtotime($userRow['created_at'])) : '-' ?></td>
                  <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                      <?php if (!empty($userRow['delete_requested'])): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Yakin menyetujui penghapusan akun ini?')">
                          <input type="hidden" name="action" value="approve_delete_user">
                          <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                          <button class="admin-action-btn danger">Setuju Hapus</button>
                        </form>
                        <form method="post" style="display:inline;">
                          <input type="hidden" name="action" value="reject_delete_user">
                          <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                          <button class="admin-action-btn">Tolak</button>
                        </form>
                      <?php endif; ?>
                      <?php if ($userRow['role'] === 'seller' && $userRow['status'] === 'pending'): ?>
                        <form method="post" style="display:inline;">
                          <input type="hidden" name="action" value="approve_seller">
                          <input type="hidden" name="seller_id" value="<?= $userRow['id'] ?>">
                          <button class="admin-action-btn success">Verifikasi</button>
                        </form>
                      <?php endif; ?>
                      <?php if ($userRow['role'] !== 'admin'): ?>
                        <?php if ($userRow['status'] === 'active'): ?>
                          <form method="post" style="display:inline;" onsubmit="return confirm('Suspend user <?= e($userRow['name']) ?>?')">
                            <input type="hidden" name="action" value="suspend_user">
                            <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                            <button class="admin-action-btn" style="background:#f59e0b;color:#fff;">Suspend</button>
                          </form>
                        <?php elseif (in_array($userRow['status'], ['suspended', 'banned'])): ?>
                          <form method="post" style="display:inline;" onsubmit="return confirm('Aktifkan user <?= e($userRow['name']) ?>?')">
                            <input type="hidden" name="action" value="activate_user">
                            <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                            <button class="admin-action-btn success">Aktifkan</button>
                          </form>
                        <?php endif; ?>
                        
                        <form method="post" style="display:inline;" onsubmit="return confirm('Hapus permanen user <?= e($userRow['name']) ?>? (Aksi ini akan menghapus semua produk, ulasan, pesanan dll yang terkait)')">
                          <input type="hidden" name="action" value="delete_user">
                          <input type="hidden" name="user_id" value="<?= $userRow['id'] ?>">
                          <button class="admin-action-btn danger">Delete</button>
                        </form>
                      <?php endif; ?>
                    </div>
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

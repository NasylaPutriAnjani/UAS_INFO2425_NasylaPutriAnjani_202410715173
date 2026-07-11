<?php
$user    = $user ?? current_user();
$role    = $user['role'] ?? 'buyer';

$nameParts = explode(' ', trim($user['name'] ?? ''));
$firstName = $nameParts[0] ?? '';
$lastName  = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
if (empty($initials)) $initials = 'U';

$backPage = match($role) {
    'seller' => 'seller',
    'admin'  => 'admin',
    default  => 'buyer',
};
?>
<div id="page-account_settings" class="page active<?= $role === 'admin' ? ' admin-page' : '' ?>">
  <div class="dash-layout<?= $role === 'admin' ? ' admin-layout' : '' ?>">

    <!-- ══════ SIDEBAR ══════ -->
    <?php if ($role === 'buyer'): ?>
      <?php $buyerMenu = 'account'; require __DIR__ . '/../buyer/partials/sidebar.php'; ?>

    <?php elseif ($role === 'seller'): ?>
      <?php
        $sellerIdForSidebar  = $user['id'];
        $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
        $sellerNavCounts = user_nav_counts($GLOBALS['pdo']);
        $sellerOrderBadgeCount = (int)($sellerNavCounts['orders'] ?? 0);
        $sellerUnreadNotifCount = (int)($sellerNavCounts['notifications'] ?? 0);
      ?>
      <aside class="dash-sidebar seller-sidebar">
        <div class="sidebar-store-profile">
          <?= user_avatar_html($user, 'sidebar-store-avatar', 'S') ?>
          <div>
            <div class="sidebar-store-name"><?= e($user['name']) ?></div>
            <div class="sidebar-store-status">Toko Aktif</div>
          </div>
        </div>
        <nav class="sidebar-nav">
          <div class="sidebar-group">
            <div class="sidebar-group-label">Menu Utama</div>
            <button class="sidebar-item" onclick="showPage('seller')"><span class="si">📊</span> Dashboard</button>
            <button class="sidebar-item" onclick="showPage('seller_products')"><span class="si">📦</span> Produk Saya <span class="sidebar-badge"><?= $activeProductsCount ?></span></button>
            <button class="sidebar-item" onclick="showPage('seller_orders')"><span class="si">🛒</span> Pesanan Masuk<?php if ($sellerOrderBadgeCount > 0): ?><span class="sidebar-badge"><?= $sellerOrderBadgeCount ?></span><?php endif; ?></button>
            <button class="sidebar-item" onclick="showPage('seller_reviews')"><span class="si">💬</span> Ulasan &amp; Rating</button>
            <button class="sidebar-item" onclick="showPage('seller_notifications')"><span class="si">🔔</span> Notifikasi<?php if ($sellerUnreadNotifCount > 0): ?><span class="sidebar-badge warn"><?= $sellerUnreadNotifCount ?></span><?php endif; ?></button>
          </div>
          <div class="sidebar-group">
            <div class="sidebar-group-label">Keuangan</div>
            <button class="sidebar-item" onclick="showPage('seller_report')"><span class="si">💰</span> Laporan Penjualan</button>
          </div>
          <div class="sidebar-group">
            <div class="sidebar-group-label">Pengaturan</div>
            <button class="sidebar-item active"><span class="si">⚙️</span> Pengaturan Akun</button>
          </div>
        </nav>
        <div class="sidebar-footer">
          <button class="sidebar-item" onclick="doLogout()" style="color:#dc2626;width:100%"><span class="si">🚪</span> Keluar</button>
        </div>
      </aside>

    <?php elseif ($role === 'admin'): ?>
      <?php $adminActivePage = 'account_settings'; require __DIR__ . '/../admin/partials/sidebar.php'; ?>
    <?php endif; ?>

    <!-- ══════ MAIN CONTENT ══════ -->
    <div class="dash-content">
      <div class="profile-container">

        <h1 class="profile-title">My Profile</h1>

        <!-- TAB NAVIGATION -->
        <div class="profile-tabs" id="profile-tabs">
          <button type="button" class="tab-btn active" onclick="switchAccountTab(this,'tab-basic-info')">Basic Info</button>
          <button type="button" class="tab-btn"        onclick="switchAccountTab(this,'tab-password-change')">Password Change</button>
          <button type="button" class="tab-btn"        onclick="switchAccountTab(this,'tab-delete-account')">Delete Account</button>
        </div>

        <!-- ══ TAB 1: BASIC INFO ══ -->
        <div id="tab-basic-info" class="tab-pane active">
          <form method="POST" action="index.php?action=update_account&page=<?= e($page ?? 'account_settings') ?>" enctype="multipart/form-data">
            <input type="hidden" name="action_type" value="save">
            <input type="hidden" name="delete_avatar" value="0">

            <!-- Avatar row -->
            <div class="acc-avatar-row">
              <?= user_avatar_html($user, 'acc-avatar-circle', $initials) ?>
              <div>
                <div style="font-weight:700;font-size:15px;color:var(--ink-dark);margin-bottom:4px;">Profile picture</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-bottom:10px;">Upload foto profil agar tampil di header, sidebar, dan profil publik sesuai role.</div>
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" style="font-size:13px;">
                <?php if (!empty($user['avatar'])): ?>
                  <button type="button" class="btn-outline-gray" style="margin-left:8px;padding:8px 12px;" onclick="this.form.delete_avatar.value='1';this.form.submit();">Hapus Foto</button>
                <?php endif; ?>
              </div>
            </div>

            <!-- Fields -->
            <div class="fields-grid">
              <div class="form-field">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= e($firstName) ?>" placeholder="First Name">
              </div>
              <div class="form-field">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= e($lastName) ?>" placeholder="Last Name">
              </div>
              <div class="form-field">
                <label>Email Address</label>
                <input type="email" value="<?= e($user['email']) ?>" readonly class="readonly-field">
              </div>
              <div class="form-field">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="+62...">
              </div>
              <div class="form-field">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= e($user['dob'] ?? '') ?>">
              </div>
              <div class="form-field">
                <label>Alternate Mobile</label>
                <input type="text" name="alternate_phone" value="<?= e($user['alternate_phone'] ?? '') ?>" placeholder="Alternative number">
              </div>
            </div>

            <div class="tab-footer">
              <button type="submit" class="btn-primary-rose">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Save Details
              </button>
              <button type="button" class="btn-outline-gray" onclick="window.location.href='index.php?page=<?= e($backPage) ?>'">✕ Cancel</button>
            </div>
          </form>
        </div><!-- /tab-basic-info -->

        <!-- ══ TAB 2: PASSWORD CHANGE ══ -->
        <div id="tab-password-change" class="tab-pane">
          <form method="POST" action="index.php?action=update_account&page=<?= e($page ?? 'account_settings') ?>">
            <input type="hidden" name="action_type" value="password_update">

            <div class="fields-grid" style="grid-template-columns:1fr;max-width:520px;">
              <div class="form-field">
                <label>Current Password</label>
                <input type="password" name="password_current" placeholder="Masukkan password saat ini">
              </div>
              <div class="form-field">
                <label>New Password</label>
                <input type="password" name="password" id="new-password" placeholder="Masukkan password baru" oninput="checkPasswordStrength(this.value)">
                <!-- strength bar -->
                <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                  <div style="display:flex;gap:4px;flex:1;max-width:220px;">
                    <div id="pwd-bar-1" class="pwd-bar"></div>
                    <div id="pwd-bar-2" class="pwd-bar"></div>
                    <div id="pwd-bar-3" class="pwd-bar"></div>
                    <div id="pwd-bar-4" class="pwd-bar"></div>
                  </div>
                  <span style="font-size:12px;color:var(--ink-muted);">Kekuatan: <span id="pwd-text">—</span></span>
                </div>
              </div>
              <div class="form-field">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirm" placeholder="Ulangi password baru">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:10px;font-size:13px;">
                  <div id="rule-len"   class="pwd-rule">• Minimal 8 karakter</div>
                  <div id="rule-upper" class="pwd-rule">• Mengandung huruf besar</div>
                  <div id="rule-num"   class="pwd-rule">• Mengandung angka</div>
                  <div id="rule-sym"   class="pwd-rule">• Mengandung simbol (!@#$%)</div>
                </div>
              </div>
            </div>

            <div class="tab-footer">
              <button type="submit" class="btn-primary-rose">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Save Password
              </button>
              <button type="button" class="btn-outline-gray" onclick="window.location.href='index.php?page=<?= e($backPage) ?>'">✕ Cancel</button>
            </div>
          </form>
        </div><!-- /tab-password-change -->

        <!-- ══ TAB 3: DELETE ACCOUNT ══ -->
        <div id="tab-delete-account" class="tab-pane">
          <form method="POST" action="index.php?action=update_account&page=<?= e($page ?? 'account_settings') ?>">
            <?php if (($user['delete_requested'] ?? 0) == 1): ?>
              <input type="hidden" name="action_type" value="cancel_delete_account">
              <div class="warning-box" style="background:#fffbeb;border:1px solid #fbbf24;border-radius:10px;padding:24px;margin-bottom:24px;">
                <h3 style="color:#d97706;margin:0 0 12px;font-size:16px;">⚠️ Permintaan Penghapusan Terkirim</h3>
                <p style="color:#92400e;font-size:14px;margin:0;line-height:1.6;">Permintaan penghapusan akun Anda sedang menunggu verifikasi Admin. Anda masih dapat membatalkan permintaan ini.</p>
              </div>
              <div class="tab-footer">
                <button type="submit" style="background:#d97706;color:#fff;border:none;padding:11px 22px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;">
                  🔄 Batalkan Permintaan Penghapusan
                </button>
              </div>
            <?php else: ?>
              <input type="hidden" name="action_type" value="request_delete_account">

              <!-- Warning box -->
              <div style="background:var(--rose-blush);border:1px solid var(--rose-pale);border-radius:10px;padding:24px;margin-bottom:28px;">
                <h3 style="color:var(--rose-deep);margin:0 0 14px;font-size:15px;font-weight:700;">⚠️ Tindakan ini tidak dapat dibatalkan</h3>
                <ul style="color:var(--rose-deep);margin:0;padding-left:20px;font-size:14px;line-height:1.8;">
                  <li>Semua data profil, riwayat transaksi, dan pesan Anda akan dihapus secara permanen.</li>
                  <li>Buku yang sedang Anda jual akan diturunkan dari katalog secara otomatis.</li>
                  <li>Saldo atau poin yang belum digunakan akan hangus dan tidak dapat dikembalikan.</li>
                  <li>Anda tidak dapat menggunakan email yang sama untuk mendaftar ulang selama 30 hari.</li>
                </ul>
              </div>

              <!-- Confirmation input -->
              <div class="form-field" style="max-width:520px;margin-bottom:20px;">
                <label style="font-weight:700;color:var(--ink-dark);">Ketik "DELETE" untuk konfirmasi</label>
                <p style="font-size:13px;color:var(--ink-muted);margin:4px 0 10px;">Ini membantu memastikan Anda tidak menghapus akun secara tidak sengaja.</p>
                <input type="text" name="delete_confirm_text" id="delete-confirm-input" placeholder="DELETE" oninput="checkDeleteStatus()">
              </div>

              <!-- Checkbox -->
              <div style="max-width:520px;margin-bottom:28px;">
                <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;font-size:14px;color:var(--ink-mid);line-height:1.5;">
                  <input type="checkbox" id="delete-checkbox" onchange="checkDeleteStatus()" style="margin-top:3px;width:17px;height:17px;accent-color:var(--rose-deep);flex-shrink:0;">
                  <span>Saya memahami bahwa akun ini akan dihapus secara permanen dan tidak dapat dipulihkan kembali.</span>
                </label>
              </div>

              <div class="tab-footer">
                <button type="submit" id="btn-delete-account" disabled
                  style="background:var(--rose-pale);color:var(--rose-deep);border:none;padding:11px 22px;border-radius:8px;font-size:14px;font-weight:700;cursor:not-allowed;display:flex;align-items:center;gap:8px;transition:0.2s;opacity:0.6;">
                  🗑️ Delete My Account
                </button>
                <button type="button" class="btn-outline-gray" onclick="window.location.href='index.php?page=<?= e($backPage) ?>'">✕ Cancel</button>
              </div>
            <?php endif; ?>
          </form>
        </div><!-- /tab-delete-account -->

      </div><!-- /profile-container -->
    </div><!-- /dash-content -->

  </div>
</div>

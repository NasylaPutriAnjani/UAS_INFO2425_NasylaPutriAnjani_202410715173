<?php
$user = $user ?? current_user();
$role = $user['role'] ?? 'buyer';

// Split name to first & last name
$nameParts = explode(' ', trim($user['name'] ?? ''));
$firstName = $nameParts[0] ?? '';
$lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

// Get initial for avatar placeholder
$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
if (empty($initials)) {
    $initials = 'U';
}

$backPage = match($role) {
    'seller' => 'seller',
    'admin'  => 'admin',
    default  => 'buyer',
};
?>
<div id="page-account_settings" class="page active">
  <div class="dash-layout">
    
    <!-- SIDEBAR SELECTION BASED ON ROLE -->
    <?php if ($role === 'buyer'): ?>
      <?php 
      $buyerMenu = 'account_settings'; 
      require __DIR__ . '/../buyer/partials/sidebar.php'; 
      ?>
    <?php elseif ($role === 'seller'): ?>
      <?php
      $currentSellerPage = 'account_settings';
      $sellerIdForSidebar = $user['id'];
      $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
      ?>
      <aside class="dash-sidebar seller-sidebar">
        <div class="sidebar-store-profile">
          <div class="sidebar-store-avatar">🏪</div>
          <div>
            <div class="sidebar-store-name"><?= e($user['name']) ?></div>
            <div class="sidebar-store-status">Toko Aktif</div>
          </div>
        </div>
        <nav class="sidebar-nav">
          <div class="sidebar-group">
            <div class="sidebar-group-label">Menu Utama</div>
            <button class="sidebar-item" onclick="showPage('seller')">
              <span class="si">📊</span> Dashboard
            </button>
            <button class="sidebar-item" onclick="showPage('seller_products')">
              <span class="si">📦</span> Produk Saya
              <span class="sidebar-badge"><?= $activeProductsCount ?></span>
            </button>
            <button class="sidebar-item" onclick="showPage('seller_orders')">
              <span class="si">🛒</span> Pesanan Masuk
            </button>
            <button class="sidebar-item" onclick="showPage('seller_reviews')">
              <span class="si">💬</span> Ulasan & Rating
            </button>
            <button class="sidebar-item" onclick="showPage('seller_notifications')">
              <span class="si">🔔</span> Notifikasi
            </button>
          </div>
          <div class="sidebar-group">
            <div class="sidebar-group-label">Keuangan</div>
            <button class="sidebar-item" onclick="showPage('seller_report')">
              <span class="si">💰</span> Laporan Penjualan
            </button>
          </div>
          <div class="sidebar-group">
            <div class="sidebar-group-label">Pengaturan</div>
            <button class="sidebar-item active" onclick="showPage('account_settings')">
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
    <?php elseif ($role === 'admin'): ?>
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
            <button class="sidebar-item active" onclick="showPage('account_settings')">
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
    <?php endif; ?>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="dash-content">
      <div class="profile-container">
        
        <h1 class="profile-title">My Profile</h1>

        <!-- NAVIGATION TABS -->
        <div class="profile-tabs">
          <button type="button" class="tab-btn active" onclick="switchTab(event, 'tab-basic-info')">Basic Info</button>
          <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-password-change')">Password Change</button>
          <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-delete-account')">Delete Account</button>
        </div>

        <form id="profileForm" class="profile-form" method="POST" action="index.php?action=update_account" enctype="multipart/form-data">
          <input type="hidden" name="delete_avatar" id="delete-avatar-input" value="0">
          
          <!-- TAB 1: BASIC INFO -->
          <div id="tab-basic-info" class="tab-pane active">
            <!-- Profile Picture Section -->
            <div class="avatar-section">
              <div class="avatar-container">
                <div id="avatar-placeholder" class="profile-avatar-placeholder">
                  <?= e($initials) ?>
                </div>
              </div>
              <div class="avatar-details">
                <h3>Profile picture</h3>
                <p>Foto profil default menggunakan inisial nama Anda.</p>
              </div>
            </div>

            <!-- Fields Grid -->
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
                <input type="email" value="<?= e($user['email']) ?>" placeholder="email@example.com" readonly class="readonly-field">
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
                <label>Alternate mobile details</label>
                <input type="text" name="alternate_phone" value="<?= e($user['alternate_phone'] ?? '') ?>" placeholder="Alternative number">
              </div>
            </div>

            <div class="profile-actions" style="margin-top: 32px; border-top: 1px solid var(--border-soft); padding-top: 24px; display:flex; gap:12px;">
              <button type="submit" name="action_type" value="save" class="btn-save-details" style="background: var(--rose-deep); color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 12 4 10"/></svg>
                Save Details
              </button>
              <button type="button" class="btn-cancel-details" onclick="window.location.href = 'index.php?page=<?= e($backPage) ?>'" style="background: #fff; border:1px solid var(--border); padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600; color:var(--ink-mid);">
                ✕ Cancel
              </button>
            </div>
          </div>

          <!-- TAB 2: PASSWORD CHANGE -->
          <div id="tab-password-change" class="tab-pane">
            <div class="fields-grid" style="grid-template-columns: 1fr; max-width: 600px;">
              <div class="form-field">
                <label>Current Password</label>
                <input type="password" name="password_current" placeholder="Masukkan password saat ini">
              </div>
              <div class="form-field">
                <label>New Password</label>
                <input type="password" name="password" id="new-password" placeholder="Masukkan password baru" onkeyup="checkPasswordStrength(this.value)">
                <div class="pwd-strength" style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                  <div class="pwd-bars" style="display:flex; gap:4px; flex:1; max-width: 250px;">
                    <div class="pwd-bar" id="pwd-bar-1" style="height:4px; background:var(--border); flex:1; border-radius:2px; transition:0.3s"></div>
                    <div class="pwd-bar" id="pwd-bar-2" style="height:4px; background:var(--border); flex:1; border-radius:2px; transition:0.3s"></div>
                    <div class="pwd-bar" id="pwd-bar-3" style="height:4px; background:var(--border); flex:1; border-radius:2px; transition:0.3s"></div>
                    <div class="pwd-bar" id="pwd-bar-4" style="height:4px; background:var(--border); flex:1; border-radius:2px; transition:0.3s"></div>
                  </div>
                  <div class="pwd-label" style="font-size:12px; color:var(--ink-muted);">Kekuatan password: <span id="pwd-text">—</span></div>
                </div>
              </div>
              <div class="form-field">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirm" placeholder="Ulangi password baru">
                <div class="pwd-rules" style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; margin-top:12px; font-size:13px; color:var(--ink-muted);">
                  <div class="rule" id="rule-len">• Minimal 8 karakter</div>
                  <div class="rule" id="rule-upper">• Mengandung huruf besar</div>
                  <div class="rule" id="rule-num">• Mengandung angka</div>
                  <div class="rule" id="rule-sym">• Mengandung simbol (!@#$%)</div>
                </div>
              </div>
            </div>
            <div class="profile-actions" style="margin-top: 32px; border-top: 1px solid var(--border-soft); padding-top: 24px; display:flex; gap:12px;">
              <button type="submit" name="action_type" value="save" class="btn-save-details" style="background: var(--rose-deep); color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                ✓ Save Password
              </button>
              <button type="button" class="btn-cancel-details" onclick="window.location.href = 'index.php?page=<?= e($backPage) ?>'" style="background: #fff; border:1px solid var(--border); padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600; color:var(--ink-mid);">
                ✕ Cancel
              </button>
            </div>
          </div>

          <!-- TAB 3: DELETE ACCOUNT -->
          <div id="tab-delete-account" class="tab-pane">
            <?php if (($user['delete_requested'] ?? 0) == 1): ?>
              <div class="danger-zone" style="background:#fffbeb; border:1px solid #d97706; border-radius:8px; padding:24px; margin-bottom:24px;">
                <h3 style="color:#d97706; margin-top:0; font-size:16px; margin-bottom:16px;">⚠️ Permintaan Penghapusan Terkirim</h3>
                <p style="color:#92400e; font-size:14px; margin:0; line-height:1.6;">Permintaan penghapusan akun Anda saat ini sedang menunggu persetujuan dan verifikasi dari Admin. Anda masih dapat membatalkan permintaan ini di bawah jika berubah pikiran.</p>
                <button type="submit" name="action_type" value="cancel_delete_account" class="btn-danger-action" style="background:#d97706; color:#fff; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:600; margin-top:20px; display:flex; align-items:center; gap:8px;">
                  🔄 Batalkan Permintaan Penghapusan Akun
                </button>
              </div>
            <?php else: ?>
              <div class="danger-zone" style="background: var(--rose-blush); border: 1px solid var(--rose-pale); border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                <h3 style="color: var(--rose-deep); margin-top:0; font-size: 16px; margin-bottom: 16px;">
                  ⚠️ Tindakan ini tidak dapat dibatalkan
                </h3>
                <ul style="color: var(--rose-deep); margin: 0; padding-left: 20px; font-size: 14px; line-height: 1.6;">
                  <li>Semua data profil, riwayat transaksi, dan pesan Anda akan dihapus secara permanen.</li>
                  <li>Buku yang sedang Anda jual akan diturunkan dari katalog secara otomatis.</li>
                  <li>Saldo atau poin yang belum digunakan akan hangus dan tidak dapat dikembalikan.</li>
                  <li>Anda tidak dapat menggunakan email yang sama untuk mendaftar ulang selama 30 hari.</li>
                </ul>
              </div>
              
              <div class="form-field" style="max-width: 600px; margin-bottom: 24px;">
                <label style="font-weight:600; color:var(--ink); margin-bottom:4px; display:block;">Ketik "DELETE" untuk konfirmasi</label>
                <p style="font-size: 13px; color: var(--ink-muted); margin-bottom: 12px; margin-top: 0;">Ini membantu memastikan Anda tidak menghapus akun secara tidak sengaja.</p>
                <input type="text" name="delete_confirm_text" placeholder="DELETE" id="delete-confirm-input" onkeyup="checkDeleteStatus()" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:6px; font-size:14px; font-family:var(--font-body);">
              </div>

              <div class="form-field" style="max-width: 600px; margin-bottom: 32px;">
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; font-weight: 400; color: var(--ink-mid);">
                  <input type="checkbox" id="delete-checkbox" onchange="checkDeleteStatus()" style="margin-top: 4px; width: 18px; height: 18px; accent-color: var(--rose-deep);">
                  <span style="font-size:14px; line-height:1.5;">Saya memahami bahwa akun ini akan dihapus secara permanen dan tidak dapat dipulihkan kembali.</span>
                </label>
              </div>

              <div class="profile-actions" style="border-top: 1px solid var(--border-soft); padding-top: 24px; display:flex; gap:12px;">
                <button type="submit" name="action_type" value="request_delete_account" class="btn-danger-action" id="btn-delete-account" disabled style="background: var(--rose-light); color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:not-allowed; font-weight:600; display:flex; align-items:center; gap:8px;">
                  🗑️ Delete My Account
                </button>
                <button type="button" class="btn-cancel-details" onclick="window.location.href = 'index.php?page=<?= e($backPage) ?>'" style="background: #fff; border:1px solid var(--border); padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600; color:var(--ink-mid);">
                  ✕ Cancel
                </button>
              </div>
            <?php endif; ?>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>

<style>
/* --- Profile Page Styling --- */
.profile-container {
  background: #fff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  max-width: 900px;
  margin: 0 auto;
}
.profile-title {
  font-family: var(--font-body);
  font-size: 26px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 24px;
}

/* Tabs */
.profile-tabs {
  display: flex;
  gap: 24px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 32px;
  padding-bottom: 2px;
}
.tab-btn {
  background: none;
  border: none;
  padding: 10px 4px;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  position: relative;
  transition: color 0.2s;
}
.tab-btn:hover {
  color: #0f172a;
}
.tab-btn.active {
  color: #1d4ed8;
}
.tab-btn.active::after {
  content: '';
  position: absolute;
  bottom: -3px;
  left: 0;
  right: 0;
  height: 3px;
  background: #1d4ed8;
  border-radius: 2px;
}

/* Panes */
.tab-pane {
  display: none;
}
.tab-pane.active {
  display: block;
}

/* Avatar section */
.avatar-section {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 32px;
}
.avatar-container {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e2e8f0;
}
.profile-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.profile-avatar-placeholder {
  font-size: 36px;
  font-weight: 700;
  color: #64748b;
}
.avatar-details h3 {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}
.avatar-details p {
  font-size: 12px;
  color: #94a3b8;
  margin-bottom: 12px;
}
.avatar-actions {
  display: flex;
  gap: 10px;
}
.btn-upload, .btn-delete-photo {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-family: var(--font-body);
  transition: all 0.2s;
}
.btn-upload {
  background: #fff;
  border: 1px solid #cbd5e1;
  color: #475569;
}
.btn-upload:hover {
  background: #f8fafc;
  border-color: #94a3b8;
}
.btn-delete-photo {
  background: #fff5f5;
  border: 1px solid #fecaca;
  color: #e11d48;
}
.btn-delete-photo:hover {
  background: #ffe4e6;
}

/* Fields Grid */
.fields-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 32px;
}
.form-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-field label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}
.form-field input {
  padding: 11px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  outline: none;
  font-family: var(--font-body);
  transition: all 0.2s;
  background: #fff;
}
.form-field input:focus:not(.readonly-field) {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
.form-field input.readonly-field {
  background: #f8fafc;
  color: #94a3b8;
  cursor: not-allowed;
  border-color: #e2e8f0;
}

/* Danger Zone */
.danger-zone {
  border: 1px dashed #f87171;
  background: #fff5f5;
  border-radius: 12px;
  padding: 24px;
}
.danger-zone h3 {
  font-size: 16px;
  font-weight: 700;
  color: #b91c1c;
  margin-bottom: 6px;
}
.danger-zone p {
  font-size: 13px;
  color: #7f1d1d;
  margin-bottom: 16px;
}
.btn-danger-action {
  background: #dc2626;
  border: none;
  color: #fff;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 700;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-danger-action:hover {
  background: #b91c1c;
}

/* Footer buttons */
.profile-actions {
  display: flex;
  gap: 12px;
  border-top: 1px solid #e2e8f0;
  padding-top: 24px;
}
.btn-save-details {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #1e3a8a; /* Premium dark navy */
  color: #fff;
  border: none;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 700;
  padding: 12px 24px;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-save-details:hover {
  background: #172554;
}
.btn-cancel-details {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #fff;
  border: 1px solid #cbd5e1;
  color: #475569;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 700;
  padding: 12px 24px;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-cancel-details:hover {
  background: #f8fafc;
}

@media (max-width: 640px) {
  .fields-grid {
    grid-template-columns: 1fr;
  }
  .avatar-section {
    flex-direction: column;
    text-align: center;
  }
}
</style>

<script>
function switchTab(e, tabId) {
  e.preventDefault();
  // Deactivate all tabs
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
  
  // Activate selected tab & pane
  e.currentTarget.classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

function checkPasswordStrength(val) {
  let strength = 0;
  let text = '—';
  let color = 'var(--border)';
  
  const hasLen = val.length >= 8;
  const hasUpper = /[A-Z]/.test(val);
  const hasNum = /[0-9]/.test(val);
  const hasSym = /[!@#$%^&*(),.?":{}|<>]/.test(val);
  
  if (hasLen) strength++;
  if (hasUpper) strength++;
  if (hasNum) strength++;
  if (hasSym) strength++;
  
  document.getElementById('rule-len').style.color = hasLen ? 'var(--rose)' : 'var(--ink-muted)';
  document.getElementById('rule-upper').style.color = hasUpper ? 'var(--rose)' : 'var(--ink-muted)';
  document.getElementById('rule-num').style.color = hasNum ? 'var(--rose)' : 'var(--ink-muted)';
  document.getElementById('rule-sym').style.color = hasSym ? 'var(--rose)' : 'var(--ink-muted)';
  
  if (strength === 0) { text = '—'; }
  else if (strength === 1) { text = 'Lemah'; color = '#ef4444'; }
  else if (strength === 2) { text = 'Sedang'; color = '#f59e0b'; }
  else if (strength === 3) { text = 'Kuat'; color = '#10b981'; }
  else if (strength === 4) { text = 'Sangat Kuat'; color = '#059669'; }
  
  document.getElementById('pwd-text').innerText = text;
  document.getElementById('pwd-text').style.color = color;
  
  for (let i = 1; i <= 4; i++) {
    document.getElementById('pwd-bar-' + i).style.background = (i <= strength) ? color : 'var(--border)';
  }
}

function checkDeleteStatus() {
  const input = document.getElementById('delete-confirm-input').value;
  const cb = document.getElementById('delete-checkbox').checked;
  const btn = document.getElementById('btn-delete-account');
  
  if (input === 'DELETE' && cb) {
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';
    btn.style.background = '#dc2626'; // pure red for final action
  } else {
    btn.disabled = true;
    btn.style.opacity = '0.5';
    btn.style.cursor = 'not-allowed';
    btn.style.background = 'var(--rose-light)';
  }
}
</script>

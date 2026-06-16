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
      $buyerMenu = 'account'; 
      require __DIR__ . '/../pembeli/partials/sidebar.php'; 
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
            <div class="sidebar-store-status">Super Admin</div>
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
            <button class="sidebar-item" onclick="showToast('📚 Kelola Produk')">
              <span class="si">📚</span> Kelola Produk
            </button>
            <button class="sidebar-item" onclick="showPage('admin_categories')">
              <span class="si">🗂️</span> Kategori
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
          <button class="tab-btn active" onclick="switchTab(event, 'tab-basic-info')">Basic Info</button>
          <button class="tab-btn" onclick="switchTab(event, 'tab-password-change')">Password Change</button>
          <button class="tab-btn" onclick="switchTab(event, 'tab-delete-account')">Delete Account</button>
        </div>

        <form id="profileForm" class="profile-form" method="POST" action="index.php?action=update_account" enctype="multipart/form-data">
          <input type="hidden" name="delete_avatar" id="delete-avatar-input" value="0">
          
          <!-- TAB 1: BASIC INFO -->
          <div id="tab-basic-info" class="tab-pane active">
            <!-- Profile Picture Section -->
            <div class="avatar-section">
              <div class="avatar-container">
                <?php if (!empty($user['avatar'])): ?>
                  <img src="<?= e($user['avatar']) ?>" id="avatar-preview" class="profile-avatar-img" alt="Avatar">
                <?php else: ?>
                  <div id="avatar-placeholder" class="profile-avatar-placeholder">
                    <?= e($initials) ?>
                  </div>
                  <img src="" id="avatar-preview" class="profile-avatar-img" style="display:none;" alt="Avatar">
                <?php endif; ?>
              </div>
              <div class="avatar-details">
                <h3>Profile picture</h3>
                <p>PNG or JPG no bigger than 1000px wide and tall.</p>
                <div class="avatar-actions">
                  <input type="file" name="avatar" id="avatar-file" accept="image/*" style="display:none;" onchange="previewAvatar(event)">
                  <button type="button" class="btn-upload" onclick="document.getElementById('avatar-file').click()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                    Upload New Photo
                  </button>
                  <button type="button" class="btn-delete-photo" onclick="deletePhoto()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Delete Photo
                  </button>
                </div>
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
          </div>

          <!-- TAB 2: PASSWORD CHANGE -->
          <div id="tab-password-change" class="tab-pane">
            <div class="fields-grid" style="grid-template-columns: 1fr; max-width: 500px;">
              <div class="form-field">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Enter current password">
              </div>
              <div class="form-field">
                <label>New Password</label>
                <input type="password" name="password" placeholder="Enter new password (min. 6 characters)">
              </div>
              <div class="form-field">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirm" placeholder="Repeat new password">
              </div>
            </div>
          </div>

          <!-- TAB 3: DELETE ACCOUNT -->
          <div id="tab-delete-account" class="tab-pane">
            <div class="danger-zone" style="background: <?= ($user['delete_requested'] ?? 0) ? '#fffbeb' : '#fff5f5' ?>; border-color: <?= ($user['delete_requested'] ?? 0) ? '#d97706' : '#f87171' ?>;">
              <?php if (($user['delete_requested'] ?? 0) == 1): ?>
                <h3 style="color:#d97706">Permintaan Penghapusan Terkirim</h3>
                <p style="color:#92400e">Permintaan penghapusan akun Anda saat ini sedang menunggu persetujuan dan verifikasi dari Admin. Anda masih dapat membatalkan permintaan ini di bawah jika berubah pikiran.</p>
                <button type="submit" name="action_type" value="cancel_delete_account" class="btn-danger-action" style="background:#d97706">
                  🔄 Batalkan Permintaan Penghapusan Akun
                </button>
              <?php else: ?>
                <h3>Warning</h3>
                <p>Menghapus akun Anda bersifat permanen dan tidak dapat dibatalkan. Mengklik tombol di bawah akan mengirimkan permintaan verifikasi ke Admin untuk menyetujui penghapusan akun Anda.</p>
                <button type="submit" name="action_type" value="request_delete_account" class="btn-danger-action" onclick="return confirm('Apakah Anda yakin ingin mengirim permintaan penghapusan akun ke Admin?')">
                  ⚠️ Kirim Permintaan Penghapusan Akun
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="profile-actions">
            <button type="submit" name="action_type" value="save" class="btn-save-details">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 12 4 10"/></svg>
              Save Details
            </button>
            <button type="button" class="btn-cancel-details" onclick="window.location.href = 'index.php?page=<?= e($backPage) ?>'">
              ✕ Cancel
            </button>
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

function previewAvatar(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = document.getElementById('avatar-preview');
      const placeholder = document.getElementById('avatar-placeholder');
      
      img.src = e.target.result;
      img.style.display = 'block';
      if (placeholder) placeholder.style.display = 'none';
      
      // Reset delete status
      document.getElementById('delete-avatar-input').value = '0';
    };
    reader.readAsDataURL(file);
  }
}

function deletePhoto() {
  const img = document.getElementById('avatar-preview');
  const placeholder = document.getElementById('avatar-placeholder');
  
  img.src = '';
  img.style.display = 'none';
  
  if (placeholder) {
    placeholder.style.display = 'flex';
  } else {
    // Dynamically create temporary placeholder if needed
    const container = document.querySelector('.avatar-container');
    const ph = document.createElement('div');
    ph.id = 'avatar-placeholder';
    ph.className = 'profile-avatar-placeholder';
    ph.textContent = '<?= e($initials) ?>';
    container.appendChild(ph);
  }
  
  document.getElementById('avatar-file').value = '';
  document.getElementById('delete-avatar-input').value = '1';
}
</script>

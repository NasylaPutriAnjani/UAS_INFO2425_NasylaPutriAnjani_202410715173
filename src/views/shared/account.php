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
            <button class="sidebar-item" onclick="showPage('seller')"><span class="si">📊</span> Dashboard</button>
            <button class="sidebar-item" onclick="showPage('seller_products')"><span class="si">📦</span> Produk Saya <span class="sidebar-badge"><?= $activeProductsCount ?></span></button>
            <button class="sidebar-item" onclick="showPage('seller_orders')"><span class="si">🛒</span> Pesanan Masuk</button>
            <button class="sidebar-item" onclick="showPage('seller_reviews')"><span class="si">💬</span> Ulasan &amp; Rating</button>
            <button class="sidebar-item" onclick="showPage('seller_notifications')"><span class="si">🔔</span> Notifikasi</button>
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
          <button type="button" class="tab-btn active" onclick="switchTab(this,'tab-basic-info')">Basic Info</button>
          <button type="button" class="tab-btn"        onclick="switchTab(this,'tab-password-change')">Password Change</button>
          <button type="button" class="tab-btn"        onclick="switchTab(this,'tab-delete-account')">Delete Account</button>
        </div>

        <!-- ══ TAB 1: BASIC INFO ══ -->
        <div id="tab-basic-info" class="tab-pane active">
          <form method="POST" action="index.php?action=update_account&page=<?= e($page ?? 'account_settings') ?>" enctype="multipart/form-data">
            <input type="hidden" name="action_type" value="save">
            <input type="hidden" name="delete_avatar" value="0">

            <!-- Avatar row -->
            <div class="acc-avatar-row">
              <div class="acc-avatar-circle"><?= e($initials) ?></div>
              <div>
                <div style="font-weight:700;font-size:15px;color:var(--ink-dark);margin-bottom:4px;">Profile picture</div>
                <div style="font-size:13px;color:var(--ink-muted);">Foto profil default menggunakan inisial nama Anda.</div>
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

<style>
/* ═══ Profile container ═══ */
.profile-container {
  background: #fff;
  border-radius: 16px;
  padding: 36px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  max-width: 860px;
  margin: 0 auto;
}
.profile-title {
  font-size: 26px;
  font-weight: 700;
  color: var(--ink-dark);
  margin: 0 0 24px;
}

/* ═══ Tabs ═══ */
.profile-tabs {
  display: flex;
  gap: 0;
  border-bottom: 2px solid var(--border-soft);
  margin-bottom: 32px;
}
.tab-btn {
  background: none;
  border: none;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  color: var(--ink-muted);
  cursor: pointer;
  position: relative;
  transition: color 0.2s;
  white-space: nowrap;
}
.tab-btn:hover { color: var(--ink-dark); }
.tab-btn.active {
  color: var(--rose-deep);
}
.tab-btn.active::after {
  content: '';
  position: absolute;
  bottom: -2px; left: 0; right: 0;
  height: 3px;
  background: var(--rose-deep);
  border-radius: 2px 2px 0 0;
}

/* ═══ Tab panes ═══ */
.tab-pane { display: none; }
.tab-pane.active { display: block; }

/* ═══ Avatar row ═══ */
.acc-avatar-row {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 28px;
  padding: 16px;
  background: var(--surface);
  border-radius: 12px;
  border: 1px solid var(--border-soft);
}
.acc-avatar-circle {
  width: 72px; height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--rose-deep), var(--rose-mid, #e85b7a));
  color: #fff;
  font-size: 26px;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}

/* ═══ Fields ═══ */
.fields-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 8px;
}
.form-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-field label {
  font-size: 13px;
  font-weight: 600;
  color: var(--ink-dark);
}
.form-field input {
  padding: 11px 14px;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fff;
  font-family: var(--font-body);
}
.form-field input:focus {
  border-color: var(--rose-deep);
  box-shadow: 0 0 0 3px rgba(var(--rose-rgb, 200,56,80), 0.12);
}
.form-field input.readonly-field {
  background: var(--surface);
  color: var(--ink-muted);
  cursor: not-allowed;
  border-color: var(--border-soft);
}

/* ═══ Password strength bar ═══ */
.pwd-bar {
  height: 4px;
  flex: 1;
  border-radius: 2px;
  background: var(--border);
  transition: background 0.3s;
}
.pwd-rule { color: var(--ink-muted); }

/* ═══ Action footer ═══ */
.tab-footer {
  display: flex;
  gap: 12px;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid var(--border-soft);
}
.btn-primary-rose {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: var(--rose-deep);
  color: #fff;
  border: none;
  padding: 11px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  font-family: var(--font-body);
  transition: opacity 0.2s;
}
.btn-primary-rose:hover { opacity: 0.87; }
.btn-outline-gray {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: #fff;
  border: 1.5px solid var(--border);
  color: var(--ink-mid);
  padding: 11px 22px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: var(--font-body);
  transition: background 0.2s;
}
.btn-outline-gray:hover { background: var(--surface); }

@media (max-width: 640px) {
  .fields-grid { grid-template-columns: 1fr; }
  .acc-avatar-row { flex-direction: column; text-align: center; }
  .profile-container { padding: 20px; }
}
</style>

<script>
/* ── Tab switching ── */
function switchTab(btn, tabId) {
  document.querySelectorAll('#profile-tabs .tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const pane = document.getElementById(tabId);
  if (pane) pane.classList.add('active');
}

/* ── Password strength ── */
function checkPasswordStrength(val) {
  const hasLen   = val.length >= 8;
  const hasUpper = /[A-Z]/.test(val);
  const hasNum   = /[0-9]/.test(val);
  const hasSym   = /[!@#$%^&*(),.?":{}|<>]/.test(val);
  let   strength = [hasLen, hasUpper, hasNum, hasSym].filter(Boolean).length;

  const colors   = ['', '#ef4444', '#f59e0b', '#10b981', '#059669'];
  const labels   = ['—', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
  const color    = strength ? colors[strength] : 'var(--border)';

  const ruleColor = (ok) => ok ? 'var(--rose-deep)' : 'var(--ink-muted)';
  document.getElementById('rule-len').style.color   = ruleColor(hasLen);
  document.getElementById('rule-upper').style.color = ruleColor(hasUpper);
  document.getElementById('rule-num').style.color   = ruleColor(hasNum);
  document.getElementById('rule-sym').style.color   = ruleColor(hasSym);

  const txt = document.getElementById('pwd-text');
  txt.textContent = labels[strength] || '—';
  txt.style.color = color;

  for (let i = 1; i <= 4; i++) {
    document.getElementById('pwd-bar-' + i).style.background = i <= strength ? color : 'var(--border)';
  }
}

/* ── Delete account gate ── */
function checkDeleteStatus() {
  const input = document.getElementById('delete-confirm-input');
  const cb    = document.getElementById('delete-checkbox');
  const btn   = document.getElementById('btn-delete-account');
  if (!btn) return;

  const ok = input && input.value === 'DELETE' && cb && cb.checked;
  btn.disabled      = !ok;
  btn.style.opacity = ok ? '1' : '0.6';
  btn.style.cursor  = ok ? 'pointer' : 'not-allowed';
  btn.style.background   = ok ? 'var(--rose-deep)' : 'var(--rose-pale)';
  btn.style.color        = ok ? '#fff' : 'var(--rose-deep)';
}
</script>

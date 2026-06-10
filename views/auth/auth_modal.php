<div class="modal-overlay" id="authModal" onclick="if(event.target===this)closeAuth()">
  <div class="auth-modal">
    <button type="button" class="auth-close" onclick="closeAuth()" aria-label="Tutup">✕</button>

    <div class="auth-header">
      <div class="auth-brand">
        <span class="auth-brand-icon" aria-hidden="true">
          <span class="auth-book auth-book--1"></span>
          <span class="auth-book auth-book--2"></span>
          <span class="auth-book auth-book--3"></span>
        </span>
        <span class="auth-logo">RubbyBooks</span>
      </div>
      <p class="auth-tagline">Temukan buku impianmu</p>
    </div>

    <div class="auth-tabs">
      <button type="button" class="auth-tab active" id="tab-masuk" onclick="switchTab('masuk')">Masuk</button>
      <button type="button" class="auth-tab" id="tab-daftar" onclick="switchTab('daftar')">Daftar</button>
    </div>

    <!-- LOGIN -->
    <div id="body-masuk" class="auth-body">
      <form method="post" action="index.php">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
          <label for="login-email">Email</label>
          <input class="form-input auth-input" type="email" name="email" id="login-email" placeholder="nama@email.com" required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="login-pass">Password</label>
          <input class="form-input auth-input" type="password" name="password" id="login-pass" placeholder="Masukkan password" required autocomplete="current-password">
        </div>
        <div class="remember-row">
          <label><input type="checkbox" name="remember"> Ingat saya</label>
          <a href="#" onclick="showToast('🔐 Fitur reset password segera hadir');return false">Lupa password?</a>
        </div>
        <button type="submit" class="btn-submit">Masuk</button>
      </form>
      <p class="auth-footer">
        Belum punya akun?
        <button type="button" class="auth-footer-link" onclick="switchTab('daftar')">Daftar sekarang</button>
      </p>
    </div>

    <!-- REGISTER -->
    <div id="body-daftar" class="auth-body" style="display:none">
      <form method="post" action="index.php" onsubmit="return validateRegister(event)">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="role" id="reg-role" value="buyer">
        <div class="form-group">
          <label for="reg-name">Nama Lengkap</label>
          <input class="form-input auth-input" type="text" name="name" id="reg-name" placeholder="Nama lengkap" required autocomplete="name">
        </div>
        <div class="form-group">
          <label for="reg-email">Email</label>
          <input class="form-input auth-input" type="email" name="email" id="reg-email" placeholder="nama@email.com" required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="reg-pass">Password</label>
          <input class="form-input auth-input" type="password" name="password" id="reg-pass" placeholder="Min. 6 karakter" required minlength="6" autocomplete="new-password">
        </div>
        <div class="form-group">
          <label for="reg-pass-confirm">Konfirmasi Password</label>
          <input class="form-input auth-input" type="password" id="reg-pass-confirm" placeholder="Ulangi password" required minlength="6" autocomplete="new-password">
        </div>
        <div class="auth-role-label">Pilih tipe akun</div>
        <div class="auth-role-types">
          <button type="button" class="auth-role-type active" data-role="buyer" onclick="selectRegRole('buyer')">
            <span class="auth-role-type-icon">🛒</span>
            <span class="auth-role-type-title">Pembeli</span>
            <span class="auth-role-type-desc">Cari &amp; beli buku</span>
          </button>
          <button type="button" class="auth-role-type" data-role="seller" onclick="selectRegRole('seller')">
            <span class="auth-role-type-icon">📦</span>
            <span class="auth-role-type-title">Penjual</span>
            <span class="auth-role-type-desc">Jual buku Anda</span>
          </button>
        </div>
        <button type="submit" class="btn-submit btn-submit-arrow">Daftar →</button>
      </form>
      <p class="auth-footer">
        Sudah punya akun?
        <button type="button" class="auth-footer-link" onclick="switchTab('masuk')">Masuk di sini</button>
      </p>
    </div>
  </div>
</div>

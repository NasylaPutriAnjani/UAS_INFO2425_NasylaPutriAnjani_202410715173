<?php $buyerMenu = 'account';
$user = current_user(); ?>
<div id="page-buyer-account" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>👤 Akun Saya</h2>
          <p>Kelola informasi profil dan keamanan akun</p>
        </div>
      </div>
      <div class="dash-body">
        <div class="buyer-panel" style="max-width:560px">
          <form class="buyer-form" method="POST" action="index.php?action=update_account&page=buyer_account">
            <div class="form-group">
              <label>Nama Lengkap</label>
              <input class="form-input" type="text" name="name" value="<?= e($user['name']) ?>" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input class="form-input" type="email" value="<?= e($user['email']) ?>" readonly>
            </div>
            <div class="form-group">
              <label>Role</label>
              <input class="form-input" type="text" value="Pembeli" readonly>
            </div>
            <div class="form-group">
              <label>Password Baru</label>
              <input class="form-input" type="password" name="password" placeholder="Kosongkan jika tidak diubah" minlength="6">
            </div>
            <button type="submit" class="btn-submit" style="max-width:220px">Simpan Perubahan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
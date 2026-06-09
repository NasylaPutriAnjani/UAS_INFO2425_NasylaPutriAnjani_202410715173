<section class="auth-page">
    <div class="auth-modal" style="transform:none">
        <div class="auth-top">
            <div class="auth-logo">Daftar RubbyBooks</div>
            <div class="auth-tagline">Buyer langsung aktif, seller menunggu approval admin.</div>
        </div>
        <form class="auth-body" method="post">
            <input type="hidden" name="action" value="register">
            <div class="form-group"><label>Nama</label><input class="form-input" name="name" required></div>
            <div class="form-group"><label>Role</label><select class="form-input" name="role"><option value="buyer">Buyer</option><option value="seller">Seller</option></select></div>
            <div class="form-group"><label>Email</label><input class="form-input" type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input class="form-input" type="password" name="password" required></div>
            <button class="btn-submit">Daftar</button>
            <p class="auth-switch">Sudah punya akun? <a href="index.php?page=login">Masuk di sini</a></p>
        </form>
    </div>
</section>

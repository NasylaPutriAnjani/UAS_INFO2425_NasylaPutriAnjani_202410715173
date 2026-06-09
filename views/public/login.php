<section class="auth-page">
    <div class="auth-modal" style="transform:none">
        <div class="auth-top">
            <div class="auth-logo">Masuk ke RubbyBooks</div>
            <div class="auth-tagline">Gunakan akun buyer, seller, atau admin.</div>
        </div>
        <form class="auth-body" method="post">
            <input type="hidden" name="action" value="login">
            <div class="form-group"><label>Email</label><input class="form-input" type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input class="form-input" type="password" name="password" required></div>
            <button class="btn-submit">Login</button>
            <p class="auth-switch">Belum punya akun? <a href="index.php?page=register">Daftar gratis</a></p>
            <p class="auth-switch auth-switch-demo">Demo: admin@rubbybooks.test / seller@rubbybooks.test / buyer@rubbybooks.test, password: password</p>
        </form>
    </div>
</section>

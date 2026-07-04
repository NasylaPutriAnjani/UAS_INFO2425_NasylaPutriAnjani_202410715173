  <?php 
  $isDashboardPage = in_array($activePage, [
      'buyer', 'buyer_account', 'buyer_wishlist', 'buyer_cart', 'buyer_orders', 'buyer_reviews', 'buyer_notifications',
      'seller', 'seller_products', 'seller_orders', 'seller_reviews', 'seller_notifications', 'seller_report',
      'admin', 'admin_users', 'admin_categories', 'admin_notifications', 'admin_settings',
      'account_settings'
  ], true);
  if (!$isDashboardPage): ?>
  <footer class="site-footer">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">📚 RubbyBooks.</div>
        <p class="footer-desc">Platform jual beli buku terpercaya di Indonesia. Lebih dari 12.000 judul pilihan menunggu kamu.</p>
      </div>
      <div class="footer-col">
        <div class="footer-heading">Pembeli</div>
        <div class="footer-links">
          <a href="index.php?page=catalog" class="footer-link">Katalog Buku</a>
          <a href="index.php?page=tracking" class="footer-link">Lacak Pesanan</a>
          <a href="#" class="footer-link">Wishlist</a>
          <a href="#" class="footer-link">Ulasan Saya</a>
        </div>
      </div>
      <div class="footer-col">
        <div class="footer-heading">Penjual</div>
        <div class="footer-links">
          <a href="#" class="footer-link">Daftar Penjual</a>
          <a href="#" class="footer-link">Kelola Produk</a>
          <a href="#" class="footer-link">Laporan Penjualan</a>
        </div>
      </div>
      <div class="footer-col">
        <div class="footer-heading">Bantuan</div>
        <div class="footer-links">
          <a href="#" class="footer-link">FAQ</a>
          <a href="#" class="footer-link">Hubungi Kami</a>
          <a href="#" class="footer-link">Kebijakan Privasi</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2025 RubbyBooks. Hak cipta dilindungi.</span>
      <span class="footer-made">Dibuat dengan ❤️ untuk pecinta buku Indonesia</span>
    </div>
  </footer>
  <?php endif; ?>
  <?php if (!empty($user)): ?>
  <script>
    window.__RB_USER__ = <?= json_encode([
        'name' => $user['name'],
        'role' => $user['role'],
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <?php endif; ?>
  <script src="src/assets/js/main.js?v=<?= time() ?>"></script>
  <?php
  $roleScriptMap = [
    'admin'  => 'src/assets/js/admin/admin.js',
    'buyer'  => 'src/assets/js/buyer/buyer.js',
    'seller' => 'src/assets/js/seller/seller.js',
  ];
  $roleScript = $roleScriptMap[$user['role'] ?? ''] ?? null;
  if ($roleScript): ?>
  <script src="<?= $roleScript ?>?v=<?= time() ?>"></script>
  <?php endif; ?>
</main>

</body>
</html>
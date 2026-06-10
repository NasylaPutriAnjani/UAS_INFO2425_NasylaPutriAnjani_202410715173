<?php $buyerMenu = 'wishlist'; ?>
<div id="page-buyer-wishlist" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>❤️ Wishlist</h2>
          <p>Buku yang kamu simpan untuk dibeli nanti</p>
        </div>
        <div class="dash-topbar-right">
          <a href="index.php?page=catalog" class="btn-dash-ghost">+ Tambah dari Katalog</a>
        </div>
      </div>
      <div class="dash-body">
        <div class="buyer-panel">
          <div class="buyer-empty">
            <div class="buyer-empty-icon">❤️</div>
            <p>Wishlist masih kosong. Ketuk ikon ♡ di katalog untuk menyimpan buku favorit.</p>
            <a href="index.php?page=catalog" class="btn-dash-primary">Jelajahi Katalog</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

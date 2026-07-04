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
        <?php if (empty($wishlistItems)): ?>
          <div class="buyer-panel">
            <div class="buyer-empty">
              <div class="buyer-empty-icon">❤️</div>
              <p>Wishlist masih kosong. Ketuk ikon ♡ di katalog untuk menyimpan buku favorit.</p>
              <a href="index.php?page=catalog" class="btn-dash-primary">Jelajahi Katalog</a>
            </div>
          </div>
        <?php else: ?>
          <div class="book-grid" style="padding:0">
            <?php foreach ($wishlistItems as $book): 
              $bcClass = 'bc' . (($book['id'] % 6) + 1);
            ?>
              <div class="book-card">
                <div class="book-cover-lg <?= $bcClass ?>">
                  <?= e($book['name']) ?>
                  <form method="POST" action="index.php?action=toggle_wishlist" style="display:inline;position:absolute;top:12px;right:12px;">
                    <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="wishlist-btn active" style="position:static" title="Hapus dari wishlist">❤️</button>
                  </form>
                </div>
                <div class="book-body">
                  <div class="book-genre"><?= e($book['category'] ?? 'Buku') ?></div>
                  <div class="book-title"><?= e($book['name']) ?></div>
                  <div class="book-author">Stok: <?= (int)$book['stock'] ?></div>
                  <div class="book-footer">
                    <div class="book-price"><?= rupiah($book['price']) ?></div>
                    <form method="POST" action="index.php?action=add_cart" style="display:inline">
                      <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
                      <button type="submit" class="btn-add">+</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

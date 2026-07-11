<?php
$buyerMenu = 'cart';
$items = $items ?? [];
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += (int)$item['price'] * (int)$item['qty'];
}
?>
<div id="page-buyer-cart" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>Keranjang</h2>
          <p><?= count($items) ?> produk di keranjang belanjamu</p>
        </div>
      </div>

      <div class="dash-body">
        <?php if (empty($items)): ?>
          <div class="buyer-panel">
            <div class="buyer-empty">
              <div class="buyer-empty-icon">🛒</div>
              <p>Keranjang masih kosong. Temukan buku impianmu di katalog!</p>
              <a href="index.php?page=catalog" class="btn-dash-primary">Belanja Sekarang</a>
            </div>
          </div>
        <?php else: ?>
          <div class="cart-shop-layout">
            <section class="cart-shop-list">
              <div class="cart-select-row">
                <label class="cart-check-wrap">
                  <input type="checkbox" id="cart-select-all" checked>
                  <span>Pilih Semua (<?= count($items) ?>)</span>
                </label>
              </div>

              <?php foreach ($items as $item):
                $lineTotal = (int)$item['price'] * (int)$item['qty'];
                $coverStyle = !empty($item['image'])
                  ? "background-image:url('" . e(asset($item['image'])) . "');background-size:cover;background-position:center;font-size:0;"
                  : '';
              ?>
                <article class="cart-shop-item" data-price="<?= (int)$item['price'] ?>" data-qty="<?= (int)$item['qty'] ?>">
                  <label class="cart-item-check">
                    <input type="checkbox" class="cart-item-select" checked>
                  </label>

                  <a href="index.php?page=product&id=<?= (int)$item['id'] ?>" class="cart-item-cover bc<?= ((int)$item['id'] % 6) + 1 ?>" style="<?= $coverStyle ?>">
                    <?= empty($item['image']) ? e($item['name']) : '' ?>
                  </a>

                  <div class="cart-item-main">
                    <a href="index.php?page=product&id=<?= (int)$item['id'] ?>" class="cart-item-title"><?= e($item['name']) ?></a>
                    <div class="cart-item-meta">Stok tersedia: <?= (int)$item['stock'] ?> buku</div>
                    <div class="cart-item-actions">
                      <form method="POST" action="index.php?action=toggle_wishlist">
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                        <button type="submit" class="cart-icon-btn" title="Wishlist">♡</button>
                      </form>
                      <form method="POST" action="index.php?action=remove_cart" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                        <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                        <input type="hidden" name="redirect" value="buyer_cart">
                        <button type="submit" class="cart-icon-btn" title="Hapus">×</button>
                      </form>
                    </div>
                  </div>

                  <div class="cart-item-side">
                    <div class="cart-item-price"><?= rupiah((int)$item['price']) ?></div>
                    <form method="POST" action="index.php?action=update_cart" class="cart-qty-form">
                      <input type="hidden" name="redirect" value="buyer_cart">
                      <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                      <button type="submit" name="qty" value="<?= max(1, (int)$item['qty'] - 1) ?>" class="cart-qty-btn" <?= (int)$item['qty'] <= 1 ? 'disabled' : '' ?>>−</button>
                      <span class="cart-qty-val"><?= (int)$item['qty'] ?></span>
                      <button type="submit" name="qty" value="<?= min((int)$item['stock'], (int)$item['qty'] + 1) ?>" class="cart-qty-btn" <?= (int)$item['qty'] >= (int)$item['stock'] ? 'disabled' : '' ?>>+</button>
                    </form>
                    <div class="cart-line-total"><?= rupiah($lineTotal) ?></div>
                  </div>
                </article>
              <?php endforeach; ?>
            </section>

            <aside class="cart-summary-card">
              <h3>Ringkasan belanja</h3>
              <div class="cart-summary-row">
                <span>Total</span>
                <strong id="cart-selected-total"><?= rupiah($subtotal) ?></strong>
              </div>
              <a href="index.php?page=checkout" class="cart-buy-btn" id="cart-checkout-btn">Beli</a>
            </aside>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

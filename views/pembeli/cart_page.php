<?php $buyerMenu = 'cart'; ?>
<div id="page-buyer-cart" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>🛒 Keranjang</h2>
          <p><?= count($items) ?> produk di keranjang belanjamu</p>
        </div>
        <?php if (!empty($items)): ?>
          <div class="dash-topbar-right">
            <a href="index.php?page=checkout" class="btn-dash-primary">Lanjut Checkout →</a>
          </div>
        <?php endif; ?>
      </div>
      <div class="dash-body">
        <div class="buyer-panel">
          <?php if (empty($items)): ?>
            <div class="buyer-empty">
              <div class="buyer-empty-icon">🛒</div>
              <p>Keranjang masih kosong. Temukan buku impianmu di katalog!</p>
              <a href="index.php?page=catalog" class="btn-dash-primary">Belanja Sekarang</a>
            </div>
          <?php else: ?>
            <div class="buyer-cart-list">
              <?php $subtotal = 0; foreach ($items as $item): $subtotal += (int) $item['price'] * (int) $item['qty']; ?>
                <div class="buyer-cart-row">
                  <div class="buyer-cart-cover bc<?= ((int) $item['id'] % 6) + 1 ?>"><?= e($item['name']) ?></div>
                  <div class="buyer-cart-info">
                    <div class="buyer-cart-title"><?= e($item['name']) ?></div>
                    <div class="buyer-cart-price"><?= rupiah((int) $item['price']) ?> × <?= (int) $item['qty'] ?></div>
                  </div>
                  <div class="buyer-cart-total"><?= rupiah((int) $item['price'] * (int) $item['qty']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="buyer-cart-summary">
              <div class="buyer-cart-summary-row">
                <span>Subtotal</span>
                <strong><?= rupiah($subtotal) ?></strong>
              </div>
              <a href="index.php?page=checkout" class="btn-submit" style="display:inline-block;text-align:center;margin-top:16px;max-width:280px">Lanjut ke Checkout →</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

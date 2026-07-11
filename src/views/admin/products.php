<div id="page-admin_products" class="page active admin-page">
  <div class="dash-layout admin-layout">

    <?php $adminActivePage = 'admin_products'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="dash-content">
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <div class="admin-topbar-title">
            <h2>📚 Kelola Produk</h2>
            <p>Daftar semua produk buku dari seluruh penjual</p>
          </div>
        </div>
        <div class="admin-topbar-right">
          <form method="GET" action="index.php" class="admin-search-form">
            <input type="hidden" name="page" value="admin_products">
            <input class="admin-search-input" type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari produk, kategori, penjual...">
            <button class="btn-admin-ghost">Cari</button>
            <?php if (!empty($q)): ?>
              <a href="index.php?page=admin_products" class="admin-action-btn">Reset</a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <div class="admin-body">
        <div class="admin-table-card">
          <div class="admin-table-head">
            <div>
              <h4>Inventori Platform</h4>
              <p>Total: <strong><?= count($products ?? []) ?> produk</strong></p>
            </div>
          </div>
          <div class="admin-table-responsive">
            <table class="admin-data-table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Penjual</th>
                  <th>Kategori</th>
                  <th>Harga</th>
                  <th>Stok</th>
                  <th>Terjual</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($products)): ?>
                  <tr><td colspan="7" style="text-align:center;color:var(--ink-muted);">Belum ada produk.</td></tr>
                <?php else: ?>
                  <?php foreach ($products as $product): ?>
                    <tr>
                      <td>
                        <div class="td-user">
                          <div class="td-user-avatar" style="background:linear-gradient(135deg,var(--accent),var(--accent-deep));">BK</div>
                          <div>
                            <div class="td-user-name"><?= e($product['name']) ?></div>
                            <div class="td-user-email"><?= e($product['book_condition']) ?></div>
                          </div>
                        </div>
                      </td>
                      <td><?= e($product['seller_name']) ?></td>
                      <td><span class="td-method"><?= e($product['category']) ?></span></td>
                      <td><span class="td-amount"><?= rupiah((int)$product['price']) ?></span></td>
                      <td><?= (int)$product['stock'] ?></td>
                      <td><?= (int)$product['sold_count'] ?></td>
                      <td>
                        <span class="status-pill <?= $product['status'] === 'active' ? 'sp-active' : 'sp-inactive' ?>">
                          <?= ucfirst(e($product['status'])) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

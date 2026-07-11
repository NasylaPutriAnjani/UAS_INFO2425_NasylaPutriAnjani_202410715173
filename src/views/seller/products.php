<div id="page-seller-products" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <?php
    $currentSellerPage = $_GET['page'] ?? 'seller';
    $sellerIdForSidebar = current_user()['id'];
    $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
    $sellerNavCounts = user_nav_counts($GLOBALS['pdo']);
    $sellerOrderBadgeCount = (int)($sellerNavCounts['orders'] ?? 0);
    $sellerUnreadNotifCount = (int)($sellerNavCounts['notifications'] ?? 0);
    ?>
    <aside class="dash-sidebar seller-sidebar">
      <div class="sidebar-store-profile">
        <?= user_avatar_html(current_user(), 'sidebar-store-avatar', 'S') ?>
        <div>
          <div class="sidebar-store-name"><?= e(current_user()['name'] ?? 'Penjual') ?></div>
          <div class="sidebar-store-status">Toko Aktif</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="sidebar-group">
          <div class="sidebar-group-label">Menu Utama</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller' ? ' active' : '' ?>" onclick="showPage('seller')">
            <span class="si">Dashboard
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_products' ? ' active' : '' ?>" onclick="showPage('seller_products')">
            <span class="si">📦</span> Produk Saya
            <span class="sidebar-badge"><?= $activeProductsCount ?></span>
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_orders' ? ' active' : '' ?>" onclick="showPage('seller_orders')">
            <span class="si">🛒</span> Pesanan Masuk

            <?php if ($sellerOrderBadgeCount > 0): ?><span class="sidebar-badge"><?= $sellerOrderBadgeCount ?></span><?php endif; ?>
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_reviews' ? ' active' : '' ?>" onclick="showPage('seller_reviews')">
            <span class="si">💬</span> Ulasan & Rating
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_notifications' ? ' active' : '' ?>" onclick="showPage('seller_notifications')">
            <span class="si">🔔</span> Notifikasi

            <?php if ($sellerUnreadNotifCount > 0): ?><span class="sidebar-badge warn"><?= $sellerUnreadNotifCount ?></span><?php endif; ?>
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Keuangan</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_report' ? ' active' : '' ?>" onclick="showPage('seller_report')">
            <span class="si">💰</span> Laporan Penjualan
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Pengaturan</div>
          <button class="sidebar-item" onclick="showPage('account_settings')">
            <span class="si">⚙️</span> Pengaturan Akun
          </button>
        </div>
      </nav>

      <div class="sidebar-footer">
        <button class="sidebar-item" onclick="doLogout()" style="color:#dc2626;width:100%">
          <span class="si">🚪</span> Keluar
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="dash-content">
      <!-- Topbar -->
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>Produk Saya</h2>
          <p>Kelola katalog buku yang Anda jual di RubbyBooks</p>
        </div>
        <div class="dash-topbar-right">
          <button class="btn-dash-primary" onclick="openAddModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Produk
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">

        <!-- Filters Row -->
        <div class="filter-section-card">
          <form method="GET" action="index.php" class="seller-filter-form">
            <input type="hidden" name="page" value="seller_products">

            <div class="search-input-wrapper">
              <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
              <input type="text" name="q" placeholder="Cari produk..." value="<?= e($filters['q'] ?? '') ?>" onchange="this.form.submit()">
            </div>

            <div class="filter-button-wrapper">
              <span class="filter-btn-icon">📁</span>
              <select name="category" onchange="this.form.submit()" class="filter-select-input">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= (string)($filters['category'] ?? '') === (string)$cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="filter-button-wrapper">
              <span class="filter-btn-icon">⇅</span>
              <select name="sort" onchange="this.form.submit()" class="filter-select-input">
                <option value="Terbaru" <?= ($filters['sort'] ?? '') === 'Terbaru' ? 'selected' : '' ?>>Urutkan: Terbaru</option>
                <option value="Harga Terendah" <?= ($filters['sort'] ?? '') === 'Harga Terendah' ? 'selected' : '' ?>>Harga Terendah</option>
                <option value="Harga Tertinggi" <?= ($filters['sort'] ?? '') === 'Harga Tertinggi' ? 'selected' : '' ?>>Harga Tertinggi</option>
                <option value="Stok Terendah" <?= ($filters['sort'] ?? '') === 'Stok Terendah' ? 'selected' : '' ?>>Stok Terendah</option>
                <option value="Stok Tertinggi" <?= ($filters['sort'] ?? '') === 'Stok Tertinggi' ? 'selected' : '' ?>>Stok Tertinggi</option>
                <option value="Terlaris" <?= ($filters['sort'] ?? '') === 'Terlaris' ? 'selected' : '' ?>>Terlaris</option>
              </select>
            </div>
          </form>
        </div>

        <!-- Product Table Card -->
        <div class="orders-card">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width: 45%;">PRODUK</th>
                <th style="width: 15%;">HARGA</th>
                <th style="text-align: center; width: 10%;">STOK</th>
                <th style="text-align: center; width: 10%;">TERJUAL</th>
                <th style="text-align: center; width: 10%;">STATUS</th>
                <th style="text-align: center; width: 10%;">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($products)): ?>
                <tr>
                  <td colspan="6" class="text-center" style="padding: 40px; color: #64748b;">
                    Belum ada produk yang sesuai dengan filter pencarian.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($products as $product):
                  $bcClass = 'bc' . (($product['id'] % 6) + 1);
                  $stockVal = (int)$product['stock'];
                  $soldVal = (int)($product['sold_count'] ?? 0);

                  // Status Logic:
                  // inactive -> Draf (orange/yellow)
                  // active + stock 0 -> Habis (red)
                  // active + stock > 0 -> Aktif (green)
                  if ($product['status'] === 'inactive') {
                      $statusLabel = 'Draf';
                      $statusClass = 's-draf';
                  } elseif ($stockVal <= 0) {
                      $statusLabel = 'Habis';
                      $statusClass = 's-habis';
                  } else {
                      $statusLabel = 'Aktif';
                      $statusClass = 's-aktif';
                  }
                ?>
                  <tr>
                    <td>
                      <div class="product-info-cell">
                        <?php if (!empty($product['image'])): ?>
                          <img src="<?= e(asset($product['image'])) ?>" class="product-cell-img" alt="<?= e($product['name']) ?>">
                        <?php else: ?>
                          <div class="product-cell-cover-placeholder <?= $bcClass ?>">
                            <?= e(substr($product['name'], 0, 2)) ?>
                          </div>
                        <?php endif; ?>
                        <div class="product-cell-details">
                          <span class="product-cell-title"><?= e($product['name']) ?></span>
                          <span class="product-cell-category"><?= e($product['category'] ?? 'Umum') ?></span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="product-cell-price"><?= rupiah($product['price']) ?></span>
                    </td>
                    <td style="text-align: center;">
                      <span class="product-cell-stock <?= $stockVal <= 2 ? 'low-stock' : '' ?>">
                        <?= $stockVal ?>
                      </span>
                    </td>
                    <td style="text-align: center;">
                      <span class="product-cell-sold"><?= $soldVal ?></span>
                    </td>
                    <td style="text-align: center;">
                      <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                    </td>
                    <td style="text-align: center;">
                      <div class="action-buttons-cell">
                        <button class="icon-action-btn edit-btn" title="Edit Produk" onclick='openEditModal(<?= json_encode($product, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                          ✏️
                        </button>
                        <form method="POST" action="index.php?page=seller_products" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                          <input type="hidden" name="action" value="delete_product">
                          <input type="hidden" name="id" value="<?= $product['id'] ?>">
                          <button type="submit" class="icon-action-btn delete-btn" title="Hapus Produk">
                            🗑️
                          </button>
                        </form>
                      </div>
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

<!-- ADD/EDIT PRODUCT MODAL -->
<div id="productModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="modalTitle">Tambah Produk</h3>
      <button class="close-modal-btn" onclick="closeProductModal()">✕</button>
    </div>
    <form id="productForm" method="POST" action="index.php?page=seller_products" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save_product">
      <input type="hidden" name="id" id="formProductId" value="">

      <div class="form-group">
        <label>Nama Buku</label>
        <input type="text" name="name" id="formProductName" class="form-input" required placeholder="Judul Buku">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Kategori</label>
          <select name="category_id" id="formProductCategory" class="form-input" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="formProductStatus" class="form-input" required>
            <option value="active">Aktif</option>
            <option value="inactive">Draf</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Harga (Rp)</label>
          <input type="number" name="price" id="formProductPrice" class="form-input" required placeholder="">
        </div>
        <div class="form-group">
          <label>Stok</label>
          <input type="number" name="stock" id="formProductStock" class="form-input" required placeholder="">
        </div>
      </div>

      <div class="form-group">
        <label>Cover Buku</label>
        <input type="file" name="image" id="formProductImage" class="form-input" accept="image/*">
        <small class="form-help" id="formProductImageHelp" style="color: #64748b; font-size: 11px;">Akan menggunakan cover default jika dikosongkan.</small>
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="description" id="formProductDescription" class="form-input" rows="3" placeholder="Tulis deskripsi sinopsis buku..."></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeProductModal()">Batal</button>
        <button type="submit" class="btn-primary" id="submitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

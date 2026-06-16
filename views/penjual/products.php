<div id="page-seller-products" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <?php
    $currentSellerPage = $_GET['page'] ?? 'seller';
    $sellerIdForSidebar = current_user()['id'];
    $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
    ?>
    <aside class="dash-sidebar seller-sidebar">
      <div class="sidebar-store-profile">
        <div class="sidebar-store-avatar">🏪</div>
        <div>
          <div class="sidebar-store-name"><?= e(current_user()['name'] ?? 'Penjual') ?></div>
          <div class="sidebar-store-status">Toko Aktif</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="sidebar-group">
          <div class="sidebar-group-label">Menu Utama</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller' ? ' active' : '' ?>" onclick="showPage('seller')">
            <span class="si">📊</span> Dashboard
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_products' ? ' active' : '' ?>" onclick="showPage('seller_products')">
            <span class="si">📦</span> Produk Saya
            <span class="sidebar-badge"><?= $activeProductsCount ?></span>
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_orders' ? ' active' : '' ?>" onclick="showPage('seller_orders')">
            <span class="si">🛒</span> Pesanan Masuk
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_reviews' ? ' active' : '' ?>" onclick="showPage('seller_reviews')">
            <span class="si">💬</span> Ulasan & Rating
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_notifications' ? ' active' : '' ?>" onclick="showPage('seller_notifications')">
            <span class="si">🔔</span> Notifikasi
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
                          <img src="<?= e($product['image']) ?>" class="product-cell-img" alt="<?= e($product['name']) ?>">
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
        <input type="text" name="name" id="formProductName" class="form-input" required placeholder="Contoh: Atomic Habits">
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
          <input type="number" name="price" id="formProductPrice" class="form-input" required placeholder="Contoh: 89000">
        </div>
        <div class="form-group">
          <label>Stok</label>
          <input type="number" name="stock" id="formProductStock" class="form-input" required placeholder="Contoh: 10">
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

<style>
/* Filters Styling */
.filter-section-card {
  background: #fff;
  border: 1px solid var(--border-soft);
  border-radius: 16px;
  padding: 16px 20px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.seller-filter-form {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}
.search-input-wrapper {
  position: relative;
  flex: 1;
  min-width: 200px;
}
.search-input-wrapper input {
  width: 100%;
  padding: 10px 14px 10px 38px;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  font-size: 13.5px;
  background: #f8fafc;
  outline: none;
  transition: all 0.2s;
}
.search-input-wrapper input:focus {
  border-color: var(--accent-light);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(var(--accent-rgb), .08);
}
.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.filter-button-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  background: #fff;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  padding: 0 12px;
  transition: all 0.2s;
}
.filter-button-wrapper:hover {
  border-color: var(--accent-light);
  background: var(--accent-blush);
}
.filter-btn-icon {
  margin-right: 6px;
  font-size: 14px;
}
.filter-select-input {
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 600;
  color: var(--ink-mid);
  padding: 10px 16px 10px 0;
  outline: none;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
}
.filter-button-wrapper::after {
  content: '▼';
  font-size: 8px;
  color: #94a3b8;
  position: absolute;
  right: 12px;
  pointer-events: none;
}

/* Table Cells Styling */
.product-info-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}
.product-cell-img {
  width: 44px;
  height: 56px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.product-cell-cover-placeholder {
  width: 44px;
  height: 56px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-serif);
  font-size: 11px;
  font-weight: 700;
  color: #fff;
  text-align: center;
  padding: 4px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.product-cell-details {
  display: flex;
  flex-direction: column;
}
.product-cell-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.3;
}
.product-cell-category {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 2px;
}
.product-cell-price {
  font-family: var(--font-serif);
  font-size: 14.5px;
  font-weight: 700;
  color: var(--ink);
}
.product-cell-stock {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--ink);
}
.product-cell-stock.low-stock {
  color: #dc2626;
}
.product-cell-sold {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

/* Status Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 30px;
  padding: 4px 12px;
  font-size: 11px;
  font-weight: 700;
}
.status-badge.s-aktif {
  background: #f0fdf4;
  color: #16a34a;
}
.status-badge.s-habis {
  background: #fef2f2;
  color: #dc2626;
}
.status-badge.s-draf {
  background: #fffbeb;
  color: #d97706;
}

/* Actions styling */
.action-buttons-cell {
  display: flex;
  gap: 8px;
  justify-content: center;
}
.icon-action-btn {
  background: #f8fafc;
  border: 1px solid var(--border-soft);
  border-radius: 8px;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 12px;
}
.icon-action-btn:hover {
  background: #f1f5f9;
  transform: scale(1.08);
}
.icon-action-btn.delete-btn:hover {
  background: #fef2f2;
  border-color: #fecaca;
}

/* MODAL OVERLAY */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}
.modal-overlay.open {
  opacity: 1;
  pointer-events: auto;
}
.modal-content {
  background: #fff;
  border-radius: 18px;
  width: 90%;
  max-width: 540px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  transform: translateY(20px);
  transition: transform 0.3s ease;
}
.modal-overlay.open .modal-content {
  transform: translateY(0);
}
.modal-header {
  padding: 16px 24px;
  border-bottom: 1.5px solid var(--border-soft);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.modal-header h3 {
  font-family: var(--font-serif);
  font-size: 18px;
  font-weight: 700;
  color: var(--ink);
}
.close-modal-btn {
  background: none;
  border: none;
  font-size: 16px;
  color: #94a3b8;
  cursor: pointer;
  transition: color 0.2s;
}
.close-modal-btn:hover {
  color: var(--ink);
}
.modal-content form {
  padding: 24px;
}
.modal-footer {
  margin-top: 24px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  border-top: 1.5px solid var(--border-soft);
  padding-top: 16px;
}
.form-help {
  display: block;
  margin-top: 4px;
}
</style>

<script>
function openProductModal() {
  document.getElementById('productModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeProductModal() {
  document.getElementById('productModal').classList.remove('open');
  document.body.style.overflow = '';
}

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Produk';
  document.getElementById('formProductId').value = '';
  document.getElementById('productForm').action = 'index.php?page=seller_products';
  
  document.getElementById('formProductName').value = '';
  document.getElementById('formProductCategory').value = document.getElementById('formProductCategory').options[0]?.value || '';
  document.getElementById('formProductStatus').value = 'active';
  document.getElementById('formProductPrice').value = '';
  document.getElementById('formProductStock').value = '';
  document.getElementById('formProductImage').value = '';
  document.getElementById('formProductDescription').value = '';
  document.getElementById('formProductImageHelp').textContent = 'Akan menggunakan cover default jika dikosongkan.';
  document.getElementById('submitBtn').textContent = 'Tambah Buku';
  
  openProductModal();
}

function openEditModal(product) {
  document.getElementById('modalTitle').textContent = 'Edit Produk';
  document.getElementById('formProductId').value = product.id;
  document.getElementById('productForm').action = 'index.php?page=seller_products';
  
  document.getElementById('formProductName').value = product.name;
  document.getElementById('formProductCategory').value = product.category_id;
  document.getElementById('formProductStatus').value = product.status;
  document.getElementById('formProductPrice').value = product.price;
  document.getElementById('formProductStock').value = product.stock;
  document.getElementById('formProductImage').value = ''; // file inputs cannot be preset
  document.getElementById('formProductDescription').value = product.description || '';
  document.getElementById('formProductImageHelp').textContent = 'Kosongkan jika tidak ingin mengubah cover.';
  document.getElementById('submitBtn').textContent = 'Simpan Perubahan';
  
  openProductModal();
}

// Close modal when clicking outside content
document.getElementById('productModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeProductModal();
  }
});
</script>

<div id="page-seller-orders" class="page active">
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
          <h2>Pesanan Masuk</h2>
          <p>Kelola pesanan buku dari pembeli yang masuk ke toko Anda</p>
        </div>
        <div class="dash-topbar-right">
          <a href="index.php?page=seller_orders&export=csv&q=<?= urlencode($filters['q'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>" class="btn-dash-ghost">
            <span>📄</span> Export CSV
          </a>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">
        
        <!-- Filters Row -->
        <div class="filter-section-card">
          <form method="GET" action="index.php" class="seller-filter-form">
            <input type="hidden" name="page" value="seller_orders">
            
            <div class="search-input-wrapper">
              <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
              <input type="text" name="q" placeholder="Cari pesanan..." value="<?= e($filters['q'] ?? '') ?>" onchange="this.form.submit()">
            </div>

            <div class="filter-button-wrapper">
              <span class="filter-btn-icon">🚦</span>
              <select name="status" onchange="this.form.submit()" class="filter-select-input">
                <option value="">Semua Status</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Dibayar</option>
                <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Diproses</option>
                <option value="shipped" <?= ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Dikirim</option>
                <option value="delivered" <?= ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Selesai</option>
                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
              </select>
            </div>
          </form>
        </div>

        <!-- Orders Cards Container -->
        <div class="orders-cards-container">
          <?php if (empty($orders)): ?>
            <div class="no-orders-placeholder">
              <span>📦</span>
              <p>Tidak ada pesanan masuk yang sesuai dengan filter saat ini.</p>
            </div>
          <?php else: ?>
            <?php foreach ($orders as $order): 
              $statusVal = $order['status'];
              
              // Status Styling
              if ($statusVal === 'pending' || $statusVal === 'paid') {
                  $statusLabel = 'Menunggu Konfirmasi';
                  $statusClass = 'o-pending';
              } elseif ($statusVal === 'processing') {
                  $statusLabel = 'Diproses';
                  $statusClass = 'o-processing';
              } elseif ($statusVal === 'shipped') {
                  $statusLabel = 'Dikirim';
                  $statusClass = 'o-shipped';
              } elseif ($statusVal === 'delivered') {
                  $statusLabel = 'Selesai';
                  $statusClass = 'o-delivered';
              } else {
                  $statusLabel = 'Dibatalkan';
                  $statusClass = 'o-cancelled';
              }
            ?>
              <div class="order-card-item">
                <div class="order-card-header">
                  <div class="order-num">#<?= e($order['invoice_number']) ?></div>
                  <span class="order-status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                </div>
                
                <div class="order-card-body">
                  <div class="buyer-details">
                    <span class="buyer-label-icon">👤</span>
                    <span class="buyer-name-text"><?= e($order['buyer_name']) ?></span>
                  </div>
                  
                  <div class="order-item-tags">
                    <?php foreach ($order['items'] as $item): ?>
                      <span class="order-book-tag">
                        📚 <?= e($item['product_name']) ?> <span class="tag-qty">× <?= $item['qty'] ?></span>
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div class="order-card-footer">
                  <div class="footer-left-info">
                    <div class="footer-price-label">Rp <?= number_format((float)$order['seller_total'], 0, ',', '.') ?></div>
                    <div class="footer-time-label">
                      🕒 <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                    </div>
                  </div>
                  
                  <div class="footer-right-actions">
                    <?php if ($statusVal === 'pending' || $statusVal === 'paid'): ?>
                      <form method="POST" action="index.php?page=seller_orders" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menolak pesanan ini?');">
                        <input type="hidden" name="action" value="seller_order_status">
                        <input type="hidden" name="status" value="cancelled">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn-order-action reject-btn">Tolak</button>
                      </form>
                      <form method="POST" action="index.php?page=seller_orders" style="display: inline;">
                        <input type="hidden" name="action" value="seller_order_status">
                        <input type="hidden" name="status" value="processing">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn-order-action confirm-btn">Konfirmasi</button>
                      </form>
                    <?php elseif ($statusVal === 'processing'): ?>
                      <button class="btn-order-action ship-btn" onclick="openShippingModal(<?= $order['id'] ?>, '<?= e($order['invoice_number']) ?>')">
                        Kirim Sekarang
                      </button>
                    <?php elseif ($statusVal === 'shipped'): ?>
                      <div class="resi-info-display">
                        Resi: <b><?= e($order['receipt_number'] ?? '-') ?></b>
                      </div>
                      <form method="POST" action="index.php?page=seller_orders" style="display: inline;">
                        <input type="hidden" name="action" value="seller_order_status">
                        <input type="hidden" name="status" value="delivered">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn-order-action confirm-btn">Selesaikan</button>
                      </form>
                    <?php else: ?>
                      <button class="btn-order-action detail-btn"
                        onclick='openDetailModal(<?= json_encode([
                          "invoice"     => $order["invoice_number"],
                          "buyer"       => $order["buyer_name"],
                          "status"      => $statusLabel,
                          "statusClass" => $statusClass,
                          "total"       => number_format((float)$order["seller_total"], 0, ",", "."),
                          "date"        => date("d M Y, H:i", strtotime($order["created_at"])),
                          "receipt"     => $order["receipt_number"] ?? "-",
                          "items"       => array_map(fn($i) => [
                            "name" => $i["product_name"],
                            "qty"  => $i["qty"],
                            "subtotal" => number_format((float)$i["subtotal"], 0, ",", ".")
                          ], $order["items"])
                        ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                        Lihat Detail
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- ORDER DETAIL MODAL -->
<div id="orderDetailModal" class="modal-overlay">
  <div class="modal-content" style="max-width:520px;">
    <div class="modal-header">
      <h3>Detail Pesanan <span id="detailInvoice" style="color:var(--accent);font-size:15px;"></span></h3>
      <button class="close-modal-btn" onclick="closeDetailModal()">✕</button>
    </div>
    <div style="padding:24px;">
      <!-- Status & Buyer -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div>
          <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Pembeli</div>
          <div id="detailBuyer" style="font-weight:700;font-size:14px;color:var(--ink);margin-top:2px;"></div>
        </div>
        <span id="detailStatus" class="order-status-badge"></span>
      </div>

      <!-- Divider -->
      <div style="border-top:1px solid #f1f5f9;margin-bottom:16px;"></div>

      <!-- Items -->
      <div style="margin-bottom:16px;">
        <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:10px;">Item Pesanan</div>
        <div id="detailItems" style="display:flex;flex-direction:column;gap:8px;"></div>
      </div>

      <!-- Divider -->
      <div style="border-top:1px solid #f1f5f9;margin-bottom:16px;"></div>

      <!-- Meta info grid -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Tanggal Pesanan</div>
          <div id="detailDate" style="font-size:13px;color:var(--ink);font-weight:600;margin-top:3px;"></div>
        </div>
        <div>
          <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Nomor Resi</div>
          <div id="detailReceipt" style="font-size:13px;color:var(--ink);font-weight:600;margin-top:3px;"></div>
        </div>
        <div style="grid-column:1/-1;">
          <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Total Pendapatan</div>
          <div id="detailTotal" style="font-family:var(--font-serif);font-size:20px;color:var(--rose);font-weight:700;margin-top:3px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SHIPPING RESI MODAL -->
<div id="shippingModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Kirim Pesanan <span id="modalInvoice"></span></h3>
      <button class="close-modal-btn" onclick="closeShippingModal()">✕</button>
    </div>
    <form id="shippingForm" method="POST" action="index.php?page=seller_orders">
      <input type="hidden" name="action" value="seller_order_status">
      <input type="hidden" name="status" value="shipped">
      <input type="hidden" name="order_id" id="shippingOrderId" value="">
      
      <div style="padding: 24px 24px 0 24px;">
        <div class="form-group">
          <label style="font-size: 13px; font-weight: 700; color: var(--ink-mid);">Nomor Resi Pengiriman</label>
          <input type="text" name="receipt_number" class="form-input" required placeholder="Contoh: REG-123456789" style="margin-top: 8px; width: 100%;">
          <small style="color: #64748b; margin-top: 6px; display: block; font-size: 11.5px;">
            Masukkan nomor resi kurir pengiriman barang untuk mempermudah pelacakan pembeli.
          </small>
        </div>
      </div>
      
      <div class="modal-footer" style="padding: 16px 24px;">
        <button type="button" class="btn-secondary" onclick="closeShippingModal()">Batal</button>
        <button type="submit" class="btn-primary">Kirim Sekarang</button>
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

/* Orders Card List Styling */
.orders-cards-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.no-orders-placeholder {
  background: #fff;
  border: 1.5px dashed var(--border);
  border-radius: 16px;
  padding: 60px 20px;
  text-align: center;
  color: #64748b;
}
.no-orders-placeholder span {
  font-size: 44px;
  display: block;
  margin-bottom: 12px;
}

.order-card-item {
  background: #fff;
  border: 1px solid var(--border-soft);
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
  display: flex;
  flex-direction: column;
  gap: 16px;
  transition: box-shadow 0.2s;
}
.order-card-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.order-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 12px;
}
.order-num {
  font-weight: 700;
  color: var(--ink);
  font-size: 14px;
}

.order-status-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 30px;
  padding: 4px 12px;
  font-size: 11px;
  font-weight: 700;
}
.order-status-badge.o-pending {
  background: #eff6ff;
  color: #1d4ed8;
}
.order-status-badge.o-processing {
  background: #fffbeb;
  color: #d97706;
}
.order-status-badge.o-shipped {
  background: #f5f3ff;
  color: #6d28d9;
}
.order-status-badge.o-delivered {
  background: #f0fdf4;
  color: #16a34a;
}
.order-status-badge.o-cancelled {
  background: #fef2f2;
  color: #dc2626;
}

.order-card-body {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.buyer-details {
  display: flex;
  align-items: center;
  gap: 8px;
}
.buyer-label-icon {
  font-size: 14px;
}
.buyer-name-text {
  font-weight: 700;
  color: var(--ink);
  font-size: 13.5px;
}
.order-item-tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.order-book-tag {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: var(--ink-mid);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.tag-qty {
  color: var(--accent);
  font-weight: 800;
}

.order-card-footer {
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}
.footer-left-info {
  display: flex;
  flex-direction: column;
}
.footer-price-label {
  font-family: var(--font-serif);
  font-size: 18px;
  font-weight: 700;
  color: var(--rose);
}
.footer-time-label {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 4px;
}

.footer-right-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}
.btn-order-action {
  border-radius: 10px;
  padding: 9px 18px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  font-family: var(--font-body);
}
.reject-btn {
  background: #fff;
  border: 1.5px solid var(--border);
  color: var(--ink-mid);
}
.reject-btn:hover {
  background: #fef2f2;
  border-color: #fca5a5;
  color: #dc2626;
}
.confirm-btn {
  background: linear-gradient(135deg, var(--accent), var(--accent-deep));
  color: #fff;
  border: none;
  box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.2);
}
.confirm-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(var(--accent-rgb), 0.3);
}
.ship-btn {
  background: linear-gradient(135deg, #d97706, #b45309);
  color: #fff;
  border: none;
  box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
}
.ship-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(217, 119, 6, 0.3);
}
.detail-btn {
  background: #f8fafc;
  border: 1px solid var(--border-soft);
  color: var(--ink-mid);
}
.detail-btn:hover {
  background: #f1f5f9;
}
.resi-info-display {
  font-size: 12.5px;
  color: #64748b;
  margin-right: 8px;
}

/* Modal styling matching products */
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
  max-width: 480px;
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
.modal-footer {
  margin-top: 24px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  border-top: 1.5px solid var(--border-soft);
}
</style>

<script>
function openShippingModal(orderId, invoice) {
  document.getElementById('shippingOrderId').value = orderId;
  document.getElementById('modalInvoice').textContent = invoice;
  document.getElementById('shippingModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeShippingModal() {
  document.getElementById('shippingModal').classList.remove('open');
  document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('shippingModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeShippingModal();
  }
});

// ── Order Detail Modal ──────────────────────────
function openDetailModal(order) {
  document.getElementById('detailInvoice').textContent  = '#' + order.invoice;
  document.getElementById('detailBuyer').textContent    = order.buyer;
  document.getElementById('detailDate').textContent     = order.date;
  document.getElementById('detailReceipt').textContent  = order.receipt || '-';
  document.getElementById('detailTotal').textContent    = 'Rp ' + order.total;

  // Status badge
  const statusEl = document.getElementById('detailStatus');
  statusEl.textContent  = order.status;
  statusEl.className    = 'order-status-badge ' + order.statusClass;

  // Items list
  const itemsEl = document.getElementById('detailItems');
  itemsEl.innerHTML = order.items.map(item => `
    <div style="display:flex;justify-content:space-between;align-items:center;
                background:#f8fafc;border-radius:10px;padding:10px 14px;">
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--ink);">📚 ${item.name}</div>
        <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Qty: ${item.qty}</div>
      </div>
      <div style="font-family:var(--font-serif);font-size:14px;font-weight:700;color:var(--ink);">
        Rp ${item.subtotal}
      </div>
    </div>
  `).join('');

  document.getElementById('orderDetailModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
  document.getElementById('orderDetailModal').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('orderDetailModal').addEventListener('click', function(e) {
  if (e.target === this) closeDetailModal();
});
</script>

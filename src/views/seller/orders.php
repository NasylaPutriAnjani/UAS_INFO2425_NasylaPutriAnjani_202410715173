<div id="page-seller-orders" class="page active">
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
            <span class="si">📊</span> Dashboard
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

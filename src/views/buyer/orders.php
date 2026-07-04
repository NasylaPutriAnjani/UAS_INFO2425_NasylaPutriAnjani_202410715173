<?php $buyerMenu = 'orders'; ?>
<div id="page-buyer-orders" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>📦 Pesanan Saya</h2>
          <p>Lacak dan kelola semua pesanan buku kamu</p>
        </div>
      </div>
      <div class="dash-body">
        <div class="buyer-panel">
          <?php if (empty($orders)): ?>
            <div class="buyer-empty">
              <div class="buyer-empty-icon">📦</div>
              <p>Belum ada pesanan. Yuk beli buku pertamamu!</p>
              <a href="index.php?page=catalog" class="btn-dash-primary">Mulai Belanja</a>
            </div>
          <?php else: ?>
            <div class="buyer-table-wrap">
              <table class="buyer-table">
                <thead>
                  <tr>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td class="buyer-table-mono"><?= e($order['invoice_number']) ?></td>
                      <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                      <td><span class="buyer-status buyer-status-<?= e($order['status']) ?>"><?= e(order_status_label($order['status'])) ?></span></td>
                      <td><strong><?= rupiah((int) $order['total']) ?></strong></td>
                      <td>
                        <?php if ($order['status'] === 'delivered'): ?>
                          <button type="button" class="btn-dash-primary" style="padding: 4px 12px; font-size: 12px;" onclick="openReviewModal(<?= htmlspecialchars(json_encode($order['items'])) ?>)">Beri Review</button>
                        <?php else: ?>
                          <span style="color:#999;font-size:13px">Menunggu Selesai</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay" id="reviewOverlay" onclick="closeReviewModal()"></div>
<div class="cart-drawer" id="reviewModal" style="max-width: 400px; left: 50%; top: 50%; transform: translate(-50%, -50%); right: auto; bottom: auto; height: auto; border-radius: 12px; opacity: 0; pointer-events: none; transition: opacity 0.3s; z-index: 10000; position: fixed; background: #fff;">
  <div class="cart-drawer-head" style="padding: 20px; border-bottom: 1px solid #eee;">
    <h3 style="margin: 0;">⭐ Beri Review</h3>
    <button class="close-btn" onclick="closeReviewModal()" style="background: none; border: none; font-size: 20px; cursor: pointer;">✕</button>
  </div>
  <div style="padding: 20px;">
    <form method="POST" action="index.php?action=submit_review" id="reviewForm">
      <div class="form-group">
        <label>Pilih Buku</label>
        <select name="product_id" id="reviewProductId" class="form-input" required>
        </select>
      </div>
      <div class="form-group">
        <label>Rating</label>
        <select name="rating" class="form-input" required>
          <option value="5">★★★★★ (5)</option>
          <option value="4">★★★★☆ (4)</option>
          <option value="3">★★★☆☆ (3)</option>
          <option value="2">★★☆☆☆ (2)</option>
          <option value="1">★☆☆☆☆ (1)</option>
        </select>
      </div>
      <div class="form-group">
        <label>Komentar</label>
        <textarea name="comment" class="form-input" rows="3" placeholder="Bagikan pendapatmu tentang buku ini..."></textarea>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">Kirim Review</button>
    </form>
  </div>
</div>

<script>
function openReviewModal(items) {
  const select = document.getElementById('reviewProductId');
  select.innerHTML = '';
  items.forEach(item => {
    const opt = document.createElement('option');
    opt.value = item.product_id;
    opt.textContent = item.name;
    select.appendChild(opt);
  });
  document.getElementById('reviewOverlay').classList.add('open');
  const modal = document.getElementById('reviewModal');
  modal.style.opacity = '1';
  modal.style.pointerEvents = 'auto';
}

function closeReviewModal() {
  document.getElementById('reviewOverlay').classList.remove('open');
  const modal = document.getElementById('reviewModal');
  modal.style.opacity = '0';
  modal.style.pointerEvents = 'none';
}
</script>

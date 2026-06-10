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
                    <th>Resi</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td class="buyer-table-mono"><?= e($order['invoice_number']) ?></td>
                      <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                      <td><span class="buyer-status buyer-status-<?= e($order['status']) ?>"><?= e(order_status_label($order['status'])) ?></span></td>
                      <td><?= e($order['receipt_number'] ?: '—') ?></td>
                      <td><strong><?= rupiah((int) $order['total']) ?></strong></td>
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

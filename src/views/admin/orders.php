<div id="page-admin_orders" class="page active admin-page">
  <div class="dash-layout admin-layout">

    <?php $adminActivePage = 'admin_orders'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="dash-content">
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <div class="admin-topbar-title">
            <h2>🛒 Semua Pesanan</h2>
            <p>Monitoring transaksi dari seluruh pembeli dan penjual</p>
          </div>
        </div>
        <div class="admin-topbar-right">
          <form method="GET" action="index.php" class="admin-search-form">
            <input type="hidden" name="page" value="admin_orders">
            <input class="admin-search-input" type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari invoice, pembeli, status...">
            <button class="btn-admin-ghost">Cari</button>
            <?php if (!empty($q)): ?>
              <a href="index.php?page=admin_orders" class="admin-action-btn">Reset</a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <div class="admin-body">
        <div class="admin-table-card">
          <div class="admin-table-head">
            <div>
              <h4>Transaksi Platform</h4>
              <p>Total: <strong><?= count($orders ?? []) ?> pesanan</strong></p>
            </div>
          </div>
          <div class="admin-table-responsive">
            <table class="admin-data-table">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Pembeli</th>
                  <th>Penjual</th>
                  <th>Total</th>
                  <th>Pembayaran</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($orders)): ?>
                  <tr><td colspan="7" style="text-align:center;color:var(--ink-muted);">Belum ada pesanan.</td></tr>
                <?php else: ?>
                  <?php foreach ($orders as $order): ?>
                    <?php
                      $statusClass = match ($order['status']) {
                        'delivered' => 'sp-done',
                        'shipped' => 'sp-ship',
                        'paid', 'processing' => 'sp-paid',
                        'cancelled' => 'sp-inactive',
                        default => 'sp-pending',
                      };
                    ?>
                    <tr>
                      <td><span class="td-mono">#<?= e($order['invoice_number']) ?></span></td>
                      <td><?= e($order['buyer_name']) ?></td>
                      <td style="color:#64748b;font-size:12px"><?= e($order['seller_names'] ?: 'RubbyBooks') ?></td>
                      <td><span class="td-amount"><?= rupiah((int)$order['total']) ?></span></td>
                      <td><span class="td-method"><?= e($order['payment_method'] ?? '-') ?></span></td>
                      <td><span class="status-pill <?= $statusClass ?>"><?= ucfirst(e($order['status'])) ?></span></td>
                      <td style="font-size:12px;color:var(--ink-muted);"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
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

<?php
global $pdo;
require_login();
$checkoutItems = cart_items($pdo);
if (empty($checkoutItems)) {
    redirect('catalog');
}
$checkoutSubtotal = 0;
foreach ($checkoutItems as $item) {
    $checkoutSubtotal += (int)$item['price'] * (int)$item['qty'];
}
$defaultShipping = shipping_cost('');
?>
<div id="page-checkout" class="page active">
  <div class="checkout-page">
    <div class="checkout-steps">
      <div class="cs-step done"><div class="cs-num">✓</div><div><div class="cs-label">Keranjang</div></div></div>
      <div class="cs-step active"><div class="cs-num">2</div><div><div class="cs-label">Pengiriman & Pembayaran</div></div></div>
      <div class="cs-step"><div class="cs-num">3</div><div><div class="cs-label">Konfirmasi Pesanan</div></div></div>
      <div class="cs-step"><div class="cs-num">4</div><div><div class="cs-label">Selesai</div></div></div>
    </div>
    
    <form action="index.php?action=checkout" method="POST" enctype="multipart/form-data">
      <div class="checkout-grid">
        <div>
          <div class="co-card">
            <h3>📍 Alamat Pengiriman</h3>
            <div class="form-row">
              <div class="form-group">
                <label>Nama Lengkap Penerima</label>
                <input type="text" name="recipient_name" class="form-input" placeholder="Nama Lengkap" required>
              </div>
              <div class="form-group">
                <label>Nomor Telepon</label>
                <input type="text" name="phone" class="form-input numeric-only" placeholder="Nomor Telepon" inputmode="numeric" pattern="[0-9]+" autocomplete="tel" required>
              </div>
            </div>
            <div class="form-group">
              <label>Alamat Lengkap</label>
              <input type="text" name="address" class="form-input" placeholder="Alamat Lengkap" required>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Kota</label>
                <input type="text" name="city" id="checkoutCity" class="form-input" placeholder="Masukkan kota" autocomplete="address-level2" required oninput="updateCheckoutTotal()">
              </div>
              <div class="form-group">
                <label>Kode Pos</label>
                <input type="text" name="postal_code" class="form-input numeric-only" placeholder="Kode Pos" inputmode="numeric" pattern="[0-9]+" autocomplete="postal-code" required>
              </div>
            </div>
            
            <div class="co-section-label">Pilih Ekspedisi</div>
            <div class="pay-grid" style="grid-template-columns: 1fr;">
              <!-- Kita menggunakan sistem shipping cost dinamis dari kota saja di backend -->
              <div class="pay-opt active"><span class="pi">🚚</span><div><div style="font-size:13px;font-weight:700">Reguler</div><div style="font-size:11px;color:var(--ink-muted)">Otomatis dihitung berdasarkan kota</div></div></div>
            </div>
            
            <div class="co-section-label">Metode Pembayaran</div>
            <div class="pay-grid">
              <label class="pay-opt active" onclick="selectPay(this)" style="cursor:pointer">
                <input type="radio" name="method" value="Transfer Bank" checked style="display:none">
                <span class="pi">🏦</span><div><div style="font-size:12.5px;font-weight:700">Transfer Bank</div></div>
              </label>
              <label class="pay-opt" onclick="selectPay(this)" style="cursor:pointer">
                <input type="radio" name="method" value="E-Wallet" style="display:none">
                <span class="pi">📱</span><div><div style="font-size:12.5px;font-weight:700">E-Wallet</div></div>
              </label>
            </div>
            
            <div class="form-group" style="margin-top:16px">
              <label>Upload Bukti Pembayaran</label>
              <input type="file" name="proof" class="form-input" accept="image/jpeg,image/png,image/webp" required style="padding: 10px;">
            </div>
          </div>
        </div>
        
        <div>
          <div class="co-card">
            <h3>🛒 Ringkasan Pesanan</h3>
            <div style="max-height: 300px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px;">
              <?php foreach ($checkoutItems as $item): 
                $bcClass = 'bc' . (($item['id'] % 6) + 1);
                $summaryCoverStyle = !empty($item['image'])
                  ? "background-image:url('" . e(asset($item['image'])) . "');background-size:cover;background-position:center;font-size:0;"
                  : '';
              ?>
                <div class="sum-item">
                  <div class="sum-cover <?= $bcClass ?>" style="<?= $summaryCoverStyle ?>"><?= empty($item['image']) ? e($item['name']) : '' ?></div>
                  <div class="ci-info">
                    <div class="sum-title"><?= e($item['name']) ?></div>
                    <div class="sum-qty">Qty: <?= (int)$item['qty'] ?></div>
                  </div>
                  <div class="sum-price"><?= rupiah($item['price']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="tot-rows">
              <div class="tot-row"><span>Subtotal (<?= count($checkoutItems) ?> item)</span><span id="coSubtotal" data-value="<?= $checkoutSubtotal ?>"><?= rupiah($checkoutSubtotal) ?></span></div>
              <div class="tot-row"><span>Ongkos Kirim</span><span id="coShipping" data-value="<?= $defaultShipping ?>"><?= rupiah($defaultShipping) ?></span></div>
              <div class="tot-row grand"><span>Total</span><span id="coTotal"><?= rupiah($checkoutSubtotal + $defaultShipping) ?></span></div>
            </div>
            <button type="submit" class="btn-checkout">Buat Pesanan →</button>
            <p style="text-align:center;font-size:11.5px;color:var(--ink-muted);margin-top:14px;">🔒 Transaksi aman & terenkripsi · 100% terjamin</p>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

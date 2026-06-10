<div id="page-checkout" class="page active">
  <div class="checkout-page">
    <div class="checkout-steps">
      <div class="cs-step done"><div class="cs-num">✓</div><div><div class="cs-label">Keranjang</div></div></div>
      <div class="cs-step active"><div class="cs-num">2</div><div><div class="cs-label">Pengiriman & Pembayaran</div></div></div>
      <div class="cs-step"><div class="cs-num">3</div><div><div class="cs-label">Konfirmasi Pesanan</div></div></div>
      <div class="cs-step"><div class="cs-num">4</div><div><div class="cs-label">Selesai</div></div></div>
    </div>
    <div class="checkout-grid">
      <div>
        <div class="co-card">
          <h3>📍 Alamat Pengiriman</h3>
          <div class="form-row">
            <div class="form-group"><label>Nama Depan</label><input class="form-input" placeholder="Sari"></div>
            <div class="form-group"><label>Nama Belakang</label><input class="form-input" placeholder="Rahayu"></div>
          </div>
          <div class="form-group"><label>Nomor Telepon</label><input class="form-input" placeholder="+62 812 3456 7890"></div>
          <div class="form-group"><label>Alamat Lengkap</label><input class="form-input" placeholder="Jl. Sudirman No. 10, Kec. Setiabudi"></div>
          <div class="form-row">
            <div class="form-group"><label>Kota</label><select class="form-select form-input"><option>Jakarta Selatan</option><option>Surabaya</option><option>Bandung</option><option>Bekasi</option></select></div>
            <div class="form-group"><label>Kode Pos</label><input class="form-input" placeholder="12910"></div>
          </div>
          <div class="co-section-label">Pilih Ekspedisi</div>
          <div class="pay-grid">
            <div class="pay-opt active" onclick="selectPay(this)"><span class="pi">🚚</span><div><div style="font-size:13px;font-weight:700">JNE Reguler</div><div style="font-size:11px;color:var(--ink-muted)">2-4 hari · Rp 18.000</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">⚡</span><div><div style="font-size:13px;font-weight:700">J&T Express</div><div style="font-size:11px;color:var(--ink-muted)">1-3 hari · Rp 22.000</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">🏃</span><div><div style="font-size:13px;font-weight:700">SiCepat</div><div style="font-size:11px;color:var(--ink-muted)">1-2 hari · Rp 25.000</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">📫</span><div><div style="font-size:13px;font-weight:700">Pos Indonesia</div><div style="font-size:11px;color:var(--ink-muted)">3-7 hari · Rp 12.000</div></div></div>
          </div>
          <div class="co-section-label">Metode Pembayaran</div>
          <div class="pay-grid">
            <div class="pay-opt active" onclick="selectPay(this)"><span class="pi">🏦</span><div><div style="font-size:12.5px;font-weight:700">Transfer Bank</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">💳</span><div><div style="font-size:12.5px;font-weight:700">Kartu Kredit</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">💚</span><div><div style="font-size:12.5px;font-weight:700">GoPay</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">💙</span><div><div style="font-size:12.5px;font-weight:700">OVO</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">🟣</span><div><div style="font-size:12.5px;font-weight:700">DANA</div></div></div>
            <div class="pay-opt" onclick="selectPay(this)"><span class="pi">🔵</span><div><div style="font-size:12.5px;font-weight:700">ShopeePay</div></div></div>
          </div>
          <div class="form-group" style="margin-top:16px">
            <label>Kode Promo (opsional)</label>
            <div style="display:flex;gap:10px">
              <input class="form-input" style="flex:1" placeholder="Masukkan kode promo">
              <button class="btn-sm-primary" onclick="showToast('✅ Kode promo berhasil!')">Terapkan</button>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="co-card">
          <h3>🛒 Ringkasan Pesanan</h3>
          <div class="sum-item">
            <div class="sum-cover bc1">Atomic Habits</div>
            <div class="ci-info"><div class="sum-title">Atomic Habits</div><div class="sum-qty">Qty: 1 · James Clear</div></div>
            <div class="sum-price">Rp 89.000</div>
          </div>
          <div class="sum-item">
            <div class="sum-cover bc2">Laskar Pelangi</div>
            <div class="ci-info"><div class="sum-title">Laskar Pelangi</div><div class="sum-qty">Qty: 2 · Andrea Hirata</div></div>
            <div class="sum-price">Rp 130.000</div>
          </div>
          <div class="sum-item">
            <div class="sum-cover bc3">Midnight Library</div>
            <div class="ci-info"><div class="sum-title">The Midnight Library</div><div class="sum-qty">Qty: 1 · Matt Haig</div></div>
            <div class="sum-price">Rp 76.000</div>
          </div>
          <div class="tot-rows">
            <div class="tot-row"><span>Subtotal (4 item)</span><span>Rp 295.000</span></div>
            <div class="tot-row"><span>Ongkos Kirim</span><span>Rp 18.000</span></div>
            <div class="tot-row"><span>Diskon Promo</span><span style="color:var(--rose)">- Rp 18.000</span></div>
            <div class="tot-row"><span>Asuransi Pengiriman</span><span>Rp 3.000</span></div>
            <div class="tot-row grand"><span>Total</span><span>Rp 298.000</span></div>
          </div>
          <button class="btn-checkout" onclick="showToast('🎉 Pesanan berhasil dibuat! Nomor pesanan: RB-20250531-001')">Buat Pesanan →</button>
          <p style="text-align:center;font-size:11.5px;color:var(--ink-muted);margin-top:14px;">🔒 Transaksi aman & terenkripsi · 100% terjamin</p>
        </div>
      </div>
    </div>
  </div>
</div>
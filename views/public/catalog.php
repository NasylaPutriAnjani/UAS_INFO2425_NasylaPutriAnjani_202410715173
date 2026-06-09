<div id="page-catalog" class="page active">
  <div class="catalog-layout">
    <aside class="catalog-sidebar">
      <div class="filter-group">
        <div class="filter-title">Kategori</div>
        <div class="filter-item"><input type="checkbox" checked> Semua Kategori</div>
        <div class="filter-item"><input type="checkbox"> Roman & Fiksi</div>
        <div class="filter-item"><input type="checkbox"> Bisnis & Karier</div>
        <div class="filter-item"><input type="checkbox"> Pengembangan Diri</div>
        <div class="filter-item"><input type="checkbox"> Fantasi & Sci-Fi</div>
        <div class="filter-item"><input type="checkbox"> Sastra Klasik</div>
        <div class="filter-item"><input type="checkbox"> Keuangan</div>
        <div class="filter-item"><input type="checkbox"> Pendidikan</div>
      </div>
      <div class="filter-group">
        <div class="filter-title">Rentang Harga</div>
        <div class="price-range">
          <input type="range" class="range-input" min="0" max="300000" value="200000" oninput="updatePrice(this)">
          <div class="range-vals"><span>Rp 0</span><span id="price-max">Rp 200.000</span></div>
        </div>
      </div>
      <div class="filter-group">
        <div class="filter-title">Rating</div>
        <div class="filter-item"><input type="checkbox"> ★★★★★ (5.0)</div>
        <div class="filter-item"><input type="checkbox"> ★★★★☆ (4.0+)</div>
        <div class="filter-item"><input type="checkbox"> ★★★☆☆ (3.0+)</div>
      </div>
      <div class="filter-group">
        <div class="filter-title">Kondisi Buku</div>
        <div class="filter-item"><input type="checkbox" checked> Baru</div>
        <div class="filter-item"><input type="checkbox"> Bekas - Baik</div>
        <div class="filter-item"><input type="checkbox"> Bekas - Cukup</div>
      </div>
      <button class="btn-primary" style="width:100%;justify-content:center;margin-top:8px" onclick="showToast('🔍 Filter diterapkan!')">Terapkan Filter</button>
    </aside>
    <div class="catalog-main">
      <div class="catalog-toolbar">
        <div class="catalog-results">Menampilkan <b>128 buku</b> dari 12.480 judul</div>
        <select class="sort-select">
          <option>Terlaris</option>
          <option>Terbaru</option>
          <option>Harga Terendah</option>
          <option>Harga Tertinggi</option>
          <option>Rating Tertinggi</option>
        </select>
      </div>
      <div class="book-grid">
        <div class="book-card"><div class="book-cover-lg bc1"><span class="badge badge-new">Baru</span>Atomic Habits<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Pengembangan Diri</div><div class="book-title">Atomic Habits</div><div class="book-author">James Clear</div><div class="book-rating"><span class="stars">★★★★★</span><span class="rating-count">4.9</span></div><div class="book-footer"><div class="book-price">Rp 89.000<span class="orig">Rp 120.000</span></div><button class="btn-add" onclick="addToCart(event,'Atomic Habits')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc2">Laskar Pelangi<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Roman Indonesia</div><div class="book-title">Laskar Pelangi</div><div class="book-author">Andrea Hirata</div><div class="book-rating"><span class="stars">★★★★★</span><span class="rating-count">4.9</span></div><div class="book-footer"><div class="book-price">Rp 65.000</div><button class="btn-add" onclick="addToCart(event,'Laskar Pelangi')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc3"><span class="badge badge-sale">-20%</span>The Midnight Library<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Fiksi Internasional</div><div class="book-title">The Midnight Library</div><div class="book-author">Matt Haig</div><div class="book-rating"><span class="stars">★★★★☆</span><span class="rating-count">4.7</span></div><div class="book-footer"><div class="book-price">Rp 76.000<span class="orig">Rp 95.000</span></div><button class="btn-add" onclick="addToCart(event,'Midnight Library')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc4">Rich Dad Poor Dad<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Keuangan</div><div class="book-title">Rich Dad Poor Dad</div><div class="book-author">Robert Kiyosaki</div><div class="book-rating"><span class="stars">★★★★★</span><span class="rating-count">4.8</span></div><div class="book-footer"><div class="book-price">Rp 75.000</div><button class="btn-add" onclick="addToCart(event,'Rich Dad Poor Dad')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc5">Bumi Manusia<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Sastra Klasik</div><div class="book-title">Bumi Manusia</div><div class="book-author">Pramoedya A. Toer</div><div class="book-rating"><span class="stars">★★★★★</span><span class="rating-count">5.0</span></div><div class="book-footer"><div class="book-price">Rp 85.000</div><button class="btn-add" onclick="addToCart(event,'Bumi Manusia')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc6"><span class="badge badge-new">Baru</span>Psychology of Money<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Psikologi & Keuangan</div><div class="book-title">The Psychology of Money</div><div class="book-author">Morgan Housel</div><div class="book-rating"><span class="stars">★★★★★</span><span class="rating-count">4.9</span></div><div class="book-footer"><div class="book-price">Rp 92.000</div><button class="btn-add" onclick="addToCart(event,'Psychology of Money')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc1">Ikigai<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Pengembangan Diri</div><div class="book-title">Ikigai</div><div class="book-author">Héctor García</div><div class="book-rating"><span class="stars">★★★★☆</span><span class="rating-count">4.6</span></div><div class="book-footer"><div class="book-price">Rp 78.000</div><button class="btn-add" onclick="addToCart(event,'Ikigai')">+</button></div></div></div>
        <div class="book-card"><div class="book-cover-lg bc2">Sapiens<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button></div><div class="book-body"><div class="book-genre">Sejarah & Sains</div><div class="book-title">Sapiens</div><div class="book-author">Yuval Noah Harari</div><div class="book-rating"><span class="stars">★★★★★</span><span class="rating-count">4.8</span></div><div class="book-footer"><div class="book-price">Rp 115.000</div><button class="btn-add" onclick="addToCart(event,'Sapiens')">+</button></div></div></div>
      </div>
      <div class="page-pagination">
        <button aria-label="Sebelumnya">‹</button>
        <button class="active">1</button>
        <button>2</button>
        <button>3</button>
        <span class="page-dots">…</span>
        <button>12</button>
        <button aria-label="Berikutnya">›</button>
      </div>
    </div>
  </div>
</div>
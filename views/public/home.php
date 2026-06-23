<!-- ── HOME ── -->
<div id="page-home" class="page active">

  <!-- HERO -->
  <section class="hero">
    <div class="hero-bg-blob b1"></div>
    <div class="hero-bg-blob b2"></div>
    <div class="hero-content">
      <div class="hero-tag"><span class="pulse"></span> ✨ Koleksi Terbaru 2025</div>
      <h1 class="hero-h1">Temukan <em>Buku Impianmu</em> di RubbyBooks</h1>
      <p class="hero-p">Ribuan judul pilihan — dari fiksi romantis hingga pengembangan diri — dikirim langsung ke pintu rumahmu dengan harga terbaik dan gratis ongkir.</p>
      <div class="hero-actions">
        <button class="btn-primary" onclick="showPage('catalog');setActiveByName('Katalog')">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          Jelajahi Buku
        </button>
        <button class="btn-secondary" data-role="buyer" onclick="openAuth('seller')">✍️ Mulai Jual Buku</button>
      </div>
      <div class="hero-stats">
        <div class="hero-stat">
          <div class="num">12K+</div>
          <div class="lbl">Judul Buku</div>
        </div>
        <div class="hero-stat">
          <div class="num">4.8★</div>
          <div class="lbl">Rating</div>
        </div>
        <div class="hero-stat">
          <div class="num">86K+</div>
          <div class="lbl">Pembeli Puas</div>
        </div>
        <div class="hero-stat">
          <div class="num">500+</div>
          <div class="lbl">Penjual Aktif</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="bk-sm">
        <div class="bk-cover-sm bc1">Atomic<br>Habits</div>
        <div class="bk-info-sm">
          <div class="t">Atomic Habits</div>
          <div class="a">James Clear</div>
          <div class="stars">★★★★★</div>
          <div class="p">Rp 89.000</div>
        </div>
      </div>
      <div class="bk-sm">
        <div class="bk-cover-sm bc2">Laskar<br>Pelangi</div>
        <div class="bk-info-sm">
          <div class="t">Laskar Pelangi</div>
          <div class="a">Andrea Hirata</div>
          <div class="stars">★★★★★</div>
          <div class="p">Rp 65.000</div>
        </div>
      </div>
      <div class="bk-sm">
        <div class="bk-cover-sm bc3">The Midnight<br>Library</div>
        <div class="bk-info-sm">
          <div class="t">Midnight Library</div>
          <div class="a">Matt Haig</div>
          <div class="stars">★★★★☆</div>
          <div class="p">Rp 95.000</div>
        </div>
      </div>
      <div class="bk-sm">
        <div class="bk-cover-sm bc4">Rich Dad<br>Poor Dad</div>
        <div class="bk-info-sm">
          <div class="t">Rich Dad Poor Dad</div>
          <div class="a">R. Kiyosaki</div>
          <div class="stars">★★★★★</div>
          <div class="p">Rp 75.000</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CATEGORIES -->
  <div class="cat-strip">
    <div class="cat-chip active" onclick="filterCat(this)"><span class="ci">📖</span> Semua</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">💕</span> Roman & Fiksi</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">💼</span> Bisnis & Karier</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">🌱</span> Pengembangan Diri</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">🔮</span> Fantasi & Sci-Fi</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">🍳</span> Kuliner & Gaya Hidup</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">🎓</span> Pendidikan</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">👶</span> Anak & Remaja</div>
    <div class="cat-chip" onclick="filterCat(this)"><span class="ci">🕌</span> Agama & Spiritualitas</div>
  </div>

  <!-- BESTSELLERS -->
  <section class="section">
    <div class="sec-head">
      <h2 class="sec-title">🔥 Buku <span>Terlaris</span></h2>
      <a class="sec-link" href="#" onclick="showPage('catalog');return false">Lihat semua <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M5 12h14M12 5l7 7-7 7" />
        </svg></a>
    </div>
    <div class="book-grid">
      <?php foreach ($products as $book): 
        $rating = 5.0; // default rating or calculate from db
        $stars = '★★★★★';
        $bcClass = 'bc' . (($book['id'] % 6) + 1);
      ?>
      <div class="book-card">
        <?php if (!empty($book['image'])): ?>
          <div class="book-cover-lg" style="background-image: url('<?= e(asset($book['image'])) ?>'); background-size: cover; background-position: center; font-size:0;">
            <?php if ($book['book_condition'] === 'new'): ?><span class="badge badge-new">Baru</span><?php endif; ?>
            <button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button>
          </div>
        <?php else: ?>
          <div class="book-cover-lg <?= $bcClass ?>">
            <?php if ($book['book_condition'] === 'new'): ?>
              <span class="badge badge-new">Baru</span>
            <?php endif; ?>
            <?= e($book['name']) ?>
            <button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this)">♡</button>
          </div>
        <?php endif; ?>
        <div class="book-body">
          <div class="book-genre"><?= e($book['category']) ?></div>
          <div class="book-title"><?= e($book['name']) ?></div>
          <div class="book-author">Stok: <?= (int)$book['stock'] ?></div>
          <div class="book-rating"><span class="stars"><?= $stars ?></span><span class="rating-count"><?= number_format($rating, 1) ?></span></div>
          <div class="book-footer">
            <div class="book-price"><?= rupiah($book['price']) ?></div>
            <button class="btn-add" onclick="event.preventDefault(); addToCart(event, <?= $book['id'] ?>, '<?= e(addslashes($book['name'])) ?>')">+ Keranjang</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="sec-divider"></div>

  <!-- FEATURED BOOK -->
  <section class="section">
    <div class="sec-head">
      <h2 class="sec-title">⭐ Pilihan <span>Editor</span></h2>
    </div>
    <?php if ($featured): ?>
    <div class="featured-book">
      <?php if (!empty($featured['image'])): ?>
        <div class="featured-cover" style="background-image: url('<?= e(asset($featured['image'])) ?>'); background-size: cover; background-position: center; color: transparent;"></div>
      <?php else: ?>
        <div class="featured-cover bc1"><?= e($featured['name']) ?></div>
      <?php endif; ?>
      <div>
        <div class="featured-tags">
          <span class="tag-chip">📚 <?= e($featured['category']) ?></span>
          <span class="tag-chip">⭐ Pilihan Editor</span>
        </div>
        <div class="featured-title"><?= e($featured['name']) ?></div>
        <div class="featured-author">Stok tersedia: <b><?= (int)$featured['stock'] ?></b></div>
        <p class="featured-desc"><?= e($featured['description'] ?? 'Sebuah karya luar biasa yang wajib Anda baca.') ?></p>
        <div class="featured-rating">
          <span class="stars">★★★★★</span>
          <span>5.0</span>
          <span>·</span>
          <span>🛒 Terlaris</span>
        </div>
        <div class="big-price"><?= rupiah($featured['price']) ?></div>
        <div class="price-actions">
          <button class="btn-primary" onclick="event.preventDefault(); addToCart(event, <?= $featured['id'] ?>, '<?= e(addslashes($featured['name'])) ?>')">🛒 Tambah ke Keranjang</button>
          <button class="btn-secondary" onclick="toggleWishFeatured(this)">♡ Wishlist</button>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </section>

  <!-- SELLER CTA -->
  <div class="seller-cta-section" id="section-cara-kerja" data-role="buyer">
    <h2>Punya Buku untuk Dijual? ✍️</h2>
    <p>Bergabunglah dengan 500+ penjual aktif di RubbyBooks. Mudah, aman, dan komisi transparan.</p>
    <div class="seller-steps">
      <div class="seller-step">
        <div class="step-icon">📝</div>
        <div class="step-num">1</div>
        <p>Daftar sebagai Penjual</p>
      </div>
      <div class="seller-step">
        <div class="step-icon">📸</div>
        <div class="step-num">2</div>
        <p>Upload produk & harga</p>
      </div>
      <div class="seller-step">
        <div class="step-icon">📦</div>
        <div class="step-num">3</div>
        <p>Terima pesanan & kirim</p>
      </div>
      <div class="seller-step">
        <div class="step-icon">💸</div>
        <div class="step-num">4</div>
        <p>Terima pembayaran otomatis</p>
      </div>
    </div>
    <button class="btn-primary" onclick="openAuth()">🚀 Mulai Berjualan Gratis</button>
  </div>

  <!-- TESTIMONIALS -->
  <section class="section" id="section-testimoni">
    <div class="sec-head">
      <h2 class="sec-title">💬 Apa Kata <span>Pembeli Kami</span></h2>
    </div>
    <div class="testi-grid">
      <div class="testi-card">
        <p class="testi-body">"Pengiriman super cepat! Buku datang dalam kondisi sempurna, dikemas dengan cantik. Bakal beli lagi terus dari RubbyBooks."</p>
        <div class="testi-user">
          <div class="avatar">SR</div>
          <div>
            <div class="testi-name">Sari Rahayu</div>
            <div class="testi-loc">📍 Surabaya · ★★★★★</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <p class="testi-body">"Harganya jauh lebih murah dibanding toko buku fisik. Koleksinya lengkap banget, dari buku lokal sampai impor ada semua!"</p>
        <div class="testi-user">
          <div class="avatar" style="background:linear-gradient(135deg,#1e40af,#1d4ed8)">BW</div>
          <div>
            <div class="testi-name">Bimo Wicaksono</div>
            <div class="testi-loc">📍 Bandung · ★★★★★</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <p class="testi-body">"Sebagai penjual, dashboard-nya sangat mudah dipakai. Bisa pantau penjualan real-time. Komisi juga sangat transparan!"</p>
        <div class="testi-user">
          <div class="avatar" style="background:linear-gradient(135deg,#065f46,#047857)">DL</div>
          <div>
            <div class="testi-name">Dewi Lestari</div>
            <div class="testi-loc">📍 Penjual Jakarta · ★★★★★</div>
          </div>
        </div>
      </div>
    </div>
  </section>
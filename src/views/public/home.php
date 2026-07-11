<?php
// Category emoji map based on common category names
$catEmojiMap = [
    'novel'        => '💕',
    'komik'        => '🎨',
    'teknologi'    => '💻',
    'bisnis'       => '💼',
    'agama'        => '🕌',
    'sejarah'      => '🏛️',
    'pendidikan'   => '🎓',
    'anak'         => '👶',
    'fantasi'      => '🔮',
    'kuliner'      => '🍳',
    'pengembangan' => '🌱',
    'default'      => '📖',
];
$getCatEmoji = function(string $name) use ($catEmojiMap): string {
    $lower = strtolower($name);
    foreach ($catEmojiMap as $key => $emoji) {
        if (str_contains($lower, $key)) return $emoji;
    }
    return $catEmojiMap['default'];
};

// Color classes for hero book cards
$bcColors = ['bc1','bc2','bc3','bc4','bc5','bc6'];
?>
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
          <div class="num"><?= $heroStats['products'] ?>+</div>
          <div class="lbl">Judul Buku</div>
        </div>
        <div class="hero-stat">
          <div class="num"><?= $heroStats['rating'] ?></div>
          <div class="lbl">Rating</div>
        </div>
        <div class="hero-stat">
          <div class="num"><?= $heroStats['buyers'] ?>+</div>
          <div class="lbl">Pembeli Puas</div>
        </div>
        <div class="hero-stat">
          <div class="num"><?= $heroStats['sellers'] ?>+</div>
          <div class="lbl">Penjual Aktif</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <?php foreach ($heroProducts as $i => $hb):
        $bc = $bcColors[$i % count($bcColors)];
        $titleWords = explode(' ', $hb['name']);
        $heroTitle  = implode('<br>', array_chunk($titleWords, 2) ? [implode(' ', array_slice($titleWords, 0, 2)), implode(' ', array_slice($titleWords, 2, 2))] : $titleWords);
      ?>
      <div class="bk-sm">
        <?php if (!empty($hb['image'])): ?>
          <div class="bk-cover-sm <?= $bc ?>" style="background-image:url('<?= e(asset($hb['image'])) ?>');background-size:cover;background-position:center;font-size:0;"></div>
        <?php else: ?>
          <div class="bk-cover-sm <?= $bc ?>"><?= $heroTitle ?></div>
        <?php endif; ?>
        <div class="bk-info-sm">
          <div class="t"><?= e($hb['name']) ?></div>
          <div class="a"><?= e($hb['category']) ?></div>
          <div class="stars">★★★★★</div>
          <div class="p"><?= rupiah($hb['price']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- CATEGORIES -->
  <div class="cat-strip">
    <div class="cat-chip active" onclick="filterCat(this)"><span class="ci">📖</span> Semua</div>
    <?php foreach ($homeCategories as $cat): ?>
      <div class="cat-chip" data-id="<?= $cat['id'] ?>" onclick="filterCat(this)">
        <span class="ci"><?= $getCatEmoji($cat['name']) ?></span> <?= e($cat['name']) ?>
      </div>
    <?php endforeach; ?>
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
        $rating = round((float)($book['avg_rating'] ?? 0), 1);
        $stars = $rating >= 4.5 ? '★★★★★' : ($rating >= 3.5 ? '★★★★☆' : ($rating >= 2.5 ? '★★★☆☆' : ($rating >= 1.5 ? '★★☆☆☆' : '★☆☆☆☆')));
        if ($rating == 0) { $stars = '☆☆☆☆☆'; }
        $bcClass = 'bc' . (($book['id'] % 6) + 1);
      ?>
      <div class="book-card" onclick="window.location='index.php?page=product&id=<?= $book['id'] ?>'" style="cursor:pointer">
        <?php $inWishlist = is_in_wishlist($GLOBALS['pdo'], $book['id']); ?>
        <?php if (!empty($book['image'])): ?>
          <div class="book-cover-lg" style="background-image: url('<?= e(asset($book['image'])) ?>'); background-size: cover; background-position: center; font-size:0;">
            <?php if ($book['book_condition'] === 'new'): ?><span class="badge badge-new">Baru</span><?php endif; ?>
            <form method="POST" action="index.php?action=toggle_wishlist" style="position: absolute; top: 12px; right: 12px; z-index: 10; margin: 0;" onclick="event.stopPropagation()">
              <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
              <button type="submit" title="Wishlist" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); border: none; display: flex; align-items: center; justify-content: center; font-size: 15px; color: <?= $inWishlist ? '#f43f5e' : '#a1a1aa' ?>; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"><?= $inWishlist ? '♥' : '♡' ?></button>
            </form>
          </div>
        <?php else: ?>
          <div class="book-cover-lg <?= $bcClass ?>">
            <?php if ($book['book_condition'] === 'new'): ?>
              <span class="badge badge-new">Baru</span>
            <?php endif; ?>
            <?= e($book['name']) ?>
            <form method="POST" action="index.php?action=toggle_wishlist" style="position: absolute; top: 12px; right: 12px; z-index: 10; margin: 0;" onclick="event.stopPropagation()">
              <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
              <button type="submit" title="Wishlist" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); border: none; display: flex; align-items: center; justify-content: center; font-size: 15px; color: <?= $inWishlist ? '#f43f5e' : '#a1a1aa' ?>; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"><?= $inWishlist ? '♥' : '♡' ?></button>
            </form>
          </div>
        <?php endif; ?>
        <div class="book-body">
          <div class="book-genre"><?= e($book['category']) ?></div>
          <div class="book-title"><?= e($book['name']) ?></div>
          <div class="book-author">Stok: <?= (int)$book['stock'] ?></div>
          <div class="book-rating"><span class="stars"><?= $stars ?></span><span class="rating-count"><?= number_format($rating, 1) ?></span></div>
          <div class="book-footer">
            <div class="book-price"><?= rupiah($book['price']) ?></div>
            <button type="button" class="btn-add" onclick="event.stopPropagation(); event.preventDefault(); addToCart(event, <?= $book['id'] ?>, '<?= e(addslashes($book['name'])) ?>')">+</button>
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
        <?php
          $featAvg = round((float)($featured['avg_rating'] ?? 0), 1);
          $featStars = $featAvg >= 4.5 ? '★★★★★' : ($featAvg >= 3.5 ? '★★★★☆' : ($featAvg >= 2.5 ? '★★★☆☆' : ($featAvg >= 1.5 ? '★★☆☆☆' : '☆☆☆☆☆')));
        ?>
        <div class="featured-rating">
          <span class="stars"><?= $featStars ?></span>
          <span><?= $featAvg > 0 ? number_format($featAvg, 1) : 'Belum ada rating' ?></span>
          <span>·</span>
          <span>🛒 Terlaris</span>
        </div>
        <div class="big-price"><?= rupiah($featured['price']) ?></div>
        <div class="price-actions">
          <button class="btn-primary" onclick="event.preventDefault(); addToCart(event, <?= $featured['id'] ?>, '<?= e(addslashes($featured['name'])) ?>')">🛒 Tambah ke Keranjang</button>
          <form method="POST" action="index.php?action=toggle_wishlist" style="display:inline;" onclick="event.stopPropagation()">
            <?php $featWish = is_in_wishlist($GLOBALS['pdo'], $featured['id']); ?>
            <input type="hidden" name="product_id" value="<?= $featured['id'] ?>">
            <button type="submit" class="btn-secondary" <?= $featWish ? 'style="color:var(--rose-deep); border-color:var(--rose-blush); background:var(--rose-blush);"' : '' ?>><?= $featWish ? '♥' : '♡' ?> Wishlist</button>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </section>

  <!-- CARA KERJA & SELLER CTA -->
  <section class="section hiw-section" id="section-cara-kerja">
    
    <!-- Bagian 1: Cara Kerja Pembeli -->
    <div class="hiw-container">
      <h2 class="hiw-title">Cara Kerja</h2>
      <p class="hiw-subtitle">Nikmati kemudahan berbelanja buku favorit Anda hanya dalam tiga langkah mudah.</p>
      
      <div class="hiw-grid">
        <!-- Step 1 -->
        <div class="hiw-card">
          <div class="hiw-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
          <h3 class="hiw-card-title">Cari Buku Favorit</h3>
          <p class="hiw-card-desc">Jelajahi ribuan koleksi buku dari berbagai genre yang sesuai dengan minat Anda.</p>
        </div>
        <!-- Step 2 -->
        <div class="hiw-card">
          <div class="hiw-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          </div>
          <h3 class="hiw-card-title">Lakukan Pembayaran</h3>
          <p class="hiw-card-desc">Proses transaksi aman dengan berbagai pilihan metode pembayaran yang praktis.</p>
        </div>
        <!-- Step 3 -->
        <div class="hiw-card">
          <div class="hiw-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          </div>
          <h3 class="hiw-card-title">Buku Sampai di Rumah</h3>
          <p class="hiw-card-desc">Pengiriman cepat dan terlacak langsung ke alamat Anda dengan aman.</p>
        </div>
      </div>
    </div>

    <!-- Divider -->
    <div class="hiw-divider"></div>

    <!-- Bagian 2: Mulai Berjualan (Penjual) -->
    <div class="hiw-seller-container">
      <h2 class="hiw-title-sm">Mulai Berjualan di Rubby Books</h2>
      <p class="hiw-subtitle">Bergabunglah dengan ribuan penjual lainnya dan mulai kembangkan bisnis buku Anda bersama kami.</p>
      
      <div class="hiw-seller-grid">
        <!-- Sell Step 1 -->
        <div>
          <div class="hiw-seller-step-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Daftar Akun
          </div>
          <p class="hiw-seller-step-desc">Buat akun penjual Anda dalam hitungan menit.</p>
        </div>
        <!-- Sell Step 2 -->
        <div>
          <div class="hiw-seller-step-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="12" y2="12"/><line x1="15" y1="15" x2="12" y2="12"/></svg>
            Upload Produk
          </div>
          <p class="hiw-seller-step-desc">Unggah katalog buku terbaik Anda dengan mudah.</p>
        </div>
        <!-- Sell Step 3 -->
        <div>
          <div class="hiw-seller-step-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Mulai Berjualan
          </div>
          <p class="hiw-seller-step-desc">Terima pesanan pertama Anda dan raih keuntungan.</p>
        </div>
      </div>

      <button class="hiw-btn-seller" onclick="openAuth('seller')">
        Daftar Jadi Penjual
      </button>
    </div>

  </section>

  <!-- TESTIMONIALS -->
  <section class="section" id="section-testimoni">
    <div class="sec-head">
      <h2 class="sec-title">💬 Apa Kata <span>Pembeli Kami</span></h2>
    </div>
    <div class="testi-grid">
      <?php if (!empty($testimonials)): ?>
        <?php foreach ($testimonials as $t):
          $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $t['buyer_name']), 0, 2))));
          $starsCount = (int)$t['rating'];
          $starStr = str_repeat('★', $starsCount) . str_repeat('☆', 5 - $starsCount);
          // random gradient per person
          $grads = ['#7c3aed,#a855f7','#1e40af,#1d4ed8','#065f46,#047857','#b91c1c,#dc2626','#b45309,#d97706'];
          $grad = $grads[crc32($t['buyer_name']) % count($grads)];
        ?>
        <div class="testi-card">
          <p class="testi-body">"<?= e($t['comment']) ?>"</p>
          <div class="testi-user">
            <div class="avatar" style="background:linear-gradient(135deg,<?= $grad ?>)"><?= $initials ?></div>
            <div>
              <div class="testi-name"><?= e($t['buyer_name']) ?></div>
              <div class="testi-loc">📍 Pembeli · <?= $starStr ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback testimonials when no reviews yet -->
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
      <?php endif; ?>
    </div>
  </section>
</div>
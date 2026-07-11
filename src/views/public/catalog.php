<?php
$f = $filters ?? [];
$qStr = $f['q'] ?? '';
$selectedCats = $f['category'] ?? [];
$priceMax = $f['price_max'] ?? 300000;
$selectedRatings = $f['rating'] ?? [];
$selectedConditions = $f['condition'] ?? [];
$sort = $f['sort'] ?? 'Terlaris';
$currentPage = $f['page'] ?? 1;
$totalPages = $f['totalPages'] ?? 1;
$totalItems = $f['totalItems'] ?? 0;
$ratingOptions = $ratingOptions ?? [];
$conditionOptions = $conditionOptions ?? [];
?>
<div id="page-catalog" class="page active">
  <div class="catalog-layout">
    <form id="catalog-form" action="index.php" method="GET" class="catalog-sidebar">
      <input type="hidden" name="page" value="catalog">
      <input type="hidden" name="p" id="page-input" value="<?= $currentPage ?>">
      <?php if ($qStr): ?>
        <input type="hidden" name="q" value="<?= e($qStr) ?>">
      <?php endif; ?>

      <div class="filter-group">
        <div class="filter-title">Kategori</div>
        <label class="filter-item">
          <input type="checkbox" id="cat-all" <?= empty($selectedCats) ? 'checked' : '' ?>>
          <span>Semua Kategori</span>
        </label>
        <?php foreach ($categories as $c): ?>
          <label class="filter-item">
            <input type="checkbox" name="category[]" class="cat-checkbox" value="<?= $c['id'] ?>" <?= in_array($c['id'], $selectedCats) ? 'checked' : '' ?>>
            <span><?= e($c['name']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
      
      <div class="filter-group">
        <div class="filter-title">Rentang Harga</div>
        <div class="price-range">
          <input type="range" name="price_max" class="range-input" min="0" max="300000" step="10000" value="<?= $priceMax ?>">
          <div class="range-vals"><span>Rp 0</span><span id="price-max">Rp <?= number_format($priceMax, 0, ',', '.') ?></span></div>
        </div>
      </div>
      
      <div class="filter-group">
        <div class="filter-title">Rating</div>
        <?php foreach ($ratingOptions as $opt): ?>
          <label class="filter-item">
            <input type="checkbox" name="rating[]" value="<?= e($opt['value']) ?>" <?= in_array((float)$opt['value'], $selectedRatings, true) ? 'checked' : '' ?>>
            <span><?= e($opt['stars']) ?> (<?= e($opt['label']) ?>)</span>
            <small><?= (int)$opt['count'] ?></small>
          </label>
        <?php endforeach; ?>
      </div>
      
      <div class="filter-group">
        <div class="filter-title">Kondisi Buku</div>
        <?php foreach ($conditionOptions as $value => $opt): ?>
          <label class="filter-item">
            <input type="checkbox" name="condition[]" value="<?= e($value) ?>" <?= in_array($value, $selectedConditions, true) ? 'checked' : '' ?>>
            <span><?= e($opt['label']) ?></span>
            <small><?= (int)$opt['count'] ?></small>
          </label>
        <?php endforeach; ?>
      </div>
      
      <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px">Terapkan Filter</button>
    </form>
    
    <div class="catalog-main">
      <div class="catalog-toolbar">
        <div class="catalog-results">Menampilkan <b><?= count($products) ?> buku</b> dari <?= $totalItems ?> judul<?= $qStr ? ' untuk pencarian "'.e($qStr).'"' : '' ?></div>
        <select name="sort" class="sort-select" form="catalog-form">
          <option value="Terlaris" <?= $sort === 'Terlaris' ? 'selected' : '' ?>>Terlaris</option>
          <option value="Terbaru" <?= $sort === 'Terbaru' ? 'selected' : '' ?>>Terbaru</option>
          <option value="Harga Terendah" <?= $sort === 'Harga Terendah' ? 'selected' : '' ?>>Harga Terendah</option>
          <option value="Harga Tertinggi" <?= $sort === 'Harga Tertinggi' ? 'selected' : '' ?>>Harga Tertinggi</option>
          <option value="Rating Tertinggi" <?= $sort === 'Rating Tertinggi' ? 'selected' : '' ?>>Rating Tertinggi</option>
        </select>
      </div>
      
      <div class="book-grid">
        <?php if (empty($products)): ?>
          <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #666;">
            Tidak ada buku yang sesuai dengan filter Anda.
          </div>
        <?php else: ?>
          <?php foreach ($products as $book): 
            $rating = (float) $book['avg_rating'];
            $stars = $rating >= 4.5 ? '★★★★★' : ($rating >= 3.5 ? '★★★★☆' : ($rating >= 2.5 ? '★★★☆☆' : '☆☆☆☆☆'));
            $isNew = $book['book_condition'] === 'new';
            // Placeholder background color classes from bc1 to bc6 based on ID
            $bcClass = 'bc' . (($book['id'] % 6) + 1);
          ?>
            <div class="book-card" onclick="window.location='index.php?page=product&id=<?= $book['id'] ?>'" style="cursor:pointer">
              <?php $inWishlist = is_in_wishlist($GLOBALS['pdo'], $book['id']); ?>
              <form method="POST" action="index.php?action=toggle_wishlist" class="wish-form card-wish-form" onclick="event.stopPropagation()">
                <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
                <button type="submit" title="Wishlist" class="wish-btn <?= $inWishlist ? 'active' : '' ?>"><?= $inWishlist ? '&hearts;' : '&#9825;' ?></button>
              </form>
              <?php if (!empty($book['image'])): ?>
                <div class="book-cover-lg" style="background-image: url('<?= e(asset($book['image'])) ?>'); background-size: cover; background-position: center; font-size:0;">
                  <?php if ($isNew): ?><span class="badge badge-new">Baru</span><?php endif; ?>
                </div>
              <?php else: ?>
                <div class="book-cover-lg <?= $bcClass ?>">
                  <?php if ($isNew): ?>
                    <span class="badge badge-new">Baru</span>
                  <?php endif; ?>
                  <?= e($book['name']) ?>
                </div>
              <?php endif; ?>
              <div class="book-body">
                <div class="book-genre"><?= e($book['category']) ?></div>
                <div class="book-title"><?= e($book['name']) ?></div>
                <div class="book-seller">Penjual: <?= e($book['seller_name'] ?? 'RubbyBooks') ?></div>
                <div class="book-author">Stok: <?= (int)$book['stock'] ?></div>
                <div class="book-rating">
                  <span class="stars"><?= $stars ?></span>
                  <span class="rating-count"><?= number_format($rating, 1) ?></span>
                </div>
                <div class="book-footer">
                  <div class="book-price"><?= rupiah($book['price']) ?></div>
                  <button type="button" class="btn-add" onclick="event.stopPropagation(); event.preventDefault(); addToCart(event, <?= $book['id'] ?>, '<?= e(addslashes($book['name'])) ?>')">+</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      
      <?php if ($totalPages > 1): ?>
      <div class="page-pagination">
        <?php if ($currentPage > 1): ?>
          <button type="button" aria-label="Sebelumnya" onclick="changePage(<?= $currentPage - 1 ?>)">‹</button>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
            <button type="button" class="<?= $i === $currentPage ? 'active' : '' ?>" onclick="changePage(<?= $i ?>)"><?= $i ?></button>
          <?php elseif (abs($i - $currentPage) == 3): ?>
            <span class="page-dots">…</span>
          <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
          <button type="button" aria-label="Berikutnya" onclick="changePage(<?= $currentPage + 1 ?>)">›</button>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

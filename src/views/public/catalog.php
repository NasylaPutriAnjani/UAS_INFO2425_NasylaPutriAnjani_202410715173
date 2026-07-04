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
?>
<div id="page-catalog" class="page active">
  <form id="catalog-form" action="index.php" method="GET" class="catalog-layout">
    <input type="hidden" name="page" value="catalog">
    <?php if ($qStr): ?>
      <input type="hidden" name="q" value="<?= e($qStr) ?>">
    <?php endif; ?>
    
    <aside class="catalog-sidebar">
      <div class="filter-group">
        <div class="filter-title">Kategori</div>
        <div class="filter-item">
          <input type="checkbox" id="cat-all" <?= empty($selectedCats) ? 'checked' : '' ?> onclick="if(this.checked) { document.querySelectorAll('.cat-checkbox').forEach(cb => cb.checked = false); this.form.submit(); }"> 
          <label for="cat-all">Semua Kategori</label>
        </div>
        <?php foreach ($categories as $c): ?>
          <div class="filter-item">
            <input type="checkbox" name="category[]" class="cat-checkbox" value="<?= $c['id'] ?>" <?= in_array($c['id'], $selectedCats) ? 'checked' : '' ?> onchange="document.getElementById('cat-all').checked = false; this.form.submit();"> 
            <?= e($c['name']) ?>
          </div>
        <?php endforeach; ?>
      </div>
      
      <div class="filter-group">
        <div class="filter-title">Rentang Harga</div>
        <div class="price-range">
          <input type="range" name="price_max" class="range-input" min="0" max="300000" step="10000" value="<?= $priceMax ?>" oninput="updatePrice(this)" onchange="this.form.submit()">
          <div class="range-vals"><span>Rp 0</span><span id="price-max">Rp <?= number_format($priceMax, 0, ',', '.') ?></span></div>
        </div>
      </div>
      
      <div class="filter-group">
        <div class="filter-title">Rating</div>
        <div class="filter-item"><input type="checkbox" name="rating[]" value="5.0" <?= in_array('5.0', $selectedRatings) ? 'checked' : '' ?> onchange="this.form.submit()"> ★★★★★ (5.0)</div>
        <div class="filter-item"><input type="checkbox" name="rating[]" value="4.0" <?= in_array('4.0', $selectedRatings) ? 'checked' : '' ?> onchange="this.form.submit()"> ★★★★☆ (4.0+)</div>
        <div class="filter-item"><input type="checkbox" name="rating[]" value="3.0" <?= in_array('3.0', $selectedRatings) ? 'checked' : '' ?> onchange="this.form.submit()"> ★★★☆☆ (3.0+)</div>
      </div>
      
      <div class="filter-group">
        <div class="filter-title">Kondisi Buku</div>
        <div class="filter-item"><input type="checkbox" name="condition[]" value="new" <?= empty($selectedConditions) || in_array('new', $selectedConditions) ? 'checked' : '' ?> onchange="this.form.submit()"> Baru</div>
        <div class="filter-item"><input type="checkbox" name="condition[]" value="used_good" <?= in_array('used_good', $selectedConditions) ? 'checked' : '' ?> onchange="this.form.submit()"> Bekas - Baik</div>
        <div class="filter-item"><input type="checkbox" name="condition[]" value="used_fair" <?= in_array('used_fair', $selectedConditions) ? 'checked' : '' ?> onchange="this.form.submit()"> Bekas - Cukup</div>
      </div>
      
      <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px">Terapkan Filter</button>
    </aside>
    
    <div class="catalog-main">
      <div class="catalog-toolbar">
        <div class="catalog-results">Menampilkan <b><?= count($products) ?> buku</b> dari <?= $totalItems ?> judul<?= $qStr ? ' untuk pencarian "'.e($qStr).'"' : '' ?></div>
        <select name="sort" class="sort-select" onchange="this.form.submit()">
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
              <?php if (!empty($book['image'])): ?>
                <div class="book-cover-lg" style="background-image: url('<?= e(asset($book['image'])) ?>'); background-size: cover; background-position: center; font-size:0;">
                  <?php if ($isNew): ?><span class="badge badge-new">Baru</span><?php endif; ?>
                  <form method="POST" action="index.php?action=toggle_wishlist" style="position: absolute; top: 12px; right: 12px; z-index: 10; margin: 0;" onclick="event.stopPropagation()">
                    <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
                    <button type="submit" title="Tambah ke wishlist" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); border: none; display: flex; align-items: center; justify-content: center; font-size: 15px; color: #f43f5e; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">♡</button>
                  </form>
                </div>
              <?php else: ?>
                <div class="book-cover-lg <?= $bcClass ?>">
                  <?php if ($isNew): ?>
                    <span class="badge badge-new">Baru</span>
                  <?php endif; ?>
                  <?= e($book['name']) ?>
                  <form method="POST" action="index.php?action=toggle_wishlist" style="position: absolute; top: 12px; right: 12px; z-index: 10; margin: 0;" onclick="event.stopPropagation()">
                    <input type="hidden" name="product_id" value="<?= $book['id'] ?>">
                    <button type="submit" title="Tambah ke wishlist" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); border: none; display: flex; align-items: center; justify-content: center; font-size: 15px; color: #f43f5e; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">♡</button>
                  </form>
                </div>
              <?php endif; ?>
              <div class="book-body">
                <div class="book-genre"><?= e($book['category']) ?></div>
                <div class="book-title"><?= e($book['name']) ?></div>
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
        <input type="hidden" name="p" id="page-input" value="<?= $currentPage ?>">
      </div>
      <?php endif; ?>
    </div>
  </form>
</div>
<script>
function changePage(page) {
    document.getElementById('page-input').value = page;
    document.getElementById('catalog-form').submit();
}
</script>
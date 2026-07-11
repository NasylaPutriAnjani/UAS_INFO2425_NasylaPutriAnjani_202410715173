<?php
$avg       = round((float)($product['avg_rating'] ?? 0), 1);
$total     = (int)($product['review_count'] ?? 0);
$sold      = (int)($product['sold_count'] ?? 0);
$stock     = (int)$product['stock'];
$bcClass   = 'bc' . (($product['id'] % 6) + 1);
$condLabel = ['new' => 'Baru', 'used_good' => 'Bekas - Baik', 'used_fair' => 'Bekas - Cukup'][$product['book_condition']] ?? $product['book_condition'];

$starsHtml = function(float $n): string {
    $n = round($n);
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
};
$sellerUser = ['name' => $product['seller_name'], 'avatar' => $product['seller_avatar'] ?? null];
$sellerInit = user_initials($sellerUser);
$inWishlist = is_in_wishlist($GLOBALS['pdo'], (int)$product['id']);
?>
<div id="page-product" class="page active">
  <div class="product-page-wrap">

    <!-- Breadcrumb -->
    <nav class="prod-breadcrumb">
      <a href="index.php?page=catalog" class="prod-bc-link">Katalog</a>
      <span class="prod-bc-sep">›</span>
      <a href="index.php?page=catalog&category=<?= $product['category_id'] ?>" class="prod-bc-link"><?= e($product['category']) ?></a>
      <span class="prod-bc-sep">›</span>
      <span class="prod-bc-cur"><?= e($product['name']) ?></span>
    </nav>

    <!-- Main Product Section -->
    <div class="prod-main">

      <!-- Left: Cover + Seller -->
      <div class="prod-left">
        <?php if (!empty($product['image'])): ?>
          <div class="prod-cover" style="background-image:url('<?= e(asset($product['image'])) ?>');background-size:cover;background-position:center;font-size:0;">
        <?php else: ?>
          <div class="prod-cover <?= $bcClass ?>">
            <?= e($product['name']) ?>
        <?php endif; ?>
            <?php if ($product['book_condition'] === 'new'): ?>
              <span class="prod-badge">Baru</span>
            <?php endif; ?>
            <form method="POST" action="index.php?action=toggle_wishlist" class="prod-wish-form">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" class="prod-wish-btn <?= $inWishlist ? 'active' : '' ?>" title="Wishlist">
                <?= $inWishlist ? '❤️' : '♡' ?>
              </button>
            </form>
          </div>

        <!-- Seller card -->
        <div class="prod-seller-card">
          <?= user_avatar_html($sellerUser, 'prod-seller-av', $sellerInit) ?>
          <div>
            <div class="prod-seller-name">🏪 <?= e($product['seller_name']) ?></div>
            <div class="prod-seller-sub">Penjual Terverifikasi ✓</div>
          </div>
        </div>
      </div>

      <!-- Right: Info -->
      <div class="prod-right">
        <div class="prod-category-lbl"><?= e($product['category']) ?></div>
        <h1 class="prod-title"><?= e($product['name']) ?></h1>

        <div class="prod-rating-row">
          <?php if ($avg > 0): ?>
            <span class="prod-stars"><?= $starsHtml($avg) ?></span>
            <span class="prod-rating-val"><?= number_format($avg, 1) ?></span>
            <span class="prod-rating-count">(<?= $total ?> ulasan)</span>
            <span class="prod-dot">·</span>
          <?php endif; ?>
          <span class="prod-sold"><?= $sold ?>+ terjual</span>
        </div>

        <!-- Price box -->
        <div class="prod-price-box">
          <div class="prod-price"><?= rupiah($product['price']) ?></div>
          <div class="prod-price-note">Harga sudah termasuk PPN, belum termasuk ongkos kirim</div>
          <?php if ($stock <= 0): ?>
            <div class="prod-stock out">Stok habis</div>
          <?php elseif ($stock <= 5): ?>
            <div class="prod-stock low">Stok tersisa <?= $stock ?> buku — segera order!</div>
          <?php else: ?>
            <div class="prod-stock">Stok tersedia — <?= $stock ?> buku</div>
          <?php endif; ?>
        </div>

        <!-- Qty + Actions -->
        <div class="prod-buy-row">
          <div class="prod-qty-wrap">
            <span class="prod-qty-lbl">Jumlah</span>
            <div class="prod-qty-ctrl">
              <button type="button" id="qty-minus" onclick="prodQty(-1)">−</button>
              <input type="number" id="prod-qty" value="1" min="1" max="<?= $stock ?>" readonly>
              <button type="button" id="qty-plus" onclick="prodQty(1)">+</button>
            </div>
          </div>
          <div class="prod-actions">
            <button class="prod-btn-cart" onclick="prodAddCart(<?= $product['id'] ?>, '<?= e(addslashes($product['name'])) ?>')">+ Keranjang</button>
            <button class="prod-btn-buy" onclick="prodBuyNow(<?= $product['id'] ?>, '<?= e(addslashes($product['name'])) ?>')">🛒 Beli Sekarang</button>
          </div>
        </div>

        <!-- Meta grid -->
        <div class="prod-meta-grid">
          <div class="prod-meta-item">
            <span class="prod-meta-lbl">Kondisi</span>
            <span class="prod-meta-val"><?= $condLabel ?></span>
          </div>
          <div class="prod-meta-item">
            <span class="prod-meta-lbl">Kategori</span>
            <span class="prod-meta-val"><?= e($product['category']) ?></span>
          </div>
          <div class="prod-meta-item">
            <span class="prod-meta-lbl">Stok</span>
            <span class="prod-meta-val"><?= $stock ?> buku</span>
          </div>
          <div class="prod-meta-item">
            <span class="prod-meta-lbl">Terjual</span>
            <span class="prod-meta-val"><?= $sold ?>+ eksemplar</span>
          </div>
        </div>
      </div>
    </div><!-- /prod-main -->

    <!-- Tabs -->
    <div class="prod-tabs-wrap">
      <div class="prod-tab-nav" role="tablist">
        <button class="prod-tab-btn active" onclick="prodTab('desc',this)">Deskripsi</button>
        <button class="prod-tab-btn" onclick="prodTab('spec',this)">Spesifikasi</button>
        <button class="prod-tab-btn" onclick="prodTab('rev',this)">Ulasan (<?= $total ?>)</button>
      </div>

      <!-- Deskripsi -->
      <div id="prod-pane-desc" class="prod-tab-pane active">
        <div class="prod-desc"><?= nl2br(e($product['description'] ?? 'Tidak ada deskripsi.')) ?></div>
      </div>

      <!-- Spesifikasi -->
      <div id="prod-pane-spec" class="prod-tab-pane">
        <div class="prod-spec-grid">
          <div class="prod-spec-item"><span class="prod-spec-lbl">Judul</span><span class="prod-spec-val"><?= e($product['name']) ?></span></div>
          <div class="prod-spec-item"><span class="prod-spec-lbl">Penjual</span><span class="prod-spec-val"><?= e($product['seller_name']) ?></span></div>
          <div class="prod-spec-item"><span class="prod-spec-lbl">Kategori</span><span class="prod-spec-val"><?= e($product['category']) ?></span></div>
          <div class="prod-spec-item"><span class="prod-spec-lbl">Kondisi</span><span class="prod-spec-val"><?= $condLabel ?></span></div>
          <div class="prod-spec-item"><span class="prod-spec-lbl">Harga</span><span class="prod-spec-val"><?= rupiah($product['price']) ?></span></div>
          <div class="prod-spec-item"><span class="prod-spec-lbl">Stok</span><span class="prod-spec-val"><?= $stock ?> buku</span></div>
        </div>
      </div>

      <!-- Ulasan -->
      <div id="prod-pane-rev" class="prod-tab-pane">
        <?php if ($total > 0): ?>
          <div class="prod-rev-header">
            <div class="prod-rev-score-col">
              <div class="prod-rev-big"><?= number_format($avg, 1) ?></div>
              <div class="prod-rev-stars-big"><?= $starsHtml($avg) ?></div>
              <div class="prod-rev-total">Dari <?= $total ?> ulasan</div>
            </div>
            <div class="prod-breakdown">
              <?php foreach ([5,4,3,2,1] as $s):
                $cnt = $breakdown[$s] ?? 0;
                $pct = $total > 0 ? round($cnt / $total * 100) : 0;
              ?>
              <div class="prod-bar-row">
                <span class="prod-bar-lbl"><?= $s ?> ★</span>
                <div class="prod-bar-track"><div class="prod-bar-fill" style="width:<?= $pct ?>%"></div></div>
                <span class="prod-bar-pct"><?= $pct ?>%</span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="prod-rev-list">
            <?php foreach ($reviews as $r):
              $rInit = strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', array_slice(explode(' ', $r['buyer_name']), 0, 2))));
              $rGrads = ['#7c3aed,#a855f7','#1e40af,#1d4ed8','#065f46,#047857','#b91c1c,#dc2626','#b45309,#d97706'];
              $rGrad = $rGrads[abs(crc32($r['buyer_name'])) % count($rGrads)];
              $diff = time() - strtotime($r['created_at'] ?? 'now');
              $timeAgo = $diff < 86400 ? floor($diff/3600).' jam lalu' : ($diff < 604800 ? floor($diff/86400).' hari lalu' : floor($diff/604800).' minggu lalu');
            ?>
            <div class="prod-rev-item">
              <div class="prod-rev-top">
                <div class="prod-rev-av" style="background:linear-gradient(135deg,<?= $rGrad ?>)"><?= $rInit ?></div>
                <div>
                  <div class="prod-rev-name"><?= e($r['buyer_name']) ?></div>
                  <div class="prod-rev-stars"><?= $starsHtml((float)$r['rating']) ?></div>
                </div>
                <div class="prod-rev-date"><?= $timeAgo ?></div>
              </div>
              <div class="prod-rev-comment"><?= nl2br(e($r['comment'] ?? '')) ?></div>
              <?php if (!empty($r['seller_reply'])): ?>
                <div class="prod-seller-reply">
                  <span class="prod-reply-badge">🏪 Balasan Penjual</span>
                  <?= nl2br(e($r['seller_reply'])) ?>
                </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="prod-no-rev">📭 Belum ada ulasan untuk buku ini.</div>
        <?php endif; ?>
      </div>
    </div><!-- /tabs -->

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
    <div class="prod-related">
      <h2 class="prod-related-title">📚 Buku Lainnya di Kategori Ini</h2>
      <div class="book-grid">
        <?php foreach ($related as $rb):
          $rbBc = 'bc' . (($rb['id'] % 6) + 1);
        ?>
        <div class="book-card" data-product-id="<?= $rb['id'] ?>" onclick="window.location='index.php?page=product&id=<?= $rb['id'] ?>'">
          <?php if (!empty($rb['image'])): ?>
            <div class="book-cover-lg" style="background-image:url('<?= e(asset($rb['image'])) ?>');background-size:cover;background-position:center;font-size:0;"></div>
          <?php else: ?>
            <div class="book-cover-lg <?= $rbBc ?>"><?= e($rb['name']) ?></div>
          <?php endif; ?>
          <div class="book-body">
            <div class="book-genre"><?= e($rb['category']) ?></div>
            <div class="book-title"><?= e($rb['name']) ?></div>
            <div class="book-seller">Penjual: <?= e($rb['seller_name'] ?? 'RubbyBooks') ?></div>
            <div class="book-author">Stok: <?= (int)$rb['stock'] ?></div>
            <div class="book-footer">
              <div class="book-price"><?= rupiah($rb['price']) ?></div>
              <button type="button" class="btn-add" onclick="event.stopPropagation(); event.preventDefault(); addToCart(event, <?= $rb['id'] ?>, '<?= e(addslashes($rb['name'])) ?>')">+</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /product-page-wrap -->
</div>

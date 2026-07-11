<?php
// Fallbacks for IDE static analysis
$reviews = $reviews ?? [];
$filters = $filters ?? ['rating' => null];
$stats = $stats ?? ['average' => 0.0, 'total' => 0, 'breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]];
?>
<div id="page-seller-reviews" class="page active">
  <div class="dash-layout">

    <!-- SIDEBAR -->
    <?php
    $currentSellerPage = $_GET['page'] ?? 'seller';
    $sellerIdForSidebar = current_user()['id'];
    $activeProductsCount = (int)$GLOBALS['pdo']->query("SELECT COUNT(*) FROM products WHERE seller_id = $sellerIdForSidebar AND status = 'active'")->fetchColumn();
    $sellerNavCounts = user_nav_counts($GLOBALS['pdo']);
    $sellerOrderBadgeCount = (int)($sellerNavCounts['orders'] ?? 0);
    $sellerUnreadNotifCount = (int)($sellerNavCounts['notifications'] ?? 0);
    ?>
    <aside class="dash-sidebar seller-sidebar">
      <div class="sidebar-store-profile">
        <?= user_avatar_html(current_user(), 'sidebar-store-avatar', 'S') ?>
        <div>
          <div class="sidebar-store-name"><?= e(current_user()['name'] ?? 'Penjual') ?></div>
          <div class="sidebar-store-status">Toko Aktif</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="sidebar-group">
          <div class="sidebar-group-label">Menu Utama</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller' ? ' active' : '' ?>" onclick="showPage('seller')">
            <span class="si">📊</span> Dashboard
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_products' ? ' active' : '' ?>" onclick="showPage('seller_products')">
            <span class="si">📦</span> Produk Saya
            <span class="sidebar-badge"><?= $activeProductsCount ?></span>
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_orders' ? ' active' : '' ?>" onclick="showPage('seller_orders')">
            <span class="si">🛒</span> Pesanan Masuk

            <?php if ($sellerOrderBadgeCount > 0): ?><span class="sidebar-badge"><?= $sellerOrderBadgeCount ?></span><?php endif; ?>
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_reviews' ? ' active' : '' ?>" onclick="showPage('seller_reviews')">
            <span class="si">💬</span> Ulasan & Rating
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_notifications' ? ' active' : '' ?>" onclick="showPage('seller_notifications')">
            <span class="si">🔔</span> Notifikasi

            <?php if ($sellerUnreadNotifCount > 0): ?><span class="sidebar-badge warn"><?= $sellerUnreadNotifCount ?></span><?php endif; ?>
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Keuangan</div>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_report' ? ' active' : '' ?>" onclick="showPage('seller_report')">
            <span class="si">💰</span> Laporan Penjualan
          </button>
        </div>
        <div class="sidebar-group">
          <div class="sidebar-group-label">Pengaturan</div>
          <button class="sidebar-item" onclick="showPage('account_settings')">
            <span class="si">⚙️</span> Pengaturan Akun
          </button>
        </div>
      </nav>

      <div class="sidebar-footer">
        <button class="sidebar-item" onclick="doLogout()" style="color:#dc2626;width:100%">
          <span class="si">🚪</span> Keluar
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="dash-content">
      <!-- Topbar -->
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>Ulasan & Rating</h2>
          <p>Lihat ulasan pembeli dan berikan balasan langsung untuk reputasi toko Anda</p>
        </div>
        <div class="dash-topbar-right">
          <form method="GET" action="index.php" class="review-filter-form">
            <input type="hidden" name="page" value="seller_reviews">
            <div class="filter-button-wrapper" style="min-width:160px;">
              <span class="filter-btn-icon">⭐</span>
              <select name="rating" onchange="this.form.submit()" class="filter-select-input">
                <option value="">Semua Rating</option>
                <?php for ($r = 5; $r >= 1; $r--): ?>
                  <option value="<?= $r ?>" <?= (isset($filters['rating']) && (int)$filters['rating'] === $r) ? 'selected' : '' ?>>
                    <?= $r ?> Bintang
                  </option>
                <?php endfor; ?>
              </select>
            </div>
          </form>
        </div>
      </div>

      <!-- Body -->
      <div class="dash-body">

        <!-- Stats Row -->
        <div class="stats-overview-grid">
          <!-- Average Rating Card -->
          <div class="stat-rating-card">
            <div class="big-rating-val"><?= number_format((float)$stats['average'], 1) ?></div>
            <div class="rating-stars-row">
              <?php
              $floorRating = floor($stats['average']);
              for ($i = 1; $i <= 5; $i++):
              ?>
                <span class="star-item <?= $i <= $floorRating ? 'filled' : '' ?>">★</span>
              <?php endfor; ?>
            </div>
            <div class="total-reviews-label">dari <?= (int)$stats['total'] ?> ulasan</div>
          </div>

          <!-- Breakdown Bars Card -->
          <div class="stat-breakdown-card">
            <?php
            for ($star = 5; $star >= 1; $star--):
              $count = $stats['breakdown'][$star] ?? 0;
              $percent = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
            ?>
              <div class="breakdown-row">
                <span class="star-num"><?= $star ?></span>
                <div class="breakdown-bar">
                  <div class="breakdown-bar-fill" style="width: <?= $percent ?>%;"></div>
                </div>
                <span class="breakdown-count"><?= $count ?></span>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Reviews List Container -->
        <div class="reviews-list-container">
          <?php if (empty($reviews)): ?>
            <div class="no-reviews-placeholder">
              <span>💬</span>
              <p>Belum ada ulasan masuk untuk produk Anda.</p>
            </div>
          <?php else: ?>
            <?php foreach ($reviews as $rev):
              $buyerUser = ['name' => $rev['buyer_name'], 'avatar' => $rev['buyer_avatar'] ?? null];
              $buyerInitial = user_initials($buyerUser);
              $revRating = (int)$rev['rating'];
            ?>
              <div class="review-item-card">
                <!-- User Header -->
                <div class="review-item-header">
                  <div class="buyer-avatar-info">
                    <?= user_avatar_html($buyerUser, 'buyer-letter-avatar', $buyerInitial) ?>
                    <div class="buyer-meta-text">
                      <span class="buyer-name-bold"><?= e($rev['buyer_name']) ?></span>
                      <span class="review-date-text"><?= date('d F Y', strtotime($rev['created_at'])) ?></span>
                    </div>
                  </div>
                  <div class="review-stars-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <span class="star-item-small <?= $i <= $revRating ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                  </div>
                </div>

                <!-- Product Name Tag -->
                <div class="review-product-tag-row">
                  <span class="product-tag-chip">
                    📖 <?= e($rev['product_name']) ?>
                  </span>
                </div>

                <!-- Comment Content -->
                <div class="review-comment-content">
                  <p><?= nl2br(e($rev['comment'])) ?></p>
                </div>

                <!-- Store Reply Section -->
                <?php if (!empty($rev['seller_reply'])): ?>
                  <div class="store-reply-display">
                    <span class="reply-label-tag">💬 Balasan Toko</span>
                    <p class="reply-text-content"><?= nl2br(e($rev['seller_reply'])) ?></p>
                  </div>
                <?php else: ?>
                  <!-- Reply Form -->
                  <div class="store-reply-input-area">
                    <form method="POST" action="index.php?page=seller_reviews" class="reply-post-form">
                      <input type="hidden" name="action" value="reply_review">
                      <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                      <textarea name="seller_reply" placeholder="Tulis balasan untuk ulasan ini..." required></textarea>
                      <div class="reply-form-footer">
                        <button type="submit" class="btn-reply-submit">Kirim Balasan</button>
                      </div>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>

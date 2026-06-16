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
    ?>
    <aside class="dash-sidebar seller-sidebar">
      <div class="sidebar-store-profile">
        <div class="sidebar-store-avatar">🏪</div>
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
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_reviews' ? ' active' : '' ?>" onclick="showPage('seller_reviews')">
            <span class="si">💬</span> Ulasan & Rating
          </button>
          <button class="sidebar-item<?= $currentSellerPage === 'seller_notifications' ? ' active' : '' ?>" onclick="showPage('seller_notifications')">
            <span class="si">🔔</span> Notifikasi
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
              $buyerInitial = strtoupper(substr($rev['buyer_name'], 0, 2));
              $revRating = (int)$rev['rating'];
            ?>
              <div class="review-item-card">
                <!-- User Header -->
                <div class="review-item-header">
                  <div class="buyer-avatar-info">
                    <div class="buyer-letter-avatar"><?= $buyerInitial ?></div>
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

<style>
  /* ── Filter Form ──────────────────── */
  .review-filter-form {
    display: flex;
    align-items: center;
  }

  .filter-button-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 0 12px;
    transition: all 0.2s;
  }

  .filter-button-wrapper:hover {
    border-color: var(--accent-light);
    background: var(--accent-blush);
  }

  .filter-btn-icon {
    margin-right: 6px;
    font-size: 14px;
  }

  .filter-select-input {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink-mid);
    padding: 10px 20px 10px 0;
    outline: none;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
  }

  .filter-button-wrapper::after {
    content: '▼';
    font-size: 8px;
    color: #94a3b8;
    position: absolute;
    right: 10px;
    pointer-events: none;
  }

  /* Stats Layout */
  .stats-overview-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 16px;
    margin-bottom: 24px;
  }

  .stat-rating-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
  }

  .big-rating-val {
    font-family: var(--font-serif);
    font-size: 56px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1;
  }

  .rating-stars-row {
    margin: 12px 0 8px;
    display: flex;
    gap: 2px;
  }

  .star-item {
    font-size: 20px;
    color: #e2e8f0;
  }

  .star-item.filled {
    color: #fbbf24;
  }

  .total-reviews-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
  }

  .stat-breakdown-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
  }

  .breakdown-row {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .star-num {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink-mid);
    width: 12px;
    text-align: center;
  }

  .breakdown-bar {
    flex: 1;
    height: 8px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
  }

  .breakdown-bar-fill {
    height: 100%;
    background: #fbbf24;
    border-radius: 4px;
  }

  .breakdown-count {
    font-size: 12.5px;
    color: #64748b;
    font-weight: 600;
    width: 24px;
    text-align: right;
  }

  /* Reviews List Styling */
  .reviews-list-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .no-reviews-placeholder {
    background: #fff;
    border: 1.5px dashed var(--border);
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    color: #64748b;
  }

  .no-reviews-placeholder span {
    font-size: 44px;
    display: block;
    margin-bottom: 12px;
  }

  .review-item-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .review-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }

  .buyer-avatar-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .buyer-letter-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 800;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .buyer-meta-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .buyer-name-bold {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--ink);
  }

  .review-date-text {
    font-size: 11px;
    color: #94a3b8;
  }

  .review-stars-display {
    display: flex;
    gap: 2px;
  }

  .star-item-small {
    font-size: 14px;
    color: #e2e8f0;
  }

  .star-item-small.filled {
    color: #fbbf24;
  }

  .review-product-tag-row {
    display: flex;
  }

  .product-tag-chip {
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
  }

  .review-comment-content p {
    font-size: 13.5px;
    color: var(--ink-mid);
    line-height: 1.6;
  }

  /* Store Reply Section */
  .store-reply-display {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    border-left: 4px solid var(--accent);
  }

  .reply-label-tag {
    font-size: 11px;
    font-weight: 800;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 6px;
  }

  .reply-text-content {
    font-size: 13px;
    color: var(--ink-mid);
    line-height: 1.5;
  }

  /* Reply Input Form */
  .store-reply-input-area {
    margin-top: 4px;
  }

  .reply-post-form textarea {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 13px;
    color: var(--ink);
    outline: none;
    min-height: 80px;
    resize: vertical;
    background: #f8fafc;
    font-family: var(--font-body);
    transition: all 0.2s;
  }

  .reply-post-form textarea:focus {
    border-color: var(--accent-light);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.08);
  }

  .reply-form-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
  }

  .btn-reply-submit {
    background: linear-gradient(135deg, var(--accent), var(--accent-deep));
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-reply-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.2);
  }
</style>
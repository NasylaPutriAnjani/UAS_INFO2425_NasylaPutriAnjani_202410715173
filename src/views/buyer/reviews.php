<?php $buyerMenu = 'reviews'; ?>
<div id="page-buyer-reviews" class="page active">
  <div class="dash-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="dash-content">
      <div class="dash-topbar">
        <div class="dash-topbar-left">
          <h2>⭐ Review Saya</h2>
          <p>Ulasan yang pernah kamu berikan untuk buku</p>
        </div>
      </div>
      <div class="dash-body">
        <div class="buyer-panel">
          <?php if (empty($reviews)): ?>
            <div class="buyer-empty">
              <div class="buyer-empty-icon">⭐</div>
              <p>Belum ada review. Beri rating setelah pesanan selesai diterima.</p>
            </div>
          <?php else: ?>
            <div class="buyer-review-list">
              <?php foreach ($reviews as $review): ?>
                <div class="buyer-review-card">
                  <div class="buyer-review-top">
                    <strong><?= e($review['product_name']) ?></strong>
                    <span class="buyer-review-stars"><?= str_repeat('★', (int) $review['rating']) ?><?= str_repeat('☆', 5 - (int) $review['rating']) ?></span>
                  </div>
                  <?php if (!empty($review['comment'])): ?>
                    <p class="buyer-review-text"><?= e($review['comment']) ?></p>
                  <?php endif; ?>
                  <div class="buyer-review-date"><?= e(date('d M Y', strtotime($review['created_at']))) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

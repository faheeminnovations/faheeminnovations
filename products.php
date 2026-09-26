<?php
$pageTitle = 'Our Products - Digital Solutions by Faheem Innovations';
$pageDesc  = 'Explore our range of digital products including ERP systems, SaaS platforms and custom software solutions built for businesses.';
require_once 'includes/header.php';
$products   = getProducts();
$categories = [];
foreach ($products as $p) {
    if ($p['category'] && !in_array($p['category'], $categories)) $categories[] = $p['category'];
}
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>Products</span></div>
    <h1>Digital Products Built for Real Business Needs.</h1>
    <p>Ready-made and customizable software solutions — from ERP systems to SaaS platforms — designed to help businesses run smarter.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($products): ?>

      <?php foreach ($categories as $cat): ?>
      <?php $catProducts = array_filter($products, fn($p) => $p['category'] === $cat); ?>
      <div style="margin-bottom:60px">
        <h2 style="margin-bottom:32px;padding-bottom:12px;border-bottom:2px solid var(--border)"><?= e($cat) ?></h2>
        <div class="products-grid">
          <?php foreach ($catProducts as $product):
            $features  = json_decode($product['features']  ?? '[]', true) ?: [];
            $techStack = json_decode($product['tech_stack'] ?? '[]', true) ?: [];
          ?>
          <div class="product-card <?= $product['is_featured'] ? 'product-featured' : '' ?>">
            <?php if ($product['is_featured']): ?>
            <span class="product-badge-featured"><i class="fas fa-star"></i> Featured</span>
            <?php endif; ?>

            <div class="product-card-header">
              <div class="product-icon"><i class="<?= e($product['icon']) ?>"></i></div>
              <div>
                <h3><?= e($product['name']) ?></h3>
                <p class="product-tagline"><?= e($product['tagline']) ?></p>
              </div>
            </div>

            <?php if ($product['image']): ?>
            <div class="product-img">
              <img src="<?= SITE_URL ?>/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
            </div>
            <?php endif; ?>

            <p class="product-desc"><?= e($product['short_description']) ?></p>

            <?php if ($features): ?>
            <ul class="product-features">
              <?php foreach ($features as $f): ?>
              <li><i class="fas fa-check-circle"></i> <?= e($f) ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if ($techStack): ?>
            <div class="card-tags" style="margin-top:16px">
              <?php foreach ($techStack as $t): ?>
              <span class="tag"><?= e($t) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="product-footer">
              <div class="product-price">
                <?php if ($product['price'] > 0): ?>
                <span class="price-label"><?= e($product['price_label']) ?></span>
                <span class="price-value"><?= e($product['currency']) ?> <?= number_format($product['price'], 0) ?></span>
                <?php else: ?>
                <span class="price-value">Contact for Pricing</span>
                <?php endif; ?>
              </div>
              <div class="product-actions">
                <?php if ($product['demo_url']): ?>
                <a href="<?= e($product['demo_url']) ?>" target="_blank" class="btn btn-outline btn-sm">Live Demo <i class="fas fa-external-link-alt"></i></a>
                <?php endif; ?>
                <a href="<?= SITE_URL ?>/products/<?= e($product['slug']) ?>" class="btn btn-primary btn-sm">View Details <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (empty($categories)): ?>
      <!-- Products without category -->
      <div class="products-grid">
        <?php foreach ($products as $product):
          $features  = json_decode($product['features']  ?? '[]', true) ?: [];
          $techStack = json_decode($product['tech_stack'] ?? '[]', true) ?: [];
        ?>
        <div class="product-card <?= $product['is_featured'] ? 'product-featured' : '' ?>">
          <?php if ($product['is_featured']): ?>
          <span class="product-badge-featured"><i class="fas fa-star"></i> Featured</span>
          <?php endif; ?>
          <div class="product-card-header">
            <div class="product-icon"><i class="<?= e($product['icon']) ?>"></i></div>
            <div><h3><?= e($product['name']) ?></h3><p class="product-tagline"><?= e($product['tagline']) ?></p></div>
          </div>
          <p class="product-desc"><?= e($product['short_description']) ?></p>
          <?php if ($features): ?>
          <ul class="product-features">
            <?php foreach ($features as $f): ?><li><i class="fas fa-check-circle"></i> <?= e($f) ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <div class="product-footer">
            <div class="product-price">
              <?php if ($product['price'] > 0): ?>
              <span class="price-label"><?= e($product['price_label']) ?></span>
              <span class="price-value"><?= e($product['currency']) ?> <?= number_format($product['price'], 0) ?></span>
              <?php else: ?>
              <span class="price-value">Contact for Pricing</span>
              <?php endif; ?>
            </div>
            <div class="product-actions">
              <?php if ($product['demo_url']): ?>
              <a href="<?= e($product['demo_url']) ?>" target="_blank" class="btn btn-outline btn-sm">Live Demo</a>
              <?php endif; ?>
              <a href="<?= $product['purchase_url'] ? e($product['purchase_url']) : SITE_URL.'/contact' ?>" class="btn btn-primary btn-sm"><?= e($product['button_text']) ?> <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:80px 0">
      <i class="fas fa-box-open" style="font-size:3rem;color:var(--text-lighter,#ccc);display:block;margin-bottom:16px"></i>
      <h3 style="color:var(--text-light)">Products Coming Soon</h3>
      <p style="color:var(--text-lighter)">We're preparing our product lineup. Check back shortly.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Need a Custom Solution?</h2>
    <p>Don't see exactly what you need? We build fully custom digital products tailored to your business requirements.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Talk to Us <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

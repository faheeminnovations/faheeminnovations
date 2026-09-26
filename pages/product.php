<?php
$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . (defined('SITE_URL') ? SITE_URL : '') . '/products'); exit; }
require_once '../includes/header.php';

$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); ?>
  <section class="page-hero"><div class="container"><h1>Product Not Found</h1><p><a href="<?= SITE_URL ?>/products">← Back to Products</a></p></div></section>
<?php require_once '../includes/footer.php'; exit; }

$pageTitle    = ($product['meta_title'] ?: $product['name'] . ' | Faheem Innovations');
$pageDesc     = $product['meta_description'] ?: $product['short_description'];
$features     = json_decode($product['features']   ?? '[]', true) ?: [];
$techStack    = json_decode($product['tech_stack']  ?? '[]', true) ?: [];
$packages     = json_decode($product['packages']    ?? '[]', true) ?: [];
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a><span>/</span>
      <a href="<?= SITE_URL ?>/products">Products</a><span>/</span>
      <span><?= e($product['name']) ?></span>
    </div>
    <div class="product-hero-inner">
      <div class="product-hero-icon"><i class="<?= e($product['icon']) ?>"></i></div>
      <div>
        <span class="section-badge"><?= e($product['category']) ?></span>
        <h1><?= e($product['name']) ?></h1>
        <p class="hero-desc"><?= e($product['tagline']) ?></p>
        <div class="hero-actions">
          <?php if ($product['demo_url']): ?>
          <a href="<?= e($product['demo_url']) ?>" target="_blank" class="btn btn-outline btn-lg">Live Demo <i class="fas fa-external-link-alt"></i></a>
          <?php endif; ?>
          <a href="<?= SITE_URL ?>/contact" class="btn btn-primary btn-lg"><?= e($product['button_text']) ?> <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Product Image -->
<?php if ($product['image']): ?>
<section class="section" style="padding-bottom:0">
  <div class="container">
    <img src="<?= SITE_URL ?>/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" style="width:100%;border-radius:var(--radius);box-shadow:var(--shadow-lg);display:block">
  </div>
</section>
<?php endif; ?>

<!-- Description + Features -->
<section class="section">
  <div class="container">
    <div class="product-detail-grid">
      <div>
        <span class="section-badge">About</span>
        <h2><?= e($product['name']) ?> — <?= e($product['tagline']) ?></h2>
        <p style="color:var(--text-light);line-height:1.8;margin-top:16px"><?= nl2br(e($product['full_description'])) ?></p>
        <?php if ($techStack): ?>
        <div style="margin-top:24px">
          <h4 style="margin-bottom:12px">Built With</h4>
          <div class="card-tags">
            <?php foreach ($techStack as $t): ?><span class="tag"><?= e($t) ?></span><?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($features): ?>
      <div class="product-features-box">
        <h4><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px"></i>What's Included</h4>
        <ul class="product-features" style="margin-top:16px">
          <?php foreach ($features as $f): ?>
          <li><i class="fas fa-check-circle"></i> <?= e($f) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Pricing Packages -->
<?php if ($packages): ?>
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Pricing</span>
      <h2 class="section-heading">Simple, Transparent Pricing</h2>
      <p class="section-desc">One-time setup fee. No hidden charges. Annual maintenance applies after the initial support period.</p>
    </div>
    <div class="pricing-grid" style="margin-top:48px">
      <?php foreach ($packages as $pkg): ?>
      <div class="pricing-card <?= !empty($pkg['recommended']) ? 'pricing-recommended' : '' ?>">
        <?php if (!empty($pkg['recommended'])): ?>
        <div class="pricing-badge"><i class="fas fa-star"></i> Recommended</div>
        <?php endif; ?>
        <div class="pricing-header">
          <h3><?= e($pkg['name']) ?></h3>
          <p class="pricing-subtitle"><?= e($pkg['subtitle']) ?></p>
          <div class="pricing-price">
            <span class="pricing-currency">Rs</span>
            <span class="pricing-amount"><?= number_format((int)$pkg['price']) ?></span>
          </div>
          <p class="pricing-label"><?= e($pkg['price_label']) ?></p>
        </div>
        <ul class="pricing-features">
          <?php foreach ($pkg['features'] as $f): ?>
          <li><i class="fas fa-check"></i> <?= e($f) ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="pricing-meta">
          <?php if (!empty($pkg['installment'])): ?>
          <div class="pricing-meta-row"><i class="fas fa-credit-card"></i> <?= e($pkg['installment']) ?></div>
          <?php endif; ?>
          <?php if (!empty($pkg['maintenance'])): ?>
          <div class="pricing-meta-row"><i class="fas fa-tools"></i> Maintenance: <?= e($pkg['maintenance']) ?></div>
          <?php endif; ?>
          <?php if (!empty($pkg['support'])): ?>
          <div class="pricing-meta-row"><i class="fas fa-headset"></i> <?= e($pkg['support']) ?></div>
          <?php endif; ?>
        </div>
        <a href="<?= SITE_URL ?>/contact" class="btn <?= !empty($pkg['recommended']) ? 'btn-primary' : 'btn-outline' ?>" style="width:100%;justify-content:center;margin-top:20px">
          Get Started <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Payment Table -->
    <div class="pricing-table-wrap">
      <h3 style="margin-bottom:20px;text-align:center">Payment & Maintenance Summary</h3>
      <table class="pricing-table">
        <thead>
          <tr>
            <th>Plan</th>
            <?php foreach ($packages as $pkg): ?><th><?= e($pkg['name']) ?></th><?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>One-time Payment</td>
            <?php foreach ($packages as $pkg): ?><td>Rs <?= number_format((int)$pkg['price']) ?></td><?php endforeach; ?>
          </tr>
          <tr>
            <td>3 Installments</td>
            <?php foreach ($packages as $pkg): ?><td><?= e($pkg['installment'] ?? '—') ?></td><?php endforeach; ?>
          </tr>
          <tr>
            <td>Annual Maintenance</td>
            <?php foreach ($packages as $pkg): ?><td><?= e($pkg['maintenance'] ?? '—') ?></td><?php endforeach; ?>
          </tr>
          <tr>
            <td>Initial Support</td>
            <?php foreach ($packages as $pkg): ?><td><?= e($pkg['support'] ?? '—') ?></td><?php endforeach; ?>
          </tr>
        </tbody>
      </table>
      <p style="font-size:0.82rem;color:var(--text-light);margin-top:14px;text-align:center">
        <i class="fas fa-info-circle"></i> Annual maintenance applies after the initial support period. Hosting, SMS/WhatsApp, data migration and custom development charges are defined separately.
      </p>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2>Ready to Modernize Your Institution?</h2>
    <p>Book a free demo and see how FaheemEdu360 can simplify your school or college management.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Book a Free Demo <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once '../includes/footer.php'; ?>

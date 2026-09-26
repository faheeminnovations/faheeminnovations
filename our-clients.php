<?php
$pageTitle = 'Our Clients - Trusted by Businesses Worldwide | Faheem Innovations';
$pageDesc  = 'Meet the businesses and organizations that trust Faheem Innovations to build their digital products.';
require_once 'includes/header.php';
$clients = getClients();
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>Our Clients</span></div>
    <h1>Trusted by Businesses Around the World.</h1>
    <p>We're proud to work with forward-thinking businesses and organizations who put their trust in us.</p>
  </div>
</section>

<section class="section our-clients-page">
  <div class="container">
    <?php if ($clients): ?>
    <div class="clients-grid">
      <?php foreach ($clients as $client): ?>
      <div class="client-card">
        <?php if ($client['logo']): ?>
          <div class="client-logo-wrap">
            <img src="<?= SITE_URL ?>/<?= e($client['logo']) ?>" alt="<?= e($client['name']) ?>" loading="lazy">
          </div>
        <?php else: ?>
          <div class="client-logo-placeholder">
            <i class="fas fa-building"></i>
          </div>
        <?php endif; ?>
        <div class="client-info">
          <h3><?= e($client['name']) ?></h3>
          <?php if ($client['industry']): ?>
          <span class="client-industry"><?= e($client['industry']) ?></span>
          <?php endif; ?>
          <?php if ($client['website']): ?>
          <a href="<?= e($client['website']) ?>" target="_blank" rel="noopener" class="client-link">
            <i class="fas fa-external-link-alt"></i> Visit Website
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align:center;padding:60px 0">
      <i class="fas fa-building" style="font-size:3rem;color:var(--text-lighter);margin-bottom:16px;display:block"></i>
      <h3 style="color:var(--text-light)">Clients Coming Soon</h3>
      <p style="color:var(--text-lighter)">We're adding our client showcase. Check back shortly.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Ready to Join Our Client List?</h2>
    <p>Let's talk about your project and how we can help you achieve your goals.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Start a Project <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

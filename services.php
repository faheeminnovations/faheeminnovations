<?php
$pageTitle = 'Our Services - Web Development, Software, AI Solutions | Faheem Innovations';
$pageDesc = 'From websites to custom business software and AI-powered solutions, we build technology around your goals.';
require_once 'includes/header.php';
$services = getServices();
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>Services</span></div>
    <h1>Our Digital Services</h1>
    <p>From websites to custom business software and AI-powered solutions, we build technology around your goals.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cards-grid">
      <?php foreach ($services as $svc):
        $features = json_decode($svc['features'] ?? '[]', true);
      ?>
      <div class="card service-card" id="<?= e($svc['slug']) ?>">
        <div class="card-icon"><i class="<?= e($svc['icon']) ?>"></i></div>
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e($svc['full_description'] ?: $svc['short_description']) ?></p>
        <?php if ($features): ?>
        <ul style="margin-top:16px;padding-left:0">
          <?php foreach ($features as $f): ?>
          <li style="display:flex;align-items:center;gap:8px;font-size:0.88rem;color:var(--text-light);margin-bottom:6px"><i class="fas fa-check" style="color:var(--primary);font-size:0.75rem;flex-shrink:0"></i><?= e($f) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <a href="<?= SITE_URL ?>/contact" class="btn btn-primary btn-sm" style="margin-top:20px"><?= e($svc['cta_text'] ?: 'Get Started') ?></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Have a Project in Mind?</h2>
    <p>Tell us what you need and let's discuss how we can help build the right solution.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Start a Conversation <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = 'How We Work | Faheem Innovations';
$pageDesc = 'A structured process helps us transform your requirements into a reliable digital product.';
require_once 'includes/header.php';
$steps = getProcessSteps();
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>Process</span></div>
    <h1>How We Work</h1>
    <p>A structured process helps us transform your requirements into a reliable digital product.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="max-width:800px;margin:0 auto">
      <?php foreach ($steps as $i => $step): ?>
      <div style="display:flex;gap:32px;margin-bottom:48px;align-items:flex-start">
        <div style="flex-shrink:0;text-align:center">
          <div style="width:64px;height:64px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem;margin:0 auto"><?= e($step['step_number']) ?></div>
          <?php if ($i < count($steps)-1): ?>
          <div style="width:2px;height:40px;background:var(--border);margin:8px auto"></div>
          <?php endif; ?>
        </div>
        <div style="padding-top:12px">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
            <i class="<?= e($step['icon']) ?>" style="color:var(--primary);font-size:1.2rem"></i>
            <h3 style="font-size:1.2rem"><?= e($step['title']) ?></h3>
          </div>
          <p style="color:var(--text-light)"><?= e($step['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Ready to Start?</h2>
    <p>Contact us to discuss your project and we'll walk you through the process.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Get in Touch <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = 'FAQs - Frequently Asked Questions | Faheem Innovations';
$pageDesc = 'Find answers to common questions about Faheem Innovations services and development process.';
require_once 'includes/header.php';
$faqs = getFAQs();
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>FAQs</span></div>
    <h1>FAQs</h1>
    <p>Find answers to common questions about our services and how we work.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="faq-list">
      <?php foreach ($faqs as $faq): ?>
      <div class="faq-item">
        <div class="faq-question">
          <span><?= e($faq['question']) ?></span>
          <i class="fas fa-chevron-down"></i>
        </div>
        <div class="faq-answer"><p><?= e($faq['answer']) ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Still Have Questions?</h2>
    <p>Contact us directly and we'll be happy to help.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Contact Us <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

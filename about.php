<?php
$pageTitle = 'About Faheem Innovations - Technology Built Around Your Business';
$pageDesc = 'Learn about Faheem Innovations, a technology company focused on web development, software development, AI solutions and custom digital products.';
require_once 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>About</span></div>
    <h1>Building Technology With Purpose.</h1>
    <p>Faheem Innovations helps businesses and organizations transform ideas into practical digital products.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="about-intro">
      <div class="about-img">
        <img src="<?= SITE_URL ?>/images/who-we-are.jpeg" alt="We Turn Ideas Into Digital Solutions" style="width:100%;border-radius:var(--radius);box-shadow:var(--shadow-lg)">
      </div>
      <div class="about-content">
        <span class="section-badge">About Us</span>
        <h2>We Turn Ideas Into Digital Solutions.</h2>
        <p>Faheem Innovations is a technology company focused on web development, software development, AI solutions and custom digital products.</p>
        <p>We work with businesses, organizations and entrepreneurs to create technology that solves real problems and supports long-term growth.</p>
        <p>Our approach combines modern technology with a clear understanding of business requirements.</p>
        <a href="<?= SITE_URL ?>/contact" class="btn btn-primary" style="margin-top:8px">Start a Project <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="cards-grid cards-grid-2">
      <div class="card">
        <div class="card-icon"><i class="fas fa-bullseye"></i></div>
        <h3>Our Mission</h3>
        <p>Our mission is to make modern technology more accessible and useful for businesses by creating reliable, practical and user-friendly digital solutions.</p>
      </div>
      <div class="card">
        <div class="card-icon"><i class="fas fa-eye"></i></div>
        <h3>Our Vision</h3>
        <p>Our vision is to build innovative digital products that help businesses operate more efficiently, connect with their customers and grow in an increasingly digital world.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Our Values</span>
      <h2 class="section-heading">What We Stand For</h2>
    </div>
    <div class="values-grid">
      <?php
      $values = [
        ['fas fa-lightbulb','Innovation','We continuously explore better ways to solve digital problems.'],
        ['fas fa-medal','Quality','We focus on reliable, maintainable and user-friendly solutions.'],
        ['fas fa-handshake','Transparency','We believe in clear communication and straightforward project processes.'],
        ['fas fa-users','Customer Focus','We build around real customer and business requirements.'],
        ['fas fa-sync-alt','Continuous Improvement','We believe every digital product can continue to improve.'],
      ];
      foreach ($values as $v): ?>
      <div class="value-card">
        <h3><i class="<?= $v[0] ?>" style="margin-right:8px"></i><?= $v[1] ?></h3>
        <p><?= $v[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Ready to Build Something?</h2>
    <p>Let's discuss your project requirements and how we can help.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Get in Touch <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

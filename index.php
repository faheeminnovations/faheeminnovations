<?php
$pageTitle = 'Faheem Innovations - Web Development, Software & AI Solutions';
$pageDesc = 'Faheem Innovations creates modern websites, custom software, AI-powered tools and digital solutions designed to help businesses work smarter and grow online.';
require_once 'includes/header.php';

$services = getServices(6);
$tools = getAITools(6);
$projects = getProjects(3, true);
$stats = getStatistics();
$steps = getProcessSteps();
$testimonials = getTestimonials();
$clients = getClients();
?>

<!-- Hero -->
<section class="hero">
  <div class="container">
    <div class="hero-inner">
      <div class="hero-content">
        <span class="hero-badge">Digital Solutions for Modern Businesses</span>
        <h1>We Build Digital Solutions That <span>Move Your Business Forward.</span></h1>
        <p class="hero-desc">Faheem Innovations creates modern websites, custom software, AI-powered tools and digital solutions designed to help businesses work smarter, serve customers better and grow online.</p>
        <div class="hero-actions">
          <a href="<?= SITE_URL ?>/contact" class="btn btn-primary btn-lg">Start Your Project <i class="fas fa-arrow-right"></i></a>
          <a href="<?= SITE_URL ?>/services" class="btn btn-outline btn-lg">Explore Our Services</a>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-visual-inner">
          <img src="<?= SITE_URL ?>/images/banner1.jpg" alt="Faheem Innovations Digital Solutions">
        </div>
        <div class="hero-float hero-float-1"><i class="fas fa-check-circle"></i> 50+ Projects Delivered</div>
        <div class="hero-float hero-float-2"><i class="fas fa-star"></i> Trusted by 30+ Clients</div>
      </div>
    </div>
  </div>
</section>

<!-- Stats -->
<?php if ($stats): ?>
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <?php foreach ($stats as $s): ?>
      <div class="stat-item">
        <div class="stat-value"><?= e($s['value']) ?></div>
        <div class="stat-label"><?= e($s['label']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Our Clients -->
<?php if ($clients): ?>
<section class="section clients-section">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Our Clients</span>
      <h2 class="section-heading">Businesses That Trust Us</h2>
      <p class="section-desc">We have worked with businesses and organizations across different industries to build reliable digital solutions.</p>
    </div>
    <div class="clients-grid">
      <?php foreach ($clients as $client): ?>
      <div class="client-card">
        <?php if ($client['logo']): ?>
          <img src="<?= SITE_URL ?>/<?= e($client['logo']) ?>" alt="<?= e($client['name']) ?>" loading="lazy">
        <?php else: ?>
          <div class="client-name-box">
            <span><?= e($client['name']) ?></span>
          </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Our Products -->
<?php $featuredProducts = getProducts(3, true); ?>
<?php if ($featuredProducts): ?>
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Our Products</span>
      <h2 class="section-heading">Ready-Made Digital Solutions.</h2>
      <p class="section-desc">Powerful software products you can deploy immediately — from ERP systems to SaaS platforms built for real business needs.</p>
    </div>
    <div class="products-grid" style="margin-top:48px">
      <?php foreach ($featuredProducts as $product):
        $features = json_decode($product['features'] ?? '[]', true) ?: [];
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
        <p class="product-desc"><?= e($product['short_description']) ?></p>
        <?php if ($features): ?>
        <ul class="product-features">
          <?php foreach (array_slice($features, 0, 4) as $f): ?>
          <li><i class="fas fa-check-circle"></i> <?= e($f) ?></li>
          <?php endforeach; ?>
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
          <a href="<?= SITE_URL ?>/products" class="btn btn-primary btn-sm">Learn More <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="section-cta"><a href="<?= SITE_URL ?>/products" class="btn btn-primary">View All Products <i class="fas fa-arrow-right"></i></a></div>
  </div>
</section>
<?php endif; ?>

<!-- Who We Are -->
<section class="section">
  <div class="container">
    <div class="about-intro">
      <div class="about-img">
        <img src="<?= SITE_URL ?>/images/who-we-are.jpeg" alt="Faheem Innovations - We Turn Ideas Into Digital Solutions" style="width:100%;border-radius:var(--radius);box-shadow:var(--shadow-lg)">
      </div>
      <div class="about-content">
        <span class="section-badge">Who We Are</span>
        <h2>Technology Built Around Your Business.</h2>
        <p>Faheem Innovations is a technology and digital solutions company focused on building practical, modern and scalable solutions for businesses, organizations and entrepreneurs.</p>
        <p>From professional websites and custom business software to AI-powered tools and digital platforms, we turn ideas into useful products that solve real business problems.</p>
        <a href="<?= SITE_URL ?>/about" class="btn btn-primary" style="margin-top:8px">Learn More About Us <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- Services -->
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">What We Do</span>
      <h2 class="section-heading">Digital Solutions Designed for Real Business Needs.</h2>
      <p class="section-desc">We combine development expertise, modern technology and business understanding to create digital products that are reliable, scalable and easy to use.</p>
    </div>
    <div class="cards-grid">
      <?php foreach ($services as $svc): ?>
      <div class="card service-card">
        <div class="card-icon"><i class="<?= e($svc['icon']) ?>"></i></div>
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e($svc['short_description']) ?></p>
        <a href="<?= SITE_URL ?>/services/<?= e($svc['slug']) ?>" class="card-link">Learn More <i class="fas fa-arrow-right"></i></a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="section-cta"><a href="<?= SITE_URL ?>/services" class="btn btn-primary">View All Services <i class="fas fa-arrow-right"></i></a></div>
  </div>
</section>

<!-- AI Tools -->
<section class="section">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">AI Tools</span>
      <h2 class="section-heading">Practical AI Tools for Everyday Work.</h2>
      <p class="section-desc">Explore simple and useful AI-powered tools designed to help users handle common digital tasks faster and more efficiently.</p>
    </div>
    <div class="cards-grid">
      <?php foreach ($tools as $tool): ?>
      <div class="card tool-card">
        <span class="tool-badge <?= $tool['is_free'] ? 'free' : 'paid' ?>"><?= $tool['is_free'] ? 'Free' : 'Paid' ?></span>
        <div class="card-icon"><i class="<?= e($tool['icon']) ?>"></i></div>
        <h3><?= e($tool['name']) ?></h3>
        <p><?= e($tool['description']) ?></p>
        <?php if ($tool['tool_url']): ?>
        <a href="<?= e($tool['tool_url']) ?>" class="btn btn-outline btn-sm" target="_blank"><?= e($tool['button_text']) ?></a>
        <?php else: ?>
        <a href="<?= SITE_URL ?>/ai-tools/<?= e($tool['slug']) ?>" class="btn btn-outline btn-sm"><?= e($tool['button_text']) ?></a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="section-cta"><a href="<?= SITE_URL ?>/ai-tools" class="btn btn-primary">Explore All AI Tools <i class="fas fa-arrow-right"></i></a></div>
  </div>
</section>

<!-- Why Us -->
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Why Choose Us</span>
      <h2 class="section-heading">Technology With a Clear Purpose.</h2>
      <p class="section-desc">We focus on building solutions that are useful, understandable and aligned with real business requirements.</p>
    </div>
    <div class="why-grid">
      <?php
      $whyItems = [
        ['fas fa-bullseye','Business-Focused Development','We understand the business requirement before building the technology.'],
        ['fas fa-microchip','Modern Technology','We use modern development tools and technologies to create reliable digital products.'],
        ['fas fa-puzzle-piece','Custom Solutions','Your business is different, so your software should be built around your actual requirements.'],
        ['fas fa-expand-arrows-alt','Scalable Architecture','Solutions are designed with future growth and expansion in mind.'],
        ['fas fa-mobile-alt','Responsive Experience','Our websites and applications are designed to work across desktop, tablet and mobile devices.'],
        ['fas fa-chart-line','Long-Term Value','We aim to build digital products that continue to provide value as your business grows.'],
      ];
      foreach ($whyItems as $w): ?>
      <div class="why-item">
        <div class="why-icon"><i class="<?= $w[0] ?>"></i></div>
        <h3><?= $w[1] ?></h3>
        <p><?= $w[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Process -->
<section class="section">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Our Process</span>
      <h2 class="section-heading">From Idea to Working Product.</h2>
      <p class="section-desc">Our development process keeps the project organized, transparent and focused on the final business objective.</p>
    </div>
    <div class="process-grid">
      <?php foreach ($steps as $step): ?>
      <div class="process-step">
        <div class="step-num"><?= e($step['step_number']) ?></div>
        <div class="step-icon"><i class="<?= e($step['icon']) ?>"></i></div>
        <h3><?= e($step['title']) ?></h3>
        <p><?= e($step['description']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Projects -->
<?php if ($projects): ?>
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Our Work</span>
      <h2 class="section-heading">Solutions Built for Different Business Needs.</h2>
      <p class="section-desc">Explore selected projects and digital solutions developed for businesses and organizations.</p>
    </div>
    <div class="cards-grid">
      <?php foreach ($projects as $proj): ?>
      <div class="card project-card">
        <div class="project-img">
          <?php if ($proj['main_image']): ?>
          <img src="<?= SITE_URL ?>/<?= e($proj['main_image']) ?>" alt="<?= e($proj['name']) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;"><i class="fas fa-laptop-code" style="font-size:2.5rem;color:rgba(255,255,255,0.4)"></i></div>
          <?php endif; ?>
        </div>
        <div class="project-body">
          <div class="project-cat"><?= e($proj['category']) ?></div>
          <h3><?= e($proj['name']) ?></h3>
          <p><?= e($proj['short_description']) ?></p>
          <?php if ($proj['technologies']): $techs = json_decode($proj['technologies'], true); ?>
          <div class="card-tags"><?php foreach (array_slice($techs,0,3) as $t): ?><span class="tag"><?= e($t) ?></span><?php endforeach; ?></div>
          <?php endif; ?>
          <a href="<?= SITE_URL ?>/projects/<?= e($proj['slug']) ?>" class="btn btn-outline btn-sm" style="margin-top:16px">View Project</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="section-cta"><a href="<?= SITE_URL ?>/projects" class="btn btn-primary">View All Projects <i class="fas fa-arrow-right"></i></a></div>
  </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<?php if ($testimonials): ?>
<section class="section">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">Client Feedback</span>
      <h2 class="section-heading">What Our Clients Say</h2>
    </div>
    <div class="cards-grid">
      <?php foreach ($testimonials as $t): ?>
      <div class="card testimonial-card">
        <div class="testimonial-rating"><?= str_repeat('★', (int)$t['rating']) ?></div>
        <p class="testimonial-text">"<?= e($t['testimonial']) ?>"</p>
        <div class="testimonial-author">
          <?php if ($t['image']): ?>
          <img src="<?= SITE_URL ?>/<?= e($t['image']) ?>" alt="<?= e($t['client_name']) ?>">
          <?php else: ?>
          <div class="author-avatar"><?= strtoupper(substr($t['client_name'],0,1)) ?></div>
          <?php endif; ?>
          <div class="author-info">
            <strong><?= e($t['client_name']) ?></strong>
            <span><?= e($t['position']) ?><?= $t['company'] ? ', ' . e($t['company']) : '' ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Final CTA -->
<section class="cta-section">
  <div class="container">
    <h2>Have an Idea? Let's Build It.</h2>
    <p>Whether you need a professional website, custom business software, an AI-powered tool or a complete digital solution, let's discuss your requirements.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Start a Conversation <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

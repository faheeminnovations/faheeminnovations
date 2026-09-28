<?php
$pageTitle = "Contact Faheem Innovations - Start Your Project";
$pageDesc = "Have a project idea? Contact Faheem Innovations to discuss your web development, software or AI solution requirements.";
require_once 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>Contact</span></div>
    <h1>Let's Build Something Useful.</h1>
    <p>Have a project idea, business requirement or digital challenge? Tell us what you need and let's discuss how technology can help.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-grid">
      <div class="contact-info">
        <h3>Get in Touch</h3>
        <div class="contact-item">
          <div class="contact-item-icon"><i class="fas fa-envelope"></i></div>
          <div class="contact-item-text">
            <strong>Email</strong>
            <a href="mailto:<?= e(getSetting('site_email')) ?>"><?= e(getSetting('site_email')) ?></a>
          </div>
        </div>
        <div class="contact-item">
          <div class="contact-item-icon"><i class="fab fa-whatsapp"></i></div>
          <div class="contact-item-text">
            <strong>WhatsApp / Phone</strong>
            <a href="https://wa.me/<?= e(getSetting('site_whatsapp','923041277320')) ?>" target="_blank" rel="noopener"><?= e(getSetting('site_phone')) ?></a>
          </div>
        </div>
        <div class="contact-item">
          <div class="contact-item-icon"><i class="fas fa-globe"></i></div>
          <div class="contact-item-text">
            <strong>Website</strong>
            <a href="https://faheeminnovations.online" target="_blank">faheeminnovations.online</a>
          </div>
        </div>
        <div class="contact-item">
          <div class="contact-item-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="contact-item-text">
            <strong>Address</strong>
            <a href="https://share.google/XJHC6Z5ywW8ddxxvi" target="_blank" rel="noopener"><?= e(getSetting('site_address')) ?></a>
          </div>
        </div>
      </div>

      <div class="contact-form">
        <div class="form-success" id="formSuccess">
          Thank you for contacting Faheem Innovations. Your enquiry has been received and our team will get back to you.
        </div>
        <form id="contactForm" action="<?= SITE_URL ?>/includes/submit-enquiry.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="recaptcha_token" id="recaptchaToken">
          <div class="form-row">
            <div class="form-group">
              <label>Name *</label>
              <input type="text" name="name" required placeholder="Your name">
            </div>
            <div class="form-group">
              <label>Email *</label>
              <input type="email" name="email" required placeholder="your@email.com">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Phone</label>
              <input type="tel" name="phone" placeholder="Your phone number">
            </div>
            <div class="form-group">
              <label>Company</label>
              <input type="text" name="company" placeholder="Your company name">
            </div>
          </div>
          <div class="form-group">
            <label>Subject *</label>
            <input type="text" name="subject" required placeholder="What is this about?">
          </div>
          <div class="form-group">
            <label>Message *</label>
            <textarea name="message" required placeholder="Tell us about your project or requirements..."></textarea>
          </div>
          <div class="g-recaptcha" data-sitekey="6LdpztMtAAAAAIkkcoADI6AMhe7WxjkzrbQ6fHq9" style="margin-bottom:16px"></div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Send Enquiry <i class="fas fa-paper-plane"></i></button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Google Maps -->
<section class="section section-alt" style="padding-top:0">
  <div class="container">
    <div style="border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);border:1px solid var(--border)">
      <div style="background:var(--primary);padding:16px 24px;display:flex;align-items:center;gap:10px">
        <i class="fas fa-map-marker-alt" style="color:#fff;font-size:1.1rem"></i>
        <span style="color:#fff;font-weight:600">Find Us — <?= e(getSetting('site_address')) ?></span>
        <a href="https://share.google/XJHC6Z5ywW8ddxxvi" target="_blank" rel="noopener" style="margin-left:auto;color:rgba(255,255,255,.8);font-size:.85rem;text-decoration:none"><i class="fas fa-external-link-alt"></i> Open in Google Maps</a>
      </div>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3407.0!2d73.9856!3d30.6436!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDM4JzM3LjAiTiA3M8KwNTknMDguMiJF!5e0!3m2!1sen!2spk!4v1234567890"
        width="100%" height="400" style="border:0;display:block" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Faheem Innovations Location — Depalpur, Pakistan">
      </iframe>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php
$footerDesc = getSetting('footer_description', 'Faheem Innovations builds modern websites, custom software, AI solutions and digital products designed around real business needs.');
$siteEmail = getSetting('site_email', 'hello@faheeminnovations.online');
$sitePhone = getSetting('site_phone', '0304-1277320');
$siteAddress = getSetting('site_address', 'Depalpur, Pakistan');
$socialLinks = getSocialLinks();
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="<?= SITE_URL ?>/" class="logo">
        <img src="<?= SITE_URL ?>/<?= e(getSetting('site_logo','images/main-logo.svg')) ?>" alt="<?= e(getSetting('site_name')) ?>" height="40">
        </a>
        <p><?= e($footerDesc) ?></p>
        <div class="social-links">
          <?php foreach ($socialLinks as $s): if (!$s['url']) continue; ?>
          <a href="<?= e($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['platform']) ?>"><i class="<?= e($s['icon']) ?>"></i></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="<?= SITE_URL ?>/">Home</a></li>
          <li><a href="<?= SITE_URL ?>/about">About</a></li>
          <li><a href="<?= SITE_URL ?>/services">Services</a></li>
          <li><a href="<?= SITE_URL ?>/ai-tools">AI Tools</a></li>
          <li><a href="<?= SITE_URL ?>/products">Products</a></li>
          <li><a href="<?= SITE_URL ?>/projects">Projects</a></li>
          <li><a href="<?= SITE_URL ?>/process">Process</a></li>
          <li><a href="<?= SITE_URL ?>/faqs">FAQs</a></li>
          <li><a href="<?= SITE_URL ?>/our-clients">Our Clients</a></li>
          <li><a href="<?= SITE_URL ?>/contact">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Services</h4>
        <ul>
          <?php foreach (getServices() as $svc): ?>
          <li><a href="<?= SITE_URL ?>/services/<?= e($svc['slug']) ?>"><?= e($svc['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <ul class="contact-list">
          <li><i class="fas fa-envelope"></i><a href="mailto:<?= e($siteEmail) ?>"><?= e($siteEmail) ?></a></li>
          <li><i class="fab fa-whatsapp"></i><a href="https://wa.me/<?= e(getSetting('site_whatsapp','923041277320')) ?>" target="_blank" rel="noopener"><?= e($sitePhone) ?></a></li>
          <li><i class="fas fa-map-marker-alt"></i><?= e($siteAddress) ?></li>
        </ul>
        <div class="footer-cta">
          <p>Have a project in mind?</p>
          <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light">Let's Talk</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= e(getSetting('site_name')) ?>. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="<?= SITE_URL ?>/assets/main.js"></script>

<?php
// Fetch services, products, tools for WhatsApp popup
$waServices = getServices();
$waProducts = getProducts();
$waTools    = getAITools();
$waPhone    = getSetting('site_whatsapp', '923041277320');
?>

<!-- WhatsApp Floating Button -->
<div id="waBtn" onclick="toggleWaPopup()" title="Chat on WhatsApp" aria-label="WhatsApp">
  <i class="fab fa-whatsapp"></i>
  <span class="wa-pulse"></span>
</div>

<!-- WhatsApp Popup -->
<div id="waPopup" role="dialog" aria-modal="true" aria-label="WhatsApp Chat">
  <div class="wa-popup-header">
    <div class="wa-popup-avatar"><i class="fab fa-whatsapp"></i></div>
    <div>
      <div class="wa-popup-name"><?= e(getSetting('site_name','Faheem Innovations')) ?></div>
      <div class="wa-popup-status"><span class="wa-online-dot"></span> Typically replies quickly</div>
    </div>
    <button class="wa-close" onclick="toggleWaPopup()" aria-label="Close">✕</button>
  </div>

  <div class="wa-popup-body">
    <div class="wa-bubble">
      👋 Hi! How can we help you today?<br>Select what you're interested in and we'll get back to you on WhatsApp.
    </div>

    <div class="wa-form">
      <div class="wa-form-group">
        <label>Your Name</label>
        <input type="text" id="waName" placeholder="Enter your name">
      </div>

      <div class="wa-form-group">
        <label>I'm interested in</label>
        <select id="waCategory" onchange="updateWaOptions()">
          <option value="">— Select category —</option>
          <option value="service">💼 Services</option>
          <option value="product">📦 Products</option>
          <option value="tool">🤖 AI Tools</option>
          <option value="general">💬 General Inquiry</option>
        </select>
      </div>

      <div class="wa-form-group" id="waOptionGroup" style="display:none">
        <label id="waOptionLabel">Select option</label>
        <select id="waOption">
          <option value="">— Select —</option>
        </select>
      </div>

      <div class="wa-form-group">
        <label>Message (optional)</label>
        <textarea id="waMessage" placeholder="Any additional details..." rows="2"></textarea>
      </div>

      <button class="wa-send-btn" onclick="sendWhatsApp()">
        <i class="fab fa-whatsapp"></i> Open WhatsApp Chat
      </button>
    </div>
  </div>
</div>

<script>
const WA_PHONE = '<?= e($waPhone) ?>';
const WA_DATA  = {
  service: <?= json_encode(array_map(fn($s) => $s['name'], $waServices)) ?>,
  product: <?= json_encode(array_map(fn($p) => $p['name'], $waProducts)) ?>,
  tool:    <?= json_encode(array_map(fn($t) => $t['name'], $waTools)) ?>,
};

function toggleWaPopup() {
  const p = document.getElementById('waPopup');
  const b = document.getElementById('waBtn');
  const open = p.classList.toggle('open');
  b.classList.toggle('active', open);
  if (open) document.getElementById('waName').focus();
}

function updateWaOptions() {
  const cat   = document.getElementById('waCategory').value;
  const group = document.getElementById('waOptionGroup');
  const sel   = document.getElementById('waOption');
  const label = document.getElementById('waOptionLabel');
  const items = WA_DATA[cat] || [];

  if (!items.length) { group.style.display = 'none'; return; }

  label.textContent = cat === 'service' ? 'Select Service' : cat === 'product' ? 'Select Product' : 'Select Tool';
  sel.innerHTML = '<option value="">— Select —</option>' + items.map(i => `<option value="${i}">${i}</option>`).join('');
  group.style.display = 'block';
}

function sendWhatsApp() {
  const name    = document.getElementById('waName').value.trim();
  const cat     = document.getElementById('waCategory').value;
  const option  = document.getElementById('waOption').value;
  const message = document.getElementById('waMessage').value.trim();

  if (!name) { document.getElementById('waName').focus(); document.getElementById('waName').style.borderColor='#ef4444'; return; }

  const catLabels = {service:'Service', product:'Product', tool:'AI Tool', general:'General Inquiry'};
  let text = `Hi Faheem Innovations! 👋\n\nName: ${name}`;
  if (cat)    text += `\nInterested in: ${catLabels[cat] || cat}`;
  if (option) text += `\nSpecific: ${option}`;
  if (message) text += `\n\nMessage: ${message}`;

  window.open(`https://wa.me/${WA_PHONE}?text=${encodeURIComponent(text)}`, '_blank');
}

// Close popup on outside click
document.addEventListener('click', e => {
  const popup = document.getElementById('waPopup');
  const btn   = document.getElementById('waBtn');
  if (popup.classList.contains('open') && !popup.contains(e.target) && !btn.contains(e.target)) {
    popup.classList.remove('open');
    btn.classList.remove('active');
  }
});
</script>
</body>
</html>

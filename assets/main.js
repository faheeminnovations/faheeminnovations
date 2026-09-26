// Faheem Innovations - Main JS
document.addEventListener('DOMContentLoaded', function () {

  // Sticky header
  const header = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    header && header.classList.toggle('scrolled', window.scrollY > 20);
  });

  // Mobile nav toggle
  const toggle = document.getElementById('navToggle');
  const nav = document.getElementById('mainNav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      nav.classList.toggle('open');
      toggle.classList.toggle('active');
    });
  }

  // Mobile dropdown toggle — only on mobile (≤768px), desktop uses pure CSS :hover
  document.querySelectorAll('.has-dropdown > a').forEach(link => {
    link.addEventListener('click', function (e) {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        const li = this.closest('.has-dropdown');
        const isOpen = li.classList.contains('open');
        document.querySelectorAll('.has-dropdown.open').forEach(el => el.classList.remove('open'));
        if (!isOpen) li.classList.add('open');
      }
    });
  });

  // FAQ accordion
  document.querySelectorAll('.faq-question').forEach(q => {
    q.addEventListener('click', () => {
      const item = q.closest('.faq-item');
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });

  // Counter animation
  function animateCounter(el) {
    const target = parseInt(el.dataset.target);
    const suffix = el.dataset.suffix || '';
    const duration = 1500;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
      current += step;
      if (current >= target) { current = target; clearInterval(timer); }
      el.textContent = Math.floor(current) + suffix;
    }, 16);
  }

  const counters = document.querySelectorAll('[data-target]');
  if (counters.length) {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { animateCounter(e.target); obs.unobserve(e.target); } });
    }, { threshold: 0.5 });
    counters.forEach(c => obs.observe(c));
  }

  // Scroll reveal
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    const revealObs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); revealObs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    reveals.forEach(r => revealObs.observe(r));
  }

  // Contact form with reCAPTCHA v3
  const form = document.getElementById('contactForm');
  if (form) {
    // reCAPTCHA done event — actual submit
    form.addEventListener('recaptcha_done', async function() {
      const btn = form.querySelector('[type=submit]');
      const msg = document.getElementById('formSuccess');
      btn.disabled = true;
      btn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin"></i>';
      try {
        const res = await fetch(form.action, { method: 'POST', body: new FormData(form) });
        const data = await res.json();
        if (data.success) {
          msg && msg.classList.add('show');
          form.reset();
        } else {
          alert(data.message || 'Something went wrong.');
        }
      } catch {
        alert('Network error. Please try again.');
      }
      btn.disabled = false;
      btn.innerHTML = 'Send Enquiry <i class="fas fa-paper-plane"></i>';
    });
  }
});

// Auto-open WhatsApp popup after 3 seconds (only once per session)
setTimeout(function() {
  var popup = document.getElementById('waPopup');
  var btn   = document.getElementById('waBtn');
  if (!sessionStorage.getItem('waShown') && popup && btn) {
    popup.classList.add('open');
    btn.classList.add('active');
    sessionStorage.setItem('waShown', '1');
  }
}, 3000);

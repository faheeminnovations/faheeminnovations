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

  // Contact form
  const form = document.getElementById('contactForm');
  if (form) {
    // Add inline error div after recaptcha
    const recapDiv = form.querySelector('.g-recaptcha');
    if (recapDiv) {
      const errDiv = document.createElement('div');
      errDiv.id = 'recaptchaError';
      errDiv.style.cssText = 'display:none;color:#dc2626;font-size:.83rem;margin-bottom:10px;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px';
      errDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please complete the reCAPTCHA verification.';
      recapDiv.insertAdjacentElement('afterend', errDiv);
    }

    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn  = form.querySelector('[type=submit]');
      const msg  = document.getElementById('formSuccess');
      const recaptchaError = document.getElementById('recaptchaError');

      // Check reCAPTCHA inline
      if (typeof grecaptcha !== 'undefined') {
        const response = grecaptcha.getResponse();
        if (!response) {
          if (recaptchaError) recaptchaError.style.display = 'block';
          return;
        }
      }
      if (recaptchaError) recaptchaError.style.display = 'none';

      btn.disabled = true;
      btn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin"></i>';
      try {
        const res  = await fetch(form.action, { method: 'POST', body: new FormData(form) });
        const data = await res.json();
        if (data.success) {
          msg && msg.classList.add('show');
          form.reset();
          if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
        } else {
          // Show server errors inline
          let errBox = document.getElementById('formError');
          if (!errBox) {
            errBox = document.createElement('div');
            errBox.id = 'formError';
            errBox.style.cssText = 'color:#dc2626;font-size:.85rem;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin-bottom:12px';
            form.insertBefore(errBox, form.firstChild);
          }
          errBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Something went wrong.');
          errBox.style.display = 'block';
        }
      } catch {
        let errBox = document.getElementById('formError');
        if (!errBox) {
          errBox = document.createElement('div');
          errBox.id = 'formError';
          errBox.style.cssText = 'color:#dc2626;font-size:.85rem;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin-bottom:12px';
          form.insertBefore(errBox, form.firstChild);
        }
        errBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> Network error. Please try again.';
        errBox.style.display = 'block';
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

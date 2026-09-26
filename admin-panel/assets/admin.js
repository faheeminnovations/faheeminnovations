// Faheem Innovations Admin JS
document.addEventListener('DOMContentLoaded', function () {

  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('adminOverlay');
  const toggle  = document.getElementById('sidebarToggle');
  const close   = document.getElementById('sidebarClose');

  function openSidebar() { sidebar.classList.add('open'); overlay.classList.add('show'); }
  function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

  toggle && toggle.addEventListener('click', openSidebar);
  close  && close.addEventListener('click', closeSidebar);
  overlay && overlay.addEventListener('click', closeSidebar);

  // Confirm delete
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });

  // Auto-hide alerts
  document.querySelectorAll('.alert').forEach(a => {
    setTimeout(() => { a.style.opacity = '0'; a.style.transition = '0.5s'; setTimeout(() => a.remove(), 500); }, 4000);
  });

  // Slug generator
  const nameInput = document.getElementById('name');
  const slugInput = document.getElementById('slug');
  if (nameInput && slugInput && !slugInput.value) {
    nameInput.addEventListener('input', function () {
      slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    });
  }

  // Image preview
  document.querySelectorAll('input[type=file][data-preview]').forEach(input => {
    input.addEventListener('change', function () {
      const preview = document.getElementById(this.dataset.preview);
      if (preview && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });
});

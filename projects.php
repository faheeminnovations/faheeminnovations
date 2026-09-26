<?php
$pageTitle = 'Our Projects | Faheem Innovations';
$pageDesc = 'A selection of websites, software systems, AI tools and digital solutions developed by Faheem Innovations.';
require_once 'includes/header.php';
$projects = getProjects();
$categories = ['All'];
foreach ($projects as $p) { if ($p['category'] && !in_array($p['category'], $categories)) $categories[] = $p['category']; }
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>Projects</span></div>
    <h1>Our Projects</h1>
    <p>A selection of websites, software systems, AI tools and digital solutions developed by Faheem Innovations.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (count($categories) > 1): ?>
    <div class="filter-tabs" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:40px;justify-content:center">
      <?php foreach ($categories as $cat): ?>
      <button class="filter-btn <?= $cat==='All'?'active':'' ?>" data-filter="<?= e($cat) ?>" style="padding:8px 20px;border-radius:50px;border:2px solid var(--border);background:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;transition:0.3s"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($projects): ?>
    <div class="cards-grid" id="projectsGrid">
      <?php foreach ($projects as $proj): ?>
      <div class="card project-card" data-category="<?= e($proj['category']) ?>">
        <div class="project-img">
          <?php if ($proj['main_image']): ?>
          <img src="<?= SITE_URL ?>/<?= e($proj['main_image']) ?>" alt="<?= e($proj['name']) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center"><i class="fas fa-laptop-code" style="font-size:2.5rem;color:rgba(255,255,255,0.4)"></i></div>
          <?php endif; ?>
        </div>
        <div class="project-body">
          <div class="project-cat"><?= e($proj['category']) ?></div>
          <h3><?= e($proj['name']) ?></h3>
          <p><?= e($proj['short_description']) ?></p>
          <?php if ($proj['technologies']): $techs = json_decode($proj['technologies'], true); ?>
          <div class="card-tags"><?php foreach (array_slice($techs,0,4) as $t): ?><span class="tag"><?= e($t) ?></span><?php endforeach; ?></div>
          <?php endif; ?>
          <a href="<?= SITE_URL ?>/projects/<?= e($proj['slug']) ?>" class="btn btn-outline btn-sm" style="margin-top:16px">View Project</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:80px 0;color:var(--text-light)">
      <i class="fas fa-folder-open" style="font-size:3rem;margin-bottom:16px;display:block;opacity:0.3"></i>
      <p>Projects will be added soon. Check back later.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('#projectsGrid .project-card').forEach(card => {
      card.style.display = (filter === 'All' || card.dataset.category === filter) ? '' : 'none';
    });
  });
});
document.querySelectorAll('.filter-btn.active').forEach(b => { b.style.background='var(--primary)';b.style.color='#fff';b.style.borderColor='var(--primary)'; });
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => { b.style.background='#fff';b.style.color='var(--text)';b.style.borderColor='var(--border)'; });
    this.style.background='var(--primary)';this.style.color='#fff';this.style.borderColor='var(--primary)';
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>

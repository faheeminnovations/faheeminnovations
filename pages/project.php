<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: ' . SITE_URL . '/projects'); exit; }

$stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = ? AND status = 1");
$stmt->execute([$slug]);
$project = $stmt->fetch();
if (!$project) {
    http_response_code(404);
    $pageTitle = '404 — Project Not Found | Faheem Innovations';
    $pageDesc  = '';
    require_once '../includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Project Not Found</h1><p>The project you are looking for does not exist or has been removed.</p><a href="' . SITE_URL . '/projects" class="btn btn-primary" style="margin-top:20px">View All Projects</a></div></section>';
    require_once '../includes/footer.php';
    exit;
}

$technologies = json_decode($project['technologies'] ?? '[]', true);
$gallery      = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order");
$gallery->execute([$project['id']]);
$gallery = $gallery->fetchAll();

// Related projects (same category, exclude current)
$related = $pdo->prepare("SELECT * FROM projects WHERE status=1 AND category=? AND id!=? ORDER BY sort_order LIMIT 3");
$related->execute([$project['category'], $project['id']]);
$related = $related->fetchAll();

$pageTitle = $project['meta_title'] ?: ($project['name'] . ' | Faheem Innovations');
$pageDesc  = $project['meta_description'] ?: $project['short_description'];
require_once '../includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span>/</span>
      <a href="<?= SITE_URL ?>/projects">Projects</a>
      <span>/</span>
      <span><?= e($project['name']) ?></span>
    </div>
    <div class="project-cat" style="justify-content:center;margin-bottom:12px"><?= e($project['category']) ?></div>
    <h1><?= e($project['name']) ?></h1>
    <?php if ($project['short_description']): ?>
    <p><?= e($project['short_description']) ?></p>
    <?php endif; ?>
  </div>
</section>

<!-- Main Content -->
<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 300px;gap:48px;align-items:start">

      <!-- Left: Main image + description -->
      <div>
        <!-- Main Image -->
        <?php if ($project['main_image']): ?>
        <div style="border-radius:var(--radius);overflow:hidden;margin-bottom:36px;box-shadow:var(--shadow-lg)">
          <img src="<?= SITE_URL ?>/<?= e($project['main_image']) ?>" alt="<?= e($project['name']) ?>" style="width:100%;height:420px;object-fit:cover">
        </div>
        <?php endif; ?>

        <!-- Description -->
        <?php if ($project['full_description']): ?>
        <div style="margin-bottom:36px">
          <h2 style="font-size:1.4rem;margin-bottom:16px">About This Project</h2>
          <div style="color:var(--text-light);line-height:1.8;font-size:1rem"><?= nl2br(e($project['full_description'])) ?></div>
        </div>
        <?php endif; ?>

        <!-- Gallery -->
        <?php if ($gallery): ?>
        <div style="margin-bottom:36px">
          <h3 style="margin-bottom:20px">Project Gallery</h3>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
            <?php foreach ($gallery as $img): ?>
            <a href="<?= SITE_URL ?>/<?= e($img['image']) ?>" target="_blank" style="display:block;border-radius:8px;overflow:hidden;aspect-ratio:4/3">
              <img src="<?= SITE_URL ?>/<?= e($img['image']) ?>" alt="<?= e($img['alt_text'] ?: $project['name']) ?>" style="width:100%;height:100%;object-fit:cover;transition:0.3s" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Technologies -->
        <?php if ($technologies): ?>
        <div>
          <h3 style="margin-bottom:16px">Technologies Used</h3>
          <div class="card-tags">
            <?php foreach ($technologies as $tech): ?>
            <span class="tag" style="font-size:0.85rem;padding:6px 14px"><?= e($tech) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right: Project meta sidebar -->
      <div style="position:sticky;top:90px">
        <div class="card" style="padding:28px;margin-bottom:20px">
          <h3 style="margin-bottom:20px;font-size:1rem">Project Details</h3>

          <?php if ($project['client']): ?>
          <div style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)">
            <div style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px">Client</div>
            <div style="font-weight:600"><?= e($project['client']) ?></div>
          </div>
          <?php endif; ?>

          <?php if ($project['category']): ?>
          <div style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)">
            <div style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px">Category</div>
            <div><?= e($project['category']) ?></div>
          </div>
          <?php endif; ?>

          <?php if ($project['completion_date']): ?>
          <div style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)">
            <div style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px">Completed</div>
            <div><?= date('F Y', strtotime($project['completion_date'])) ?></div>
          </div>
          <?php endif; ?>

          <?php if ($technologies): ?>
          <div style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border)">
            <div style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px">Technologies</div>
            <div style="display:flex;flex-wrap:wrap;gap:6px">
              <?php foreach ($technologies as $tech): ?>
              <span class="tag" style="font-size:0.75rem"><?= e($tech) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($project['project_url']): ?>
          <a href="<?= e($project['project_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary" style="width:100%;justify-content:center;margin-bottom:12px">
            <i class="fas fa-external-link-alt"></i> View Live Project
          </a>
          <?php endif; ?>

          <a href="<?= SITE_URL ?>/contact" class="btn btn-outline" style="width:100%;justify-content:center">
            <i class="fas fa-envelope"></i> Discuss a Similar Project
          </a>
        </div>

        <!-- Share -->
        <div class="card" style="padding:20px">
          <h4 style="font-size:0.9rem;margin-bottom:12px">Share This Project</h4>
          <div style="display:flex;gap:8px">
            <?php
            $shareUrl   = urlencode(SITE_URL . '/projects/' . $project['slug']);
            $shareTitle = urlencode($project['name'] . ' | Faheem Innovations');
            ?>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $shareUrl ?>&title=<?= $shareTitle ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm" style="flex:1;justify-content:center"><i class="fab fa-linkedin-in"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm" style="flex:1;justify-content:center"><i class="fab fa-x-twitter"></i></a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm" style="flex:1;justify-content:center"><i class="fab fa-facebook-f"></i></a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Related Projects -->
<?php if ($related): ?>
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <span class="section-badge">More Work</span>
      <h2 class="section-heading">Related Projects</h2>
    </div>
    <div class="cards-grid" style="margin-top:40px">
      <?php foreach ($related as $rp): ?>
      <div class="card project-card">
        <div class="project-img">
          <?php if ($rp['main_image']): ?>
          <img src="<?= SITE_URL ?>/<?= e($rp['main_image']) ?>" alt="<?= e($rp['name']) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center"><i class="fas fa-laptop-code" style="font-size:2.5rem;color:rgba(255,255,255,0.35)"></i></div>
          <?php endif; ?>
        </div>
        <div class="project-body">
          <div class="project-cat"><?= e($rp['category']) ?></div>
          <h3><?= e($rp['name']) ?></h3>
          <p><?= e($rp['short_description']) ?></p>
          <a href="<?= SITE_URL ?>/projects/<?= e($rp['slug']) ?>" class="btn btn-outline btn-sm" style="margin-top:16px">View Project</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2>Have a Similar Project in Mind?</h2>
    <p>Let's discuss your requirements and how we can build the right solution for your business.</p>
    <div class="cta-actions">
      <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-light btn-lg">Start a Conversation <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php require_once '../includes/footer.php'; ?>

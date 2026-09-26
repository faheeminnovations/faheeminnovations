<?php
$pageTitle = 'AI Tools - Practical AI for Everyday Work | Faheem Innovations';
$pageDesc = 'Simple AI-powered tools designed to help users process content, create media and complete common digital tasks.';
require_once 'includes/header.php';
require_once 'includes/user-auth.php';
$tools = getAITools();
$categories = [];
foreach ($tools as $t) { if (!in_array($t['category'], $categories)) $categories[] = $t['category']; }
$loggedIn = isUserLoggedIn();
$userCredits = $loggedIn ? getUserCredits($_SESSION['tool_user_id']) : 0;
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= SITE_URL ?>/">Home</a><span>/</span><span>AI Tools</span></div>
    <h1>AI Tools Built for Everyday Digital Work.</h1>
    <p>Simple AI-powered tools designed to help users process content, create media and complete common digital tasks.</p>
    <?php if ($loggedIn): ?>
    <div style="margin-top:16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
      <span style="background:rgba(11,60,51,.1);border:1px solid rgba(11,60,51,.2);color:var(--primary);padding:6px 16px;border-radius:50px;font-size:.85rem;font-weight:600">
        <i class="fas fa-coins" style="color:#f59e0b"></i> <?= number_format($userCredits) ?> Credits Available
      </span>
      <a href="<?= SITE_URL ?>/user/dashboard.php" style="color:var(--primary);font-size:.85rem;text-decoration:none;font-weight:500"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
    </div>
    <?php else: ?>
    <div style="margin-top:16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
      <a href="<?= SITE_URL ?>/user/register.php" class="btn btn-primary">Create Free Account — 30 Credits <i class="fas fa-arrow-right"></i></a>
      <a href="<?= SITE_URL ?>/user/login.php" style="color:var(--primary);font-size:.88rem;text-decoration:none;font-weight:500">Already have an account? Sign in</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php foreach ($categories as $cat):
      $catTools = array_filter($tools, fn($t) => $t['category'] === $cat);
    ?>
    <div style="margin-bottom:56px">
      <h2 style="margin-bottom:32px;padding-bottom:12px;border-bottom:2px solid var(--border)"><?= e($cat) ?></h2>
      <div class="cards-grid">
        <?php foreach ($catTools as $tool):
          $features = json_decode($tool['features'] ?? '[]', true);
        ?>
        <div class="card tool-card">
          <span class="tool-badge <?= $tool['is_free'] ? 'free' : 'paid' ?>"><?= $tool['is_free'] ? 'Free' : ($tool['price'] ? 'PKR ' . number_format($tool['price'], 0) : 'Paid') ?></span>
          <div class="card-icon"><i class="<?= e($tool['icon']) ?>"></i></div>
          <h3><?= e($tool['name']) ?></h3>
          <p><?= e($tool['description']) ?></p>
          <?php if ($features): ?>
          <ul style="margin-top:12px;padding-left:0">
            <?php foreach ($features as $f): ?>
            <li style="display:flex;align-items:center;gap:8px;font-size:0.85rem;color:var(--text-light);margin-bottom:4px"><i class="fas fa-check" style="color:var(--primary);font-size:0.7rem"></i><?= e($f) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <?php if ($tool['tool_url']): ?>
          <?php if ($loggedIn): ?>
          <a href="<?= e($tool['tool_url']) ?>" class="btn btn-primary btn-sm" style="margin-top:16px;width:100%;justify-content:center"><?= e($tool['button_text']) ?></a>
          <?php else: ?>
          <a href="<?= SITE_URL ?>/user/login.php?redirect=<?= urlencode($tool['tool_url']) ?>" class="btn btn-primary btn-sm" style="margin-top:16px;width:100%;justify-content:center"><i class="fas fa-lock"></i> Sign In to Use</a>
          <?php endif; ?>
          <?php else: ?>
          <span class="btn btn-outline btn-sm" style="margin-top:16px;width:100%;justify-content:center;opacity:0.6;cursor:default">Coming Soon</span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

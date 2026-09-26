<?php
require_once __DIR__ . '/functions.php';
$siteName = getSetting('site_name', 'Faheem Innovations');
$siteLogo = getSetting('site_logo', 'images/main-logo.svg');
$primaryColor = getSetting('primary_color', '#0B3C33');
$allMenuItems = getMenuItems();
// Separate top-level and sub-items
$menuItems   = array_filter($allMenuItems, fn($i) => (int)$i['parent_id'] === 0);
$subItems    = [];
foreach ($allMenuItems as $i) {
    if ((int)$i['parent_id'] > 0) $subItems[(int)$i['parent_id']][] = $i;
}
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? $siteName) ?></title>
<meta name="description" content="<?= e($pageDesc ?? '') ?>">
<?php if (!empty($pageKeywords)): ?><meta name="keywords" content="<?= e($pageKeywords) ?>"><?php endif; ?>
<meta property="og:title" content="<?= e($pageTitle ?? $siteName) ?>">
<meta property="og:description" content="<?= e($pageDesc ?? '') ?>">
<meta property="og:type" content="website">
<link rel="icon" href="<?= SITE_URL ?>/<?= e(getSetting('site_favicon','images/main-logo.png')) ?>" type="image/png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/style.css">
<style>:root{--primary:<?= e($primaryColor) ?>;--primary-dark:#072e27;--primary-light:#1a6b5a;}</style>
<?php if (getSetting('google_analytics')): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(getSetting('google_analytics')) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= e(getSetting('google_analytics')) ?>');</script>
<?php endif; ?>
</head>
<body>

<header class="site-header" id="siteHeader">
  <div class="container">
    <div class="header-inner">
      <a href="<?= SITE_URL ?>/" class="logo">
        <img src="<?= SITE_URL ?>/<?= e($siteLogo) ?>" alt="<?= e($siteName) ?>" height="45">
      </a>
      <nav class="main-nav" id="mainNav">
        <ul>
          <?php foreach ($menuItems as $item):
            $id       = (int)$item['id'];
            $children = $subItems[$id] ?? [];
            $hasDrop  = count($children) > 0;
          ?>
          <li class="<?= $hasDrop ? 'has-dropdown' : '' ?>">
            <a href="<?= e($item['url']) ?>" target="<?= e($item['target']) ?>"
               class="<?= isActive($item['url']) ?><?= $hasDrop ? ' dropdown-toggle' : '' ?>">
              <?= e($item['label']) ?>
              <?php if ($hasDrop): ?><i class="fas fa-chevron-down drop-arrow"></i><?php endif; ?>
            </a>
            <?php if ($hasDrop): ?>
            <ul class="dropdown-menu">
              <?php foreach ($children as $child): ?>
              <li><a href="<?= e($child['url']) ?>" target="<?= e($child['target']) ?>" class="<?= isActive($child['url']) ?>"><?= e($child['label']) ?></a></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <div class="header-actions">
        <a href="<?= SITE_URL ?>/contact" class="btn btn-primary">Get Started</a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </div>
</header>

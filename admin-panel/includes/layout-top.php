<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($adminTitle ?? 'Admin Panel') ?> - Faheem Innovations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/admin.css">
</head>
<body>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
      <img src="<?= SITE_URL ?>/<?= getSetting('site_logo','images/main-logo.svg') ?>" alt="Faheem Innovations" height="36" style="filter:brightness(0) invert(1)">
      <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    </div>
    <nav class="sidebar-nav">
      <?php
      $user = adminUser();
      $nav = [
        ['icon'=>'fas fa-tachometer-alt','label'=>'Dashboard','url'=>'index.php'],
        ['icon'=>'fas fa-file-alt','label'=>'Pages','url'=>'pages/index.php'],
        ['icon'=>'fas fa-cogs','label'=>'Services','url'=>'services/index.php'],
        ['icon'=>'fas fa-robot','label'=>'AI Tools','url'=>'ai-tools/index.php'],
        ['icon'=>'fas fa-box','label'=>'Our Products','url'=>'products/index.php'],
        ['icon'=>'fas fa-briefcase','label'=>'Projects','url'=>'projects/index.php'],
        ['icon'=>'fas fa-list-ol','label'=>'Process','url'=>'process/index.php'],
        ['icon'=>'fas fa-star','label'=>'Testimonials','url'=>'testimonials/index.php'],
        ['icon'=>'fas fa-building','label'=>'Our Clients','url'=>'clients/index.php'],
        ['icon'=>'fas fa-question-circle','label'=>'FAQs','url'=>'faqs/index.php'],
        ['icon'=>'fas fa-users','label'=>'Tool Users','url'=>'tool-users/index.php'],
        ['icon'=>'fas fa-envelope','label'=>'Enquiries','url'=>'enquiries/index.php'],
        ['icon'=>'fas fa-images','label'=>'Media Library','url'=>'media/index.php'],
        ['icon'=>'fas fa-bars','label'=>'Navigation','url'=>'navigation/index.php'],
        ['icon'=>'fas fa-chart-bar','label'=>'Statistics','url'=>'statistics/index.php'],
        ['icon'=>'fas fa-search','label'=>'SEO','url'=>'seo/index.php'],
        ['icon'=>'fas fa-users','label'=>'Users','url'=>'users/index.php','role'=>'super_admin'],
        ['icon'=>'fas fa-sliders-h','label'=>'Settings','url'=>'settings/index.php'],
      ];
      $roles = ['editor'=>1,'admin'=>2,'super_admin'=>3];
      $userRoleLevel = $roles[$user['role']] ?? 1;
      foreach ($nav as $item):
        $minRole = $item['role'] ?? 'editor';
        if ($userRoleLevel < ($roles[$minRole] ?? 1)) continue;
        $current = basename($_SERVER['PHP_SELF']);
        $isActive = strpos($_SERVER['PHP_SELF'], str_replace('index.php','',$item['url'])) !== false;
      ?>
      <a href="<?= ADMIN_URL ?>/<?= $item['url'] ?>" class="nav-item <?= $isActive ? 'active' : '' ?>">
        <i class="<?= $item['icon'] ?>"></i>
        <span><?= $item['label'] ?></span>
      </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <a href="<?= SITE_URL ?>/" target="_blank" class="nav-item"><i class="fas fa-external-link-alt"></i><span>View Website</span></a>
      <a href="<?= ADMIN_URL ?>/logout.php" class="nav-item nav-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </aside>

  <!-- Main -->
  <div class="admin-main">
    <header class="admin-header">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
      <h1 class="admin-page-title"><?= htmlspecialchars($adminTitle ?? '') ?></h1>
      <div class="admin-header-right">
        <span class="admin-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($user['name']) ?></span>
      </div>
    </header>
    <div class="admin-content">

<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Pages';

if (isset($_GET['toggle'])) { handleStatusToggle('pages', $_GET['toggle']); redirect(ADMIN_URL.'/pages/index.php'); }

$pages = $pdo->query("SELECT * FROM pages ORDER BY id")->fetchAll();
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:14px 16px;font-size:0.85rem;color:#1e40af;margin-bottom:20px">
  <i class="fas fa-info-circle" style="margin-right:6px"></i>
  These are the core website pages. Click <strong>Edit SEO</strong> to manage meta titles and descriptions. Click <strong>Edit Content</strong> to manage page-level settings.
</div>
<div class="admin-table-wrap">
  <div class="table-header"><h3>Website Pages (<?= count($pages) ?>)</h3></div>
  <table>
    <thead><tr><th>#</th><th>Page</th><th>Slug / URL</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($pages as $pg): ?>
      <tr>
        <td><?= $pg['id'] ?></td>
        <td><strong><?= e($pg['title']) ?></strong><?php if ($pg['meta_title']): ?><br><small style="color:var(--text-light)"><?= e(substr($pg['meta_title'],0,50)) ?></small><?php endif; ?></td>
        <td>
          <code>/<?= e($pg['slug'] === 'home' ? '' : $pg['slug']) ?></code>
          <a href="<?= SITE_URL ?>/<?= $pg['slug'] === 'home' ? '' : $pg['slug'] ?>" target="_blank" style="color:var(--primary);margin-left:6px;font-size:0.8rem"><i class="fas fa-external-link-alt"></i></a>
        </td>
        <td><span class="badge badge-<?= $pg['status'] ? 'success' : 'danger' ?>"><?= $pg['status'] ? 'Published' : 'Draft' ?></span></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $pg['id'] ?>" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i> Edit</a>
          <a href="../seo/index.php?page=<?= e($pg['slug']) ?>" class="btn btn-outline btn-sm"><i class="fas fa-search"></i> SEO</a>
          <a href="?toggle=<?= $pg['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $pg['status'] ? '' : '-slash' ?>"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>

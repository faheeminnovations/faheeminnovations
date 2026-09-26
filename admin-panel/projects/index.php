<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Projects';

if (isset($_GET['delete'])) { handleDelete('projects', $_GET['delete']); flashMessage('success','Project deleted.'); redirect(ADMIN_URL.'/projects/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('projects', $_GET['toggle']); redirect(ADMIN_URL.'/projects/index.php'); }

$search = trim($_GET['search'] ?? '');
$where  = '1'; $params = [];
if ($search) { $where = 'name LIKE ? OR category LIKE ?'; $params = ["%$search%", "%$search%"]; }

$page   = max(1, (int)($_GET['page'] ?? 1));
$result = getPaginatedResults('projects', $page, 15, $where, $params, 'sort_order ASC, created_at DESC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center">
  <form method="GET" style="display:flex;gap:8px">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" name="search" placeholder="Search projects..." value="<?= e($search) ?>"></div>
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
    <?php if ($search): ?><a href="index.php" class="btn btn-outline btn-sm">Reset</a><?php endif; ?>
  </form>
  <a href="add.php" class="btn btn-primary btn-sm" style="margin-left:auto"><i class="fas fa-plus"></i> Add Project</a>
</div>

<div class="admin-table-wrap">
  <div class="table-header"><h3>Projects (<?= $result['total'] ?>)</h3></div>
  <table>
    <thead><tr><th>#</th><th>Image</th><th>Name</th><th>Category</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td>
          <?php if ($row['main_image']): ?>
          <img src="<?= SITE_URL ?>/<?= e($row['main_image']) ?>" style="width:50px;height:36px;object-fit:cover;border-radius:4px">
          <?php else: ?>
          <div style="width:50px;height:36px;background:var(--bg);border:1px solid var(--border);border-radius:4px;display:flex;align-items:center;justify-content:center"><i class="fas fa-image" style="color:var(--text-light);font-size:0.75rem"></i></div>
          <?php endif; ?>
        </td>
        <td><strong><?= e($row['name']) ?></strong><br><small style="color:var(--text-light)"><?= e($row['client']) ?></small></td>
        <td><?= e($row['category']) ?></td>
        <td><span class="badge badge-<?= $row['is_featured'] ? 'warning' : 'primary' ?>"><?= $row['is_featured'] ? 'Featured' : 'Normal' ?></span></td>
        <td><span class="badge badge-<?= $row['status'] ? 'success' : 'danger' ?>"><?= $row['status'] ? 'Published' : 'Draft' ?></span></td>
        <td class="table-actions">
          <a href="<?= SITE_URL ?>/projects/<?= e($row['slug']) ?>" target="_blank" class="btn btn-outline btn-sm btn-icon" title="View"><i class="fas fa-external-link-alt"></i></a>
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $row['status'] ? '' : '-slash' ?>"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this project? This cannot be undone."><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$result['data']): ?>
      <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-light)"><i class="fas fa-folder-open" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.3"></i>No projects found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php paginationLinks($result, '?search=' . urlencode($search)); ?>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>

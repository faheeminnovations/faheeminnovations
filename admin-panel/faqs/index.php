<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'FAQs';
if (isset($_GET['delete'])) { handleDelete('faqs', $_GET['delete']); flashMessage('success','FAQ deleted.'); redirect(ADMIN_URL.'/faqs/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('faqs', $_GET['toggle']); redirect(ADMIN_URL.'/faqs/index.php'); }
$page = max(1,(int)($_GET['page']??1));
$result = getPaginatedResults('faqs',$page,20,'1',[],'sort_order ASC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-table-wrap">
  <div class="table-header">
    <h3>FAQs (<?= $result['total'] ?>)</h3>
    <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add FAQ</a>
  </div>
  <table>
    <thead><tr><th>#</th><th>Question</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= e(substr($row['question'],0,80)) ?>...</td>
        <td><span class="badge badge-<?= $row['status']?'success':'danger' ?>"><?= $row['status']?'Active':'Hidden' ?></span></td>
        <td><?= $row['sort_order'] ?></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $row['status']?'':'-slash' ?>"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this FAQ?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php paginationLinks($result,'?'); ?>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>

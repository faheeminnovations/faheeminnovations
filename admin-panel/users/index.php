<?php
require_once '../includes/auth.php';
requireRole('super_admin');
require_once '../includes/crud.php';
$adminTitle = 'Users';

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    if ($did === (int)$_SESSION['admin_id']) { flashMessage('danger','You cannot delete your own account.'); redirect(ADMIN_URL.'/users/index.php'); }
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$did]);
    flashMessage('success','User deleted.');
    redirect(ADMIN_URL.'/users/index.php');
}
if (isset($_GET['toggle'])) { handleStatusToggle('users', $_GET['toggle']); redirect(ADMIN_URL.'/users/index.php'); }

$result = getPaginatedResults('users', 1, 20, '1', [], 'created_at DESC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div style="display:flex;justify-content:flex-end;margin-bottom:16px">
  <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add User</a>
</div>
<div class="admin-table-wrap">
  <div class="table-header"><h3>Admin Users (<?= $result['total'] ?>)</h3></div>
  <table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $u):
        $stmt = $pdo->prepare("SELECT name FROM roles WHERE id=?"); $stmt->execute([$u['role_id']]); $roleName = $stmt->fetchColumn() ?: 'editor';
      ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem"><?= strtoupper(substr($u['name'],0,1)) ?></div>
            <strong><?= e($u['name']) ?></strong><?= $u['id']==$_SESSION['admin_id']?' <span class="badge badge-primary" style="font-size:0.7rem">You</span>':'' ?>
          </div>
        </td>
        <td><?= e($u['email']) ?></td>
        <td>
          <?php $roleColors = ['super_admin'=>'danger','admin'=>'warning','editor'=>'info']; ?>
          <span class="badge badge-<?= $roleColors[$roleName] ?? 'info' ?>"><?= ucfirst(str_replace('_',' ',$roleName)) ?></span>
        </td>
        <td><?= $u['last_login'] ? date('M d, Y', strtotime($u['last_login'])) : '<span style="color:var(--text-light)">Never</span>' ?></td>
        <td><span class="badge badge-<?= $u['status'] ? 'success' : 'danger' ?>"><?= $u['status'] ? 'Active' : 'Inactive' ?></span></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <?php if ($u['id'] != $_SESSION['admin_id']): ?>
          <a href="?toggle=<?= $u['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-power-off"></i></a>
          <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete user '<?= e($u['name']) ?>'?"><i class="fas fa-trash"></i></a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>

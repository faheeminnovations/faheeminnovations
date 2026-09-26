<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Enquiries';

if (isset($_GET['delete'])) { handleDelete('enquiries',$_GET['delete']); flashMessage('success','Enquiry deleted.'); redirect(ADMIN_URL.'/enquiries/index.php'); }

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])) {
    csrf_verify();
    $pdo->prepare("UPDATE enquiries SET status=? WHERE id=?")->execute([$_POST['status'],(int)$_POST['id']]);
    if (!empty($_POST['note'])) {
        $pdo->prepare("INSERT INTO enquiry_notes (enquiry_id,note,added_by) VALUES (?,?,?)")->execute([(int)$_POST['id'],trim($_POST['note']),$_SESSION['admin_id']]);
    }
    flashMessage('success','Enquiry updated.');
    redirect(ADMIN_URL.'/enquiries/index.php');
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$where = '1'; $params = [];
if ($search) { $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)"; $s = "%$search%"; $params = array_merge($params,[$s,$s,$s]); }
if ($status) { $where .= " AND status=?"; $params[] = $status; }

$page = max(1,(int)($_GET['page']??1));
$result = getPaginatedResults('enquiries',$page,15,$where,$params,'created_at DESC');

// Export CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="enquiries-'.date('Y-m-d').'.csv"');
    $out = fopen('php://output','w');
    fputcsv($out,['ID','Name','Email','Phone','Company','Subject','Message','Status','Date']);
    $all = $pdo->prepare("SELECT * FROM enquiries WHERE $where ORDER BY created_at DESC"); $all->execute($params);
    foreach ($all->fetchAll() as $r) fputcsv($out,[$r['id'],$r['name'],$r['email'],$r['phone'],$r['company'],$r['subject'],$r['message'],$r['status'],$r['created_at']]);
    fclose($out); exit;
}

require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center">
  <form method="GET" style="display:flex;gap:8px;flex:1;flex-wrap:wrap">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" name="search" placeholder="Search enquiries..." value="<?= e($search) ?>"></div>
    <select name="status" style="padding:8px 12px;border:1px solid var(--border);border-radius:7px;font-family:var(--font);font-size:0.88rem">
      <option value="">All Status</option>
      <?php foreach (['new','contacted','in_progress','converted','closed'] as $s): ?>
      <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="index.php" class="btn btn-outline btn-sm">Reset</a>
  </form>
  <a href="?export=1&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export CSV</a>
</div>

<div class="admin-table-wrap">
  <div class="table-header"><h3>Enquiries (<?= $result['total'] ?>)</h3></div>
  <table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= e($row['name']) ?></td>
        <td><a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a></td>
        <td><?= e(substr($row['subject'],0,40)) ?></td>
        <td><span class="badge badge-<?= $row['status']==='new'?'danger':($row['status']==='converted'?'success':'info') ?>"><?= ucfirst(str_replace('_',' ',$row['status'])) ?></span></td>
        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
        <td class="table-actions">
          <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-eye"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this enquiry?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php paginationLinks($result,'?search='.urlencode($search).'&status='.urlencode($status)); ?>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>

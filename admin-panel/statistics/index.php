<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Statistics';

if (isset($_GET['delete'])) { handleDelete('statistics', $_GET['delete']); flashMessage('success','Stat deleted.'); redirect(ADMIN_URL.'/statistics/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    if (isset($_POST['add_stat'])) {
        $pdo->prepare("INSERT INTO statistics (value,label,sort_order) VALUES (?,?,?)")
            ->execute([trim($_POST['value']), trim($_POST['label']), (int)$_POST['sort_order']]);
        flashMessage('success', 'Statistic added.');
    } elseif (isset($_POST['update_stat'])) {
        foreach ($_POST['stat_id'] as $i => $sid) {
            $pdo->prepare("UPDATE statistics SET value=?,label=?,sort_order=? WHERE id=?")
                ->execute([trim($_POST['stat_value'][$i]), trim($_POST['stat_label'][$i]), (int)$_POST['stat_order'][$i], (int)$sid]);
        }
        flashMessage('success', 'Statistics updated.');
    }
    redirect(ADMIN_URL . '/statistics/index.php');
}

$stats = $pdo->query("SELECT * FROM statistics ORDER BY sort_order")->fetchAll();
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px 16px;font-size:0.85rem;color:#92400e;margin-bottom:20px">
  <i class="fas fa-info-circle" style="margin-right:6px"></i>
  These numbers appear in the statistics bar on the homepage. E.g. "50+", "30+". Changes are reflected immediately on the live website.
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px">

  <!-- Edit existing stats -->
  <div class="admin-table-wrap">
    <div class="table-header"><h3>Current Statistics</h3></div>
    <div style="padding:20px">
      <?php if ($stats): ?>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="update_stat" value="1">
        <?php foreach ($stats as $stat): ?>
        <input type="hidden" name="stat_id[]" value="<?= $stat['id'] ?>">
        <div style="display:grid;grid-template-columns:1fr 2fr 80px 40px;gap:12px;align-items:end;margin-bottom:12px">
          <div class="form-group" style="margin:0">
            <label style="font-size:0.78rem">Value</label>
            <input type="text" name="stat_value[]" value="<?= e($stat['value']) ?>" placeholder="50+" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-family:var(--font);width:100%;font-size:0.9rem;font-weight:700">
          </div>
          <div class="form-group" style="margin:0">
            <label style="font-size:0.78rem">Label</label>
            <input type="text" name="stat_label[]" value="<?= e($stat['label']) ?>" placeholder="Projects Completed" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-family:var(--font);width:100%;font-size:0.9rem">
          </div>
          <div class="form-group" style="margin:0">
            <label style="font-size:0.78rem">Order</label>
            <input type="number" name="stat_order[]" value="<?= $stat['sort_order'] ?>" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-family:var(--font);width:100%;font-size:0.9rem">
          </div>
          <a href="?delete=<?= $stat['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this stat?" style="margin-bottom:1px"><i class="fas fa-trash"></i></a>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary" style="margin-top:8px"><i class="fas fa-save"></i> Save All Changes</button>
      </form>
      <?php else: ?>
      <p style="color:var(--text-light);text-align:center;padding:24px">No statistics added yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Add new stat -->
  <div>
    <div class="admin-table-wrap">
      <div class="table-header"><h3>Add Statistic</h3></div>
      <div style="padding:20px">
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="add_stat" value="1">
          <div class="form-group">
            <label>Value *</label>
            <input type="text" name="value" required placeholder="50+ or 100 or 5">
            <span class="form-hint">The large number shown (e.g. "50+")</span>
          </div>
          <div class="form-group">
            <label>Label *</label>
            <input type="text" name="label" required placeholder="Projects Completed">
            <span class="form-hint">The small text below the number</span>
          </div>
          <div class="form-group">
            <label>Sort Order</label>
            <input type="number" name="sort_order" value="<?= count($stats) + 1 ?>">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-plus"></i> Add Stat</button>
        </form>
      </div>
    </div>

    <!-- Preview -->
    <div style="margin-top:16px;background:var(--primary);border-radius:10px;padding:20px">
      <p style="color:rgba(255,255,255,0.6);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px">HOMEPAGE PREVIEW</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <?php foreach ($stats as $s): ?>
        <div style="text-align:center">
          <div style="font-size:1.4rem;font-weight:800;color:#fff"><?= e($s['value']) ?></div>
          <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:2px"><?= e($s['label']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>
<?php require_once '../includes/layout-bottom.php'; ?>

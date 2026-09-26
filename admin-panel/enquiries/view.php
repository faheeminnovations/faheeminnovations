<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'View Enquiry';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM enquiries WHERE id = ?");
$stmt->execute([$id]);
$enquiry = $stmt->fetch();
if (!$enquiry) { flashMessage('danger', 'Enquiry not found.'); redirect(ADMIN_URL . '/enquiries/index.php'); }

// Mark as read if new
if ($enquiry['status'] === 'new') {
    $pdo->prepare("UPDATE enquiries SET status='contacted' WHERE id=?")->execute([$id]);
    $enquiry['status'] = 'contacted';
}

// Handle status/note update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    if (isset($_POST['update_status'])) {
        $pdo->prepare("UPDATE enquiries SET status=? WHERE id=?")->execute([$_POST['status'], $id]);
        if (!empty(trim($_POST['note'] ?? ''))) {
            $pdo->prepare("INSERT INTO enquiry_notes (enquiry_id,note,added_by) VALUES (?,?,?)")
                ->execute([$id, trim($_POST['note']), $_SESSION['admin_id']]);
        }
        flashMessage('success', 'Enquiry updated.');
        redirect(ADMIN_URL . '/enquiries/view.php?id=' . $id);
    }
}

$notes = $pdo->prepare("SELECT n.*,u.name as admin_name FROM enquiry_notes n LEFT JOIN users u ON n.added_by=u.id WHERE n.enquiry_id=? ORDER BY n.created_at ASC");
$notes->execute([$id]);
$notes = $notes->fetchAll();

$statusColors = ['new'=>'danger','contacted'=>'info','in_progress'=>'warning','converted'=>'success','closed'=>'primary'];
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="margin-bottom:20px;display:flex;gap:12px;align-items:center">
  <a href="index.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Enquiries</a>
  <span class="badge badge-<?= $statusColors[$enquiry['status']] ?? 'info' ?>" style="font-size:0.85rem;padding:6px 14px"><?= ucfirst(str_replace('_', ' ', $enquiry['status'])) ?></span>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px">

  <!-- Enquiry Details -->
  <div>
    <div class="admin-table-wrap" style="margin-bottom:24px">
      <div class="table-header"><h3>Enquiry #<?= $enquiry['id'] ?></h3><span style="color:var(--text-light);font-size:0.82rem"><?= date('F d, Y \a\t h:i A', strtotime($enquiry['created_at'])) ?></span></div>
      <div style="padding:24px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
          <div>
            <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em">Name</label>
            <p style="margin-top:4px;font-weight:600"><?= e($enquiry['name']) ?></p>
          </div>
          <div>
            <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em">Email</label>
            <p style="margin-top:4px"><a href="mailto:<?= e($enquiry['email']) ?>" style="color:var(--primary)"><?= e($enquiry['email']) ?></a></p>
          </div>
          <?php if ($enquiry['phone']): ?>
          <div>
            <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em">Phone</label>
            <p style="margin-top:4px"><a href="tel:<?= e($enquiry['phone']) ?>" style="color:var(--primary)"><?= e($enquiry['phone']) ?></a></p>
          </div>
          <?php endif; ?>
          <?php if ($enquiry['company']): ?>
          <div>
            <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em">Company</label>
            <p style="margin-top:4px"><?= e($enquiry['company']) ?></p>
          </div>
          <?php endif; ?>
        </div>
        <div style="margin-bottom:20px">
          <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em">Subject</label>
          <p style="margin-top:4px;font-weight:600"><?= e($enquiry['subject']) ?></p>
        </div>
        <div>
          <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em">Message</label>
          <div style="margin-top:8px;padding:20px;background:var(--bg);border-radius:8px;border:1px solid var(--border);line-height:1.7;color:var(--text)"><?= nl2br(e($enquiry['message'])) ?></div>
        </div>
        <div style="margin-top:20px;display:flex;gap:12px">
          <a href="mailto:<?= e($enquiry['email']) ?>" class="btn btn-primary btn-sm"><i class="fas fa-envelope"></i> Reply by Email</a>
          <?php if ($enquiry['phone']): ?>
          <a href="tel:<?= e($enquiry['phone']) ?>" class="btn btn-outline btn-sm"><i class="fas fa-phone"></i> Call</a>
          <a href="https://wa.me/<?= preg_replace('/\D/','',$enquiry['phone']) ?>" target="_blank" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i> WhatsApp</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Notes Timeline -->
    <div class="admin-table-wrap">
      <div class="table-header"><h3>Notes & Activity</h3></div>
      <div style="padding:20px">
        <?php if ($notes): ?>
        <div style="margin-bottom:20px">
          <?php foreach ($notes as $note): ?>
          <div style="display:flex;gap:12px;margin-bottom:16px">
            <div style="width:32px;height:32px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0"><?= strtoupper(substr($note['admin_name'] ?? 'A', 0, 1)) ?></div>
            <div style="flex:1">
              <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px 16px">
                <p style="margin-bottom:4px"><?= nl2br(e($note['note'])) ?></p>
                <small style="color:var(--text-light)"><?= e($note['admin_name'] ?? 'Admin') ?> &bull; <?= date('M d, Y h:i A', strtotime($note['created_at'])) ?></small>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="update_status" value="1">
          <div class="form-group" style="margin-bottom:12px">
            <textarea name="note" rows="3" placeholder="Add a note..." style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:var(--font);font-size:0.9rem;resize:vertical"></textarea>
          </div>
          <div style="display:flex;gap:10px;align-items:center">
            <select name="status" style="padding:8px 12px;border:1px solid var(--border);border-radius:7px;font-family:var(--font);font-size:0.88rem">
              <?php foreach (['new','contacted','in_progress','converted','closed'] as $s): ?>
              <option value="<?= $s ?>" <?= $enquiry['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div>
    <div class="admin-table-wrap" style="margin-bottom:16px">
      <div class="table-header"><h3>Quick Info</h3></div>
      <div style="padding:20px">
        <div style="margin-bottom:16px">
          <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase">Source</label>
          <p style="margin-top:4px"><?= e($enquiry['source']) ?></p>
        </div>
        <div style="margin-bottom:16px">
          <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase">Received</label>
          <p style="margin-top:4px"><?= date('M d, Y', strtotime($enquiry['created_at'])) ?></p>
        </div>
        <div>
          <label style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase">Status</label>
          <p style="margin-top:4px"><span class="badge badge-<?= $statusColors[$enquiry['status']] ?? 'info' ?>"><?= ucfirst(str_replace('_',' ',$enquiry['status'])) ?></span></p>
        </div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px">
      <a href="index.php?status=new" class="btn btn-outline btn-sm" style="justify-content:center"><i class="fas fa-list"></i> All New Enquiries</a>
      <a href="?delete=<?= $id ?>" class="btn btn-danger btn-sm" style="justify-content:center" data-confirm="Permanently delete this enquiry?"><i class="fas fa-trash"></i> Delete Enquiry</a>
    </div>
  </div>

</div>
<?php require_once '../includes/layout-bottom.php'; ?>

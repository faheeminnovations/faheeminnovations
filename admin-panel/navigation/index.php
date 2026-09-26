<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Navigation';

if (isset($_GET['delete'])) {
    handleDelete('menu_items', $_GET['delete']);
    flashMessage('success','Menu item deleted.');
    redirect(ADMIN_URL.'/navigation/index.php');
}
if (isset($_GET['toggle'])) {
    handleStatusToggle('menu_items', $_GET['toggle']);
    redirect(ADMIN_URL.'/navigation/index.php');
}

// Add new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    csrf_verify();
    $pdo->prepare("INSERT INTO menu_items (label,url,parent_id,target,status,sort_order) VALUES (?,?,?,?,?,?)")
        ->execute([
            trim($_POST['label']), trim($_POST['url']),
            (int)($_POST['parent_id'] ?? 0),
            $_POST['target'] === '_blank' ? '_blank' : '_self',
            (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order']
        ]);
    flashMessage('success', 'Menu item added.');
    redirect(ADMIN_URL . '/navigation/index.php');
}

// Update existing item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_item'])) {
    csrf_verify();
    $eid = (int)$_POST['edit_id'];
    $pdo->prepare("UPDATE menu_items SET label=?,url=?,target=?,status=?,sort_order=? WHERE id=?")
        ->execute([
            trim($_POST['label']), trim($_POST['url']),
            $_POST['target'] === '_blank' ? '_blank' : '_self',
            (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order'],
            $eid
        ]);
    flashMessage('success', 'Menu item updated.');
    redirect(ADMIN_URL . '/navigation/index.php');
}

$items = $pdo->query("SELECT * FROM menu_items ORDER BY sort_order ASC")->fetchAll();
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<style>
.nav-page-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) {
    .nav-page-grid { grid-template-columns: 1fr; }
}
.nav-table td, .nav-table th { white-space: nowrap; }
.nav-table td.url-cell { max-width: 180px; overflow: hidden; text-overflow: ellipsis; }

/* Modal */
.modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.modal-backdrop.open { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 460px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: modalIn 0.2s ease;
}
@keyframes modalIn {
    from { transform: translateY(-16px); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
}
.modal-head {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-head h3 { font-size: 1rem; font-weight: 700; }
.modal-close {
    background: none;
    border: none;
    font-size: 1.1rem;
    color: var(--text-light);
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: 0.2s;
}
.modal-close:hover { background: #f3f4f6; color: var(--text); }
.modal-body { padding: 24px; }
.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
</style>

<div class="nav-page-grid">

  <!-- ── Menu Items Table ── -->
  <div class="admin-table-wrap">
    <div class="table-header">
      <h3><i class="fas fa-bars" style="margin-right:8px;color:var(--primary)"></i> Menu Items (<?= count($items) ?>)</h3>
      <button class="btn btn-primary btn-sm" onclick="openAddPanel()">
        <i class="fas fa-plus"></i> Add Item
      </button>
    </div>

    <div style="overflow-x:auto">
      <table class="nav-table">
        <thead>
          <tr>
            <th style="width:60px">Order</th>
            <th>Label</th>
            <th>URL</th>
            <th>Target</th>
            <th>Status</th>
            <th style="width:90px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr id="row-<?= $item['id'] ?>">
            <td>
              <input type="number" value="<?= $item['sort_order'] ?>"
                style="width:54px;padding:5px 8px;border:1px solid var(--border);border-radius:6px;font-size:0.82rem;text-align:center"
                onchange="updateOrder(<?= $item['id'] ?>, this.value)"
                title="Change sort order">
            </td>
            <td><strong><?= e($item['label']) ?></strong></td>
            <td class="url-cell">
              <code style="font-size:0.8rem;background:#f3f4f6;padding:2px 6px;border-radius:4px" title="<?= e($item['url']) ?>">
                <?= e($item['url']) ?>
              </code>
            </td>
            <td>
              <?= $item['target'] === '_blank'
                ? '<span class="badge badge-info"><i class="fas fa-external-link-alt" style="font-size:0.65rem"></i> New Tab</span>'
                : '<span style="color:var(--text-light);font-size:0.82rem">Same Tab</span>' ?>
            </td>
            <td>
              <a href="?toggle=<?= $item['id'] ?>" title="Click to toggle" style="text-decoration:none">
                <span class="badge badge-<?= $item['status'] ? 'success' : 'danger' ?>" style="cursor:pointer">
                  <i class="fas fa-<?= $item['status'] ? 'check-circle' : 'times-circle' ?>"></i>
                  <?= $item['status'] ? 'Active' : 'Hidden' ?>
                </span>
              </a>
            </td>
            <td>
              <div class="table-actions">
                <button type="button"
                  class="btn btn-outline btn-sm btn-icon"
                  title="Edit"
                  onclick="openEdit(
                    <?= $item['id'] ?>,
                    '<?= e(addslashes($item['label'])) ?>',
                    '<?= e(addslashes($item['url'])) ?>',
                    '<?= $item['target'] ?>',
                    <?= $item['status'] ?>,
                    <?= $item['sort_order'] ?>
                  )">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="?delete=<?= $item['id'] ?>"
                   class="btn btn-danger btn-sm btn-icon"
                   title="Delete"
                   data-confirm="Delete '<?= e($item['label']) ?>'?">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div style="padding:12px 20px;border-top:1px solid var(--border);background:#fafafa;display:flex;align-items:center;gap:8px">
      <i class="fas fa-info-circle" style="color:var(--primary)"></i>
      <span style="font-size:0.8rem;color:var(--text-light)">Order numbers update instantly on change. All edits reflect on the live site immediately.</span>
    </div>
  </div>

  <!-- ── Add New Item Panel ── -->
  <div id="addPanel" class="admin-table-wrap">
    <div class="table-header">
      <h3><i class="fas fa-plus-circle" style="margin-right:8px;color:var(--primary)"></i> Add Menu Item</h3>
    </div>
    <div style="padding:20px">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="add_item" value="1">

        <div class="form-group">
          <label>Label <span style="color:var(--danger)">*</span></label>
          <input type="text" name="label" required placeholder="e.g. Home, About, Services">
        </div>

        <div class="form-group">
          <label>URL <span style="color:var(--danger)">*</span></label>
          <input type="text" name="url" required placeholder="/about  or  https://...">
          <div class="form-hint">Use relative paths like <code>/contact</code> or full URLs for external links.</div>
        </div>

        <div class="form-group">
          <label>Open In</label>
          <select name="target">
            <option value="_self">Same Tab</option>
            <option value="_blank">New Tab</option>
          </select>
        </div>

        <div class="two-col">
          <div class="form-group" style="margin-bottom:0">
            <label>Status</label>
            <select name="status">
              <option value="1">Active</option>
              <option value="0">Hidden</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Sort Order</label>
            <input type="number" name="sort_order" value="<?= count($items) + 1 ?>">
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:20px">
          <i class="fas fa-plus"></i> Add Menu Item
        </button>
      </form>
    </div>
  </div>

</div>

<!-- ── Edit Modal ── -->
<div id="editModal" class="modal-backdrop" onclick="closeEditOnBackdrop(event)">
  <div class="modal-box">
    <div class="modal-head">
      <h3><i class="fas fa-edit" style="margin-right:8px;color:var(--primary)"></i> Edit Menu Item</h3>
      <button class="modal-close" onclick="closeEdit()" title="Close">&times;</button>
    </div>
    <form method="POST">
      <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="edit_item" value="1">
        <input type="hidden" name="edit_id" id="editId">

        <div class="form-group">
          <label>Label <span style="color:var(--danger)">*</span></label>
          <input type="text" name="label" id="editLabel" required>
        </div>

        <div class="form-group">
          <label>URL <span style="color:var(--danger)">*</span></label>
          <input type="text" name="url" id="editUrl" required>
          <div class="form-hint">Use relative paths like <code>/contact</code> or full URLs for external links.</div>
        </div>

        <div class="form-group">
          <label>Open In</label>
          <select name="target" id="editTarget">
            <option value="_self">Same Tab</option>
            <option value="_blank">New Tab</option>
          </select>
        </div>

        <div class="two-col">
          <div class="form-group" style="margin-bottom:0">
            <label>Status</label>
            <select name="status" id="editStatus">
              <option value="1">Active</option>
              <option value="0">Hidden</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Sort Order</label>
            <input type="number" name="sort_order" id="editOrder">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeEdit()">
          <i class="fas fa-times"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Open edit modal with pre-filled data
function openEdit(id, label, url, target, status, order) {
  document.getElementById('editId').value    = id;
  document.getElementById('editLabel').value = label;
  document.getElementById('editUrl').value   = url;
  document.getElementById('editTarget').value  = target;
  document.getElementById('editStatus').value  = status;
  document.getElementById('editOrder').value   = order;
  document.getElementById('editModal').classList.add('open');
  document.getElementById('editLabel').focus();
}

function closeEdit() {
  document.getElementById('editModal').classList.remove('open');
}

// Close modal when clicking the dark backdrop
function closeEditOnBackdrop(e) {
  if (e.target === document.getElementById('editModal')) closeEdit();
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeEdit();
});

// Show add panel (already visible on desktop, scroll to it on mobile)
function openAddPanel() {
  const panel = document.getElementById('addPanel');
  panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  panel.querySelector('input[name="label"]').focus();
}

// Update sort order via AJAX without wiping other fields
function updateOrder(id, val) {
  const row   = document.getElementById('row-' + id);
  const label = row.querySelector('td:nth-child(2) strong').innerText.trim();
  const url   = row.querySelector('td:nth-child(3) code').innerText.trim();
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'csrf_token=<?= csrf_token() ?>'
        + '&edit_item=1'
        + '&edit_id='     + encodeURIComponent(id)
        + '&sort_order='  + encodeURIComponent(val)
        + '&label='       + encodeURIComponent(label)
        + '&url='         + encodeURIComponent(url)
        + '&target=_self&status=1'
  });
}

// Confirm before delete
document.querySelectorAll('[data-confirm]').forEach(function(el) {
  el.addEventListener('click', function(e) {
    if (!confirm(this.dataset.confirm)) e.preventDefault();
  });
});
</script>

<?php require_once '../includes/layout-bottom.php'; ?>

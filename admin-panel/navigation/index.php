<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Navigation';

if (isset($_GET['delete'])) { handleDelete('menu_items', $_GET['delete']); flashMessage('success','Menu item deleted.'); redirect(ADMIN_URL.'/navigation/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('menu_items', $_GET['toggle']); redirect(ADMIN_URL.'/navigation/index.php'); }

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

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px">

  <!-- Current Menu -->
  <div class="admin-table-wrap">
    <div class="table-header"><h3>Menu Items (<?= count($items) ?>)</h3></div>
    <table>
      <thead><tr><th>Order</th><th>Label</th><th>URL</th><th>Target</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr id="row-<?= $item['id'] ?>">
          <td><input type="number" value="<?= $item['sort_order'] ?>" style="width:56px;padding:4px 8px;border:1px solid var(--border);border-radius:4px;font-size:0.82rem" onchange="updateOrder(<?= $item['id'] ?>,this.value)"></td>
          <td><strong><?= e($item['label']) ?></strong></td>
          <td><code style="font-size:0.82rem"><?= e($item['url']) ?></code></td>
          <td><?= $item['target'] === '_blank' ? '<span class="badge badge-info">New Tab</span>' : 'Same Tab' ?></td>
          <td>
            <a href="?toggle=<?= $item['id'] ?>" title="Click to toggle" style="text-decoration:none">
              <span class="badge badge-<?= $item['status'] ? 'success' : 'danger' ?>" style="cursor:pointer">
                <i class="fas fa-<?= $item['status'] ? 'check-circle' : 'times-circle' ?>"></i>
                <?= $item['status'] ? 'Active' : 'Hidden' ?>
              </span>
            </a>
          </td>
          <td class="table-actions">
            <button type="button" class="btn btn-outline btn-sm btn-icon" title="Edit"
              onclick="openEdit(<?= $item['id'] ?>,'<?= e(addslashes($item['label'])) ?>','<?= e(addslashes($item['url'])) ?>','<?= $item['target'] ?>',<?= $item['status'] ?>,<?= $item['sort_order'] ?>)">
              <i class="fas fa-edit"></i>
            </button>
            <a href="?delete=<?= $item['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete '<?= e($item['label']) ?>'?"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div style="padding:12px 16px;border-top:1px solid var(--border)">
      <p style="font-size:0.82rem;color:var(--text-light)"><i class="fas fa-info-circle"></i> Change the Order number and it updates instantly. Changes appear on the live website immediately.</p>
    </div>
  </div>

  <!-- Add New -->
  <div>
    <div class="admin-table-wrap" style="margin-bottom:16px">
      <div class="table-header"><h3>Add Menu Item</h3></div>
      <div style="padding:20px">
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="add_item" value="1">
          <div class="form-group">
            <label>Label *</label>
            <input type="text" name="label" required placeholder="e.g. Home, About, Services">
          </div>
          <div class="form-group">
            <label>URL *</label>
            <input type="text" name="url" required placeholder="/about or https://...">
          </div>
          <div class="form-group">
            <label>Open In</label>
            <select name="target">
              <option value="_self">Same Tab</option>
              <option value="_blank">New Tab</option>
            </select>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
              <label>Status</label>
              <select name="status">
                <option value="1">Active</option>
                <option value="0">Hidden</option>
              </select>
            </div>
            <div class="form-group">
              <label>Sort Order</label>
              <input type="number" name="sort_order" value="<?= count($items) + 1 ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-plus"></i> Add Item</button>
        </form>
      </div>
    </div>
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px 16px;font-size:0.82rem;color:#92400e">
      <i class="fas fa-lightbulb" style="margin-right:6px"></i>
      <strong>Note:</strong> Navigation changes reflect immediately on the public website. The header reads from this table on every page load.
    </div>
  </div>

</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:400px">
    <h3 style="margin-bottom:20px">Edit Menu Item</h3>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="edit_item" value="1">
      <input type="hidden" name="edit_id" id="editId">
      <div class="form-group"><label>Label *</label><input type="text" name="label" id="editLabel" required></div>
      <div class="form-group"><label>URL *</label><input type="text" name="url" id="editUrl" required></div>
      <div class="form-group">
        <label>Open In</label>
        <select name="target" id="editTarget">
          <option value="_self">Same Tab</option>
          <option value="_blank">New Tab</option>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="editStatus">
            <option value="1">Active</option>
            <option value="0">Hidden</option>
          </select>
        </div>
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" id="editOrder">
        </div>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Update</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(id, label, url, target, status, order) {
  document.getElementById('editId').value = id;
  document.getElementById('editLabel').value = label;
  document.getElementById('editUrl').value = url;
  document.getElementById('editTarget').value = target;
  document.getElementById('editStatus').value = status;
  document.getElementById('editOrder').value = order;
  document.getElementById('editModal').style.display = 'flex';
}
function updateOrder(id, val) {
  // Get current row data so we don't wipe label/url
  const row = document.getElementById('row-' + id);
  const label = row.querySelector('td:nth-child(2) strong').innerText;
  const url   = row.querySelector('td:nth-child(3) code').innerText;
  fetch('', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'csrf_token=<?= csrf_token() ?>&edit_item=1&edit_id=' + id
        + '&sort_order=' + encodeURIComponent(val)
        + '&label=' + encodeURIComponent(label)
        + '&url=' + encodeURIComponent(url)
        + '&target=_self&status=1'
  });
}
</script>
<?php require_once '../includes/layout-bottom.php'; ?>

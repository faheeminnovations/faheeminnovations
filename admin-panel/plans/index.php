<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Pricing Plans';

// Toggle active status
if (isset($_GET['toggle'])) {
    handleStatusToggle('credit_plans', (int)$_GET['toggle']);
    redirect(ADMIN_URL . '/plans/index.php');
}

// Delete plan
if (isset($_GET['delete'])) {
    handleDelete('credit_plans', (int)$_GET['delete']);
    flashMessage('success', 'Plan deleted.');
    redirect(ADMIN_URL . '/plans/index.php');
}

// Add new plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    csrf_verify();
    $pdo->prepare("INSERT INTO credit_plans (name, slug, credits, price, period, is_active, sort_order) VALUES (?,?,?,?,?,?,?)")
        ->execute([
            trim($_POST['name']),
            strtolower(trim($_POST['slug'])),
            (int)$_POST['credits'],
            (float)$_POST['price'],
            $_POST['period'] === 'one-time' ? 'one-time' : 'month',
            (int)($_POST['is_active'] ?? 1),
            (int)$_POST['sort_order']
        ]);
    flashMessage('success', 'Plan added.');
    redirect(ADMIN_URL . '/plans/index.php');
}

// Update plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_plan'])) {
    csrf_verify();
    $eid = (int)$_POST['edit_id'];
    $pdo->prepare("UPDATE credit_plans SET name=?, slug=?, credits=?, price=?, period=?, is_active=?, sort_order=? WHERE id=?")
        ->execute([
            trim($_POST['name']),
            strtolower(trim($_POST['slug'])),
            (int)$_POST['credits'],
            (float)$_POST['price'],
            $_POST['period'] === 'one-time' ? 'one-time' : 'month',
            (int)($_POST['is_active'] ?? 1),
            (int)$_POST['sort_order'],
            $eid
        ]);
    flashMessage('success', 'Plan updated.');
    redirect(ADMIN_URL . '/plans/index.php');
}

$plans = $pdo->query("SELECT * FROM credit_plans ORDER BY sort_order ASC")->fetchAll();
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<style>
.plans-page-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) { .plans-page-grid { grid-template-columns: 1fr; } }
.plan-preview {
    background: linear-gradient(135deg, #0B3C33, #1a6b5a);
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    margin-bottom: 16px;
}
.plan-preview h4 { margin: 0 0 4px; font-size: 1rem; }
.plan-preview .price { font-size: 1.8rem; font-weight: 800; margin: 8px 0 4px; }
.plan-preview .credits { font-size: .85rem; opacity: .8; }

/* Modal */
.modal-backdrop { display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center;padding:16px; }
.modal-backdrop.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden; }
.modal-head { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between; }
.modal-head h3 { font-size:1rem;font-weight:700;margin:0; }
.modal-close { background:none;border:none;font-size:1.2rem;color:var(--text-light);cursor:pointer;padding:4px 8px;border-radius:6px; }
.modal-body { padding:24px; }
.modal-footer { padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end; }
.two-col { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
</style>

<div class="plans-page-grid">

    <!-- Plans Table -->
    <div class="admin-table-wrap">
        <div class="table-header">
            <h3><i class="fas fa-crown" style="margin-right:8px;color:var(--primary)"></i> Pricing Plans (<?= count($plans) ?>)</h3>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('addPanel').scrollIntoView({behavior:'smooth'});document.querySelector('[name=name]').focus()">
                <i class="fas fa-plus"></i> Add Plan
            </button>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Plan</th>
                        <th>Credits</th>
                        <th>Price (PKR)</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plans as $p): ?>
                    <?php
                    $icons = ['free'=>'🆓','pro'=>'⭐','premium'=>'🚀','lifetime'=>'♾️'];
                    $icon = $icons[$p['slug']] ?? '📦';
                    ?>
                    <tr>
                        <td>
                            <input type="number" value="<?= $p['sort_order'] ?>"
                                style="width:54px;padding:5px 8px;border:1px solid var(--border);border-radius:6px;font-size:.82rem;text-align:center"
                                onchange="updateOrder(<?= $p['id'] ?>, this.value)">
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span style="font-size:1.3rem"><?= $icon ?></span>
                                <div>
                                    <strong><?= e($p['name']) ?></strong>
                                    <div style="font-size:.75rem;color:var(--text-light)"><?= e($p['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color:var(--primary)"><?= number_format($p['credits']) ?></strong></td>
                        <td>
                            <?php if ($p['price'] > 0): ?>
                            <strong>PKR <?= number_format($p['price'], 0) ?></strong>
                            <?php else: ?>
                            <span class="badge badge-success">Free</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $p['period'] === 'one-time' ? 'warning' : 'info' ?>">
                                <?= $p['period'] === 'one-time' ? 'One-Time' : 'Monthly' ?>
                            </span>
                        </td>
                        <td>
                            <a href="?toggle=<?= $p['id'] ?>" style="text-decoration:none">
                                <span class="badge badge-<?= $p['is_active'] ? 'success' : 'danger' ?>" style="cursor:pointer">
                                    <i class="fas fa-<?= $p['is_active'] ? 'check-circle' : 'times-circle' ?>"></i>
                                    <?= $p['is_active'] ? 'Active' : 'Hidden' ?>
                                </span>
                            </a>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="btn btn-outline btn-sm btn-icon" title="Edit"
                                    onclick="openEdit(<?= $p['id'] ?>,'<?= e(addslashes($p['name'])) ?>','<?= e($p['slug']) ?>',<?= $p['credits'] ?>,<?= $p['price'] ?>,'<?= $p['period'] ?>',<?= $p['is_active'] ?>,<?= $p['sort_order'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm btn-icon"
                                    data-confirm="Delete '<?= e($p['name']) ?>' plan?">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:12px 20px;border-top:1px solid var(--border);background:#fafafa">
            <p style="font-size:.8rem;color:var(--text-light)">
                <i class="fas fa-info-circle" style="color:var(--primary)"></i>
                Plans show on user dashboard. Price changes reflect immediately. Credits are assigned when admin manually upgrades a user's plan.
            </p>
        </div>
    </div>

    <!-- Add Plan Panel -->
    <div id="addPanel" class="admin-table-wrap">
        <div class="table-header">
            <h3><i class="fas fa-plus-circle" style="margin-right:8px;color:var(--primary)"></i> Add Plan</h3>
        </div>
        <div style="padding:20px">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="add_plan" value="1">

                <div class="form-group">
                    <label>Plan Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Pro, Premium">
                </div>
                <div class="form-group">
                    <label>Slug <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="slug" required placeholder="e.g. pro, premium">
                    <div class="form-hint">Lowercase, no spaces. Used internally.</div>
                </div>
                <div class="two-col">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Credits <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="credits" required min="1" placeholder="300">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Price (PKR)</label>
                        <input type="number" name="price" step="1" min="0" value="0" placeholder="0 = Free">
                    </div>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>Billing Period</label>
                    <select name="period">
                        <option value="month">Monthly</option>
                        <option value="one-time">One-Time</option>
                    </select>
                </div>
                <div class="two-col">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Status</label>
                        <select name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= count($plans) + 1 ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:20px">
                    <i class="fas fa-plus"></i> Add Plan
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-backdrop" onclick="if(event.target===this)closeEdit()">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-edit" style="margin-right:8px;color:var(--primary)"></i> Edit Plan</h3>
            <button class="modal-close" onclick="closeEdit()">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="edit_plan" value="1">
                <input type="hidden" name="edit_id" id="editId">

                <div class="form-group">
                    <label>Plan Name *</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group">
                    <label>Slug *</label>
                    <input type="text" name="slug" id="editSlug" required>
                    <div class="form-hint">Lowercase only. Changing slug may affect existing users.</div>
                </div>
                <div class="two-col">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Credits *</label>
                        <input type="number" name="credits" id="editCredits" required min="1">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Price (PKR)</label>
                        <input type="number" name="price" id="editPrice" step="1" min="0">
                    </div>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>Billing Period</label>
                    <select name="period" id="editPeriod">
                        <option value="month">Monthly</option>
                        <option value="one-time">One-Time</option>
                    </select>
                </div>
                <div class="two-col">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Status</label>
                        <select name="is_active" id="editActive">
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
                <button type="button" class="btn btn-outline" onclick="closeEdit()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name, slug, credits, price, period, active, order) {
    document.getElementById('editId').value      = id;
    document.getElementById('editName').value    = name;
    document.getElementById('editSlug').value    = slug;
    document.getElementById('editCredits').value = credits;
    document.getElementById('editPrice').value   = price;
    document.getElementById('editPeriod').value  = period;
    document.getElementById('editActive').value  = active;
    document.getElementById('editOrder').value   = order;
    document.getElementById('editModal').classList.add('open');
    document.getElementById('editName').focus();
}
function closeEdit() {
    document.getElementById('editModal').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEdit(); });

function updateOrder(id, val) {
    fetch('', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= csrf_token() ?>&edit_plan=1&edit_id='+id+'&sort_order='+encodeURIComponent(val)
            + '&name=x&slug=x&credits=1&price=0&period=month&is_active=1'
    });
}

document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => { if (!confirm(el.dataset.confirm)) e.preventDefault(); });
});
</script>

<?php require_once '../includes/layout-bottom.php'; ?>

<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Tool Users';

// Handle credit adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adjust_credits'])) {
    csrf_verify();
    $uid    = (int)$_POST['user_id'];
    $amount = (int)$_POST['amount'];
    $type   = $_POST['type'] === 'add' ? 'credit' : 'debit';
    $note   = trim($_POST['note']) ?: 'Manual adjustment by admin';

    if ($type === 'credit') {
        require_once SITE_ROOT . '/includes/credits.php';
        addCredits($uid, abs($amount), $note);
    } else {
        $stmt = $pdo->prepare("SELECT credits FROM user_credits WHERE user_id=?");
        $stmt->execute([$uid]);
        $cur = (int)$stmt->fetchColumn();
        $newBal = max(0, $cur - abs($amount));
        $pdo->prepare("UPDATE user_credits SET credits=? WHERE user_id=?")->execute([$newBal, $uid]);
        $pdo->prepare("INSERT INTO credit_transactions (user_id,type,amount,balance_after,description) VALUES (?,?,?,?,?)")
            ->execute([$uid, 'debit', abs($amount), $newBal, $note]);
    }
    flashMessage('success', 'Credits updated.');
    redirect(ADMIN_URL . '/tool-users/index.php');
}

// Handle plan change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_plan'])) {
    csrf_verify();
    $uid  = (int)$_POST['user_id'];
    $plan = $_POST['plan'];
    $pdo->prepare("UPDATE tool_users SET plan=? WHERE id=?")->execute([$plan, $uid]);
    flashMessage('success', 'Plan updated.');
    redirect(ADMIN_URL . '/tool-users/index.php');
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    handleStatusToggle('tool_users', (int)$_GET['toggle']);
    redirect(ADMIN_URL . '/tool-users/index.php');
}

$search = trim($_GET['q'] ?? '');
$where  = $search ? "WHERE u.name LIKE ? OR u.email LIKE ?" : "WHERE 1";
$params = $search ? ["%$search%", "%$search%"] : [];
$users  = $pdo->prepare("SELECT u.*, uc.credits, uc.total_used FROM tool_users u LEFT JOIN user_credits uc ON u.id=uc.user_id $where ORDER BY u.created_at DESC");
$users->execute($params);
$users = $users->fetchAll();

require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <h3>Tool Users (<?= count($users) ?>)</h3>
  <form method="GET" style="display:flex;gap:8px">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search name or email..." style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font:inherit;font-size:.85rem;width:240px">
    <button class="btn btn-outline btn-sm" type="submit"><i class="fas fa-search"></i></button>
  </form>
</div>

<div class="admin-table-wrap">
  <table>
    <thead>
      <tr><th>User</th><th>Plan</th><th>Credits</th><th>Used</th><th>Joined</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td>
          <div style="font-weight:600"><?= htmlspecialchars($u['name']) ?></div>
          <div style="font-size:.78rem;color:var(--text-light)"><?= htmlspecialchars($u['email']) ?></div>
        </td>
        <td>
          <span style="background:rgba(11,60,51,.08);color:var(--primary);padding:3px 10px;border-radius:50px;font-size:.78rem;font-weight:700">
            <?= ['free'=>'🆓','pro'=>'⭐','premium'=>'🚀','lifetime'=>'♾️'][$u['plan']] ?? '' ?> <?= ucfirst($u['plan']) ?>
          </span>
        </td>
        <td><strong style="color:var(--primary)"><?= number_format((int)$u['credits']) ?></strong></td>
        <td><?= number_format((int)$u['total_used']) ?></td>
        <td style="font-size:.82rem;color:var(--text-light)"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        <td>
          <a href="?toggle=<?= $u['id'] ?>" style="text-decoration:none">
            <span class="badge badge-<?= $u['status'] ? 'success' : 'danger' ?>" style="cursor:pointer">
              <?= $u['status'] ? 'Active' : 'Blocked' ?>
            </span>
          </a>
        </td>
        <td class="table-actions">
          <button class="btn btn-outline btn-sm btn-icon" title="Adjust Credits"
            onclick="openModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>', <?= (int)$u['credits'] ?>, '<?= $u['plan'] ?>')">
            <i class="fas fa-coins"></i>
          </button>
          <a href="view.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="View History"><i class="fas fa-eye"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$users): ?>
      <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-light)">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Credits & Plan Modal -->
<div id="creditsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:420px;max-height:90vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <h3 style="margin:0">Manage User</h3>
      <button onclick="document.getElementById('creditsModal').style.display='none'" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light)">✕</button>
    </div>
    <p id="modalUserName" style="font-size:.88rem;color:var(--text-light);margin-bottom:16px"></p>

    <!-- Balance display -->
    <div style="background:var(--bg-alt,#f8fafc);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
      <div>
        <div style="font-size:.75rem;color:var(--text-light)">Current Balance</div>
        <div id="modalBalance" style="font-size:1.8rem;font-weight:800;color:var(--primary)"></div>
      </div>
      <div>
        <div style="font-size:.75rem;color:var(--text-light)">Current Plan</div>
        <div id="modalCurrentPlan" style="font-size:1rem;font-weight:700;color:var(--primary)"></div>
      </div>
    </div>

    <!-- Tab switcher -->
    <div style="display:flex;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:20px">
      <button id="tabCredits" onclick="switchTab('credits')" style="flex:1;padding:9px;border:none;background:var(--primary);color:#fff;font:inherit;font-size:.88rem;font-weight:600;cursor:pointer">
        <i class="fas fa-coins"></i> Adjust Credits
      </button>
      <button id="tabPlan" onclick="switchTab('plan')" style="flex:1;padding:9px;border:none;background:#fff;color:var(--text);font:inherit;font-size:.88rem;font-weight:600;cursor:pointer">
        <i class="fas fa-crown"></i> Change Plan
      </button>
    </div>

    <!-- Credits Form -->
    <div id="panelCredits">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="adjust_credits" value="1">
        <input type="hidden" name="user_id" id="modalUserId">
        <div class="form-group">
          <label>Action</label>
          <select name="type" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:6px;font:inherit">
            <option value="add">➕ Add Credits</option>
            <option value="deduct">➖ Deduct Credits</option>
          </select>
        </div>
        <div class="form-group">
          <label>Amount</label>
          <input type="number" name="amount" min="1" value="30" required style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:6px;font:inherit">
        </div>
        <div class="form-group">
          <label>Note (optional)</label>
          <input type="text" name="note" placeholder="e.g. Monthly top-up" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:6px;font:inherit">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-save"></i> Update Credits</button>
      </form>
    </div>

    <!-- Plan Form -->
    <div id="panelPlan" style="display:none">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="change_plan" value="1">
        <input type="hidden" name="user_id" id="modalUserIdPlan">
        <div class="form-group">
          <label>Select New Plan</label>
          <select name="plan" id="modalPlanSelect" style="width:100%;padding:11px 12px;border:1px solid var(--border);border-radius:6px;font:inherit;font-size:.95rem">
            <option value="free">🆓 Free — 30 Credits/month</option>
            <option value="pro">⭐ Pro — 300 Credits/month (PKR 2,800)</option>
            <option value="premium">🚀 Premium — 1,000 Credits/month (PKR 5,600)</option>
            <option value="lifetime">♾️ Lifetime — 5,000 Credits (PKR 27,720)</option>
          </select>
        </div>
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:6px;padding:10px 12px;font-size:.82rem;color:#92400e;margin-bottom:16px">
          <i class="fas fa-info-circle"></i> Changing plan does <strong>not</strong> automatically update credits. Use "Adjust Credits" to add the plan's credits manually.
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-exchange-alt"></i> Change Plan</button>
      </form>
    </div>

  </div>
</div>

<script>
function switchTab(tab) {
  document.getElementById('panelCredits').style.display = tab === 'credits' ? 'block' : 'none';
  document.getElementById('panelPlan').style.display    = tab === 'plan'    ? 'block' : 'none';
  document.getElementById('tabCredits').style.background = tab === 'credits' ? 'var(--primary)' : '#fff';
  document.getElementById('tabCredits').style.color      = tab === 'credits' ? '#fff' : 'var(--text)';
  document.getElementById('tabPlan').style.background    = tab === 'plan'    ? 'var(--primary)' : '#fff';
  document.getElementById('tabPlan').style.color         = tab === 'plan'    ? '#fff' : 'var(--text)';
}

const planIcons = {free:'🆓 Free', pro:'⭐ Pro', premium:'🚀 Premium', lifetime:'♾️ Lifetime'};

function openModal(id, name, credits, plan) {
  document.getElementById('modalUserId').value     = id;
  document.getElementById('modalUserIdPlan').value = id;
  document.getElementById('modalUserName').textContent = name;
  document.getElementById('modalBalance').textContent  = credits.toLocaleString();
  document.getElementById('modalCurrentPlan').textContent = planIcons[plan] || plan;
  document.getElementById('modalPlanSelect').value = plan;
  switchTab('credits');
  document.getElementById('creditsModal').style.display = 'flex';
}
</script>

<?php require_once '../includes/layout-bottom.php'; ?>

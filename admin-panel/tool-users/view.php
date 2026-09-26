<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'User Details';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { flashMessage('error','Invalid user.'); redirect(ADMIN_URL.'/tool-users/index.php'); }

// Get user
$stmt = $pdo->prepare("SELECT u.*, uc.credits, uc.total_used FROM tool_users u LEFT JOIN user_credits uc ON u.id = uc.user_id WHERE u.id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { flashMessage('error','User not found.'); redirect(ADMIN_URL.'/tool-users/index.php'); }

// Get credit transactions
$txStmt = $pdo->prepare("SELECT * FROM credit_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$txStmt->execute([$id]);
$transactions = $txStmt->fetchAll();

require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<!-- Back + Header -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
    <a href="index.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    <h2 style="margin:0;font-size:1.1rem">User Details</h2>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:24px;align-items:start">

    <!-- User Profile Card -->
    <div class="admin-table-wrap" style="padding:0;overflow:hidden">
        <div style="background:var(--primary);padding:28px;text-align:center">
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:2rem;color:#fff;font-weight:700">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <h3 style="color:#fff;margin:0 0 4px"><?= e($user['name']) ?></h3>
            <p style="color:rgba(255,255,255,.7);font-size:.85rem;margin:0"><?= e($user['email']) ?></p>
        </div>
        <div style="padding:20px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
                <div style="background:#f0fdf4;border-radius:8px;padding:14px;text-align:center">
                    <div style="font-size:1.6rem;font-weight:800;color:var(--primary)"><?= number_format((int)$user['credits']) ?></div>
                    <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Credits Left</div>
                </div>
                <div style="background:#fef9ec;border-radius:8px;padding:14px;text-align:center">
                    <div style="font-size:1.6rem;font-weight:800;color:#d97706"><?= number_format((int)$user['total_used']) ?></div>
                    <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Total Used</div>
                </div>
            </div>

            <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--text-light);font-weight:600">Plan</td>
                    <td style="padding:10px 0;text-align:right">
                        <?php $planIcons = ['free'=>'🆓','pro'=>'⭐','premium'=>'🚀','lifetime'=>'♾️']; ?>
                        <span style="background:rgba(11,60,51,.08);color:var(--primary);padding:3px 10px;border-radius:50px;font-weight:700">
                            <?= ($planIcons[$user['plan']] ?? '') . ' ' . ucfirst($user['plan']) ?>
                        </span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--text-light);font-weight:600">Status</td>
                    <td style="padding:10px 0;text-align:right">
                        <span class="badge badge-<?= $user['status'] ? 'success' : 'danger' ?>">
                            <?= $user['status'] ? 'Active' : 'Blocked' ?>
                        </span>
                    </td>
                </tr>
                <?php if (!empty($user['provider'])): ?>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--text-light);font-weight:600">Login Via</td>
                    <td style="padding:10px 0;text-align:right">
                        <span class="badge badge-info"><?= ucfirst(e($user['provider'])) ?></span>
                    </td>
                </tr>
                <?php endif; ?>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--text-light);font-weight:600">Joined</td>
                    <td style="padding:10px 0;text-align:right;font-size:.82rem"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php if (!empty($user['last_login'])): ?>
                <tr>
                    <td style="padding:10px 0;color:var(--text-light);font-weight:600">Last Login</td>
                    <td style="padding:10px 0;text-align:right;font-size:.82rem"><?= date('d M Y, H:i', strtotime($user['last_login'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>

            <div style="margin-top:16px;display:flex;gap:8px">
                <a href="?toggle=<?= $user['id'] ?>&redirect=view" class="btn btn-<?= $user['status'] ? 'danger' : 'success' ?> btn-sm" style="flex:1;justify-content:center">
                    <i class="fas fa-<?= $user['status'] ? 'ban' : 'check' ?>"></i>
                    <?= $user['status'] ? 'Block User' : 'Activate User' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="admin-table-wrap">
        <div class="table-header">
            <h3><i class="fas fa-history" style="margin-right:8px;color:var(--primary)"></i> Credit Transactions</h3>
            <span style="font-size:.82rem;color:var(--text-light)">Last 50 records</span>
        </div>
        <?php if ($transactions): ?>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td style="font-size:.82rem;color:var(--text-light);white-space:nowrap">
                            <?= date('d M Y, H:i', strtotime($tx['created_at'])) ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $tx['type'] === 'credit' ? 'success' : 'danger' ?>">
                                <i class="fas fa-<?= $tx['type'] === 'credit' ? 'plus' : 'minus' ?>"></i>
                                <?= ucfirst($tx['type']) ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color:<?= $tx['type'] === 'credit' ? 'var(--success)' : 'var(--danger)' ?>">
                                <?= $tx['type'] === 'credit' ? '+' : '-' ?><?= number_format((int)$tx['amount']) ?>
                            </strong>
                        </td>
                        <td><strong><?= number_format((int)$tx['balance_after']) ?></strong></td>
                        <td style="font-size:.85rem;color:var(--text-light)"><?= e($tx['description'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="padding:48px;text-align:center;color:var(--text-light)">
            <i class="fas fa-history" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px"></i>
            <p>No transactions yet.</p>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once '../includes/layout-bottom.php'; ?>

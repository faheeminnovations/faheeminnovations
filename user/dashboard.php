<?php
require_once '../includes/config.php';
require_once '../includes/user-auth.php';
require_once '../includes/credits.php';
requireUserLogin();

$user         = getUser();
$credits      = (int)($user['credits'] ?? 0);
$transactions = getUserTransactions($user['id'], 15);
$plans        = $pdo->query("SELECT * FROM credit_plans WHERE is_active=1 ORDER BY sort_order")->fetchAll();
$welcome      = isset($_GET['welcome']);

$planColors = ['free'=>'#16a34a','pro'=>'#2563eb','premium'=>'#7c3aed','lifetime'=>'#b45309'];
$planIcons  = ['free'=>'🆓','pro'=>'⭐','premium'=>'🚀','lifetime'=>'♾️'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Dashboard — Faheem Innovations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',sans-serif;background:#f0f4f8;min-height:100vh;color:#1a202c}
  .topbar{background:#0B3C33;color:#fff;padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
  .topbar-brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:1rem}
  .topbar-brand img{height:32px;filter:brightness(0) invert(1)}
  .topbar-right{display:flex;align-items:center;gap:16px;font-size:.88rem}
  .topbar-right a{color:rgba(255,255,255,.75);text-decoration:none;transition:color .2s}
  .topbar-right a:hover{color:#fff}
  .wrap{max-width:960px;margin:0 auto;padding:32px 20px}
  .welcome-banner{background:linear-gradient(135deg,#0B3C33,#1a6b5a);color:#fff;border-radius:14px;padding:24px 28px;margin-bottom:28px;display:flex;align-items:center;gap:16px}
  .welcome-banner i{font-size:2rem;opacity:.8}
  .welcome-banner h2{font-size:1.2rem;margin-bottom:4px}
  .welcome-banner p{font-size:.88rem;opacity:.85}
  .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px}
  .stat-box{background:#fff;border-radius:12px;padding:20px 24px;border:1px solid #e2e8f0}
  .stat-box .label{font-size:.8rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px}
  .stat-box .value{font-size:2rem;font-weight:800;color:#0B3C33}
  .stat-box .sub{font-size:.8rem;color:#94a3b8;margin-top:4px}
  .plan-badge{display:inline-flex;align-items:center;gap:6px;font-size:.8rem;font-weight:700;padding:3px 12px;border-radius:50px;background:rgba(11,60,51,.1);color:#0B3C33}
  .section-title{font-size:1rem;font-weight:700;color:#1a202c;margin-bottom:16px;display:flex;align-items:center;gap:8px}
  .card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:28px}
  .card-body{padding:20px 24px}
  /* Plans grid */
  .plans-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px}
  .plan-card{border:2px solid #e2e8f0;border-radius:12px;padding:18px 16px;text-align:center;position:relative;transition:all .2s}
  .plan-card.current{border-color:#0B3C33;background:#f0fdf4}
  .plan-card .plan-icon{font-size:1.6rem;margin-bottom:8px}
  .plan-card h4{font-size:.95rem;font-weight:700;margin-bottom:4px}
  .plan-card .plan-price{font-size:1.3rem;font-weight:800;color:#0B3C33;margin:6px 0}
  .plan-card .plan-credits{font-size:.8rem;color:#64748b;margin-bottom:12px}
  .plan-card .current-tag{position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#0B3C33;color:#fff;font-size:.7rem;font-weight:700;padding:2px 10px;border-radius:50px;white-space:nowrap}
  .btn-upgrade{display:block;padding:8px 12px;border-radius:8px;background:#0B3C33;color:#fff;font-size:.82rem;font-weight:600;text-decoration:none;transition:background .2s;border:none;cursor:pointer;width:100%;text-align:center}
  .btn-upgrade:hover{background:#072e27}
  .btn-current{display:block;padding:8px 12px;border-radius:8px;background:#ecfdf5;color:#0B3C33;font-size:.82rem;font-weight:600;border:1px solid #6ee7b7;text-align:center}
  /* Transactions */
  table{width:100%;border-collapse:collapse}
  th,td{padding:10px 16px;text-align:left;font-size:.85rem;border-bottom:1px solid #f1f5f9}
  th{font-weight:600;color:#64748b;font-size:.78rem;text-transform:uppercase;background:#f8fafc}
  .credit-badge{display:inline-flex;align-items:center;gap:4px;font-weight:700;font-size:.88rem}
  .debit{color:#dc2626}.credit-in{color:#16a34a}
  .tools-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px}
  .tool-link{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;text-decoration:none;color:#1a202c;transition:all .2s}
  .tool-link:hover{border-color:#0B3C33;background:#f0fdf4}
  .tool-link i{width:36px;height:36px;background:rgba(11,60,51,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#0B3C33;font-size:1.1rem;flex-shrink:0}
  .tool-link span{font-size:.88rem;font-weight:600}
  @media(max-width:600px){.plans-grid{grid-template-columns:1fr 1fr}.topbar-brand span{display:none}}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">
    <img src="<?= SITE_URL ?>/images/main-logo.png" alt="Faheem Innovations">
    <span>AI Tools Dashboard</span>
  </div>
  <div class="topbar-right">
    <span><i class="fas fa-coins" style="color:#fbbf24"></i> <strong><?= number_format($credits) ?></strong> Credits</span>
    <a href="<?= SITE_URL ?>/ai-tools"><i class="fas fa-tools"></i> All Tools</a>
    <a href="<?= SITE_URL ?>/user/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</div>

<div class="wrap">

  <?php if ($welcome): ?>
  <div class="welcome-banner">
    <i class="fas fa-party-horn"></i>
    <div>
      <h2>Welcome to Faheem Innovations, <?= htmlspecialchars($user['name']) ?>!</h2>
      <p>Your account is ready. You have 30 free credits to get started. Try AI Video HD now!</p>
    </div>
  </div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-box">
      <div class="label">Available Credits</div>
      <div class="value"><?= number_format($credits) ?></div>
      <div class="sub">Resets monthly on Free plan</div>
    </div>
    <div class="stat-box">
      <div class="label">Total Used</div>
      <?php $used = $pdo->prepare("SELECT total_used FROM user_credits WHERE user_id=?"); $used->execute([$user['id']]); $totalUsed = (int)$used->fetchColumn(); ?>
      <div class="value"><?= number_format($totalUsed) ?></div>
      <div class="sub">All time</div>
    </div>
    <div class="stat-box">
      <div class="label">Current Plan</div>
      <div class="value" style="font-size:1.4rem"><?= $planIcons[$user['plan']] ?? '🆓' ?></div>
      <div class="sub"><span class="plan-badge"><?= ucfirst($user['plan']) ?></span></div>
    </div>
    <div class="stat-box">
      <div class="label">Member Since</div>
      <div class="value" style="font-size:1.2rem"><?= date('M Y', strtotime($user['created_at'])) ?></div>
      <div class="sub"><?= htmlspecialchars($user['email']) ?></div>
    </div>
  </div>

  <!-- Available Tools -->
  <div class="section-title"><i class="fas fa-tools" style="color:#0B3C33"></i> Available Tools</div>
  <?php $tools = $pdo->query("SELECT name,slug,icon,tool_url FROM ai_tools WHERE status=1 AND tool_url IS NOT NULL AND tool_url != '' ORDER BY sort_order")->fetchAll(); ?>
  <div class="tools-grid" style="margin-bottom:28px">
    <?php foreach($tools as $t): ?>
    <a href="<?= htmlspecialchars($t['tool_url']) ?>" class="tool-link">
      <i class="<?= htmlspecialchars($t['icon']) ?>"></i>
      <span><?= htmlspecialchars($t['name']) ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Upgrade Plans -->
  <div class="section-title"><i class="fas fa-crown" style="color:#f59e0b"></i> Your Plan & Upgrades</div>
  <div class="card">
    <div class="card-body">
      <div class="plans-grid">
        <?php foreach($plans as $plan):
          $isCurrent = $user['plan'] === $plan['slug'];
          $icons = ['free'=>'🆓','pro'=>'⭐','premium'=>'🚀','lifetime'=>'♾️'];
        ?>
        <div class="plan-card <?= $isCurrent ? 'current' : '' ?>">
          <?php if($isCurrent): ?><div class="current-tag">Current Plan</div><?php endif; ?>
          <div class="plan-icon"><?= $icons[$plan['slug']] ?? '📦' ?></div>
          <h4><?= htmlspecialchars(ucfirst($plan['name'])) ?></h4>
          <div class="plan-price"><?= $plan['price'] > 0 ? 'PKR '.number_format($plan['price'],0) : 'Free' ?></div>
          <div class="plan-credits"><?= number_format($plan['credits']) ?> Credits/<?= $plan['period'] === 'one-time' ? 'lifetime' : 'month' ?></div>
          <?php if($isCurrent): ?>
          <span class="btn-current"><i class="fas fa-check"></i> Active</span>
          <?php else: ?>
          <a href="<?= SITE_URL ?>/contact?subject=Upgrade+to+<?= urlencode(ucfirst($plan['name'])) ?>+Plan" class="btn-upgrade">Upgrade</a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Transaction History -->
  <div class="section-title"><i class="fas fa-history" style="color:#0B3C33"></i> Credit History</div>
  <div class="card">
    <?php if ($transactions): ?>
    <table>
      <thead><tr><th>Date</th><th>Description</th><th>Tool</th><th>Credits</th><th>Balance</th></tr></thead>
      <tbody>
        <?php foreach($transactions as $tx): ?>
        <tr>
          <td style="color:#64748b"><?= date('d M Y, H:i', strtotime($tx['created_at'])) ?></td>
          <td><?= htmlspecialchars($tx['description']) ?></td>
          <td><?= $tx['tool_slug'] ? '<span style="background:#f1f5f9;padding:2px 8px;border-radius:4px;font-size:.78rem">'.htmlspecialchars($tx['tool_slug']).'</span>' : '—' ?></td>
          <td><span class="credit-badge <?= $tx['type']==='debit' ? 'debit' : 'credit-in' ?>">
            <?= $tx['type']==='debit' ? '−' : '+' ?><?= number_format($tx['amount']) ?>
          </span></td>
          <td><strong><?= number_format($tx['balance_after']) ?></strong></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div style="padding:40px;text-align:center;color:#94a3b8">
      <i class="fas fa-receipt" style="font-size:2rem;margin-bottom:12px;display:block"></i>
      No transactions yet. Use a tool to see your credit history here.
    </div>
    <?php endif; ?>
  </div>

</div>
</body>
</html>

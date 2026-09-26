<?php
// ONE-TIME SETUP — auto-deletes after run
require_once 'includes/config.php';
$msgs = [];

// 1. tool_users table (separate from admin users)
$pdo->exec("CREATE TABLE IF NOT EXISTS tool_users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,
    plan         ENUM('free','pro','premium','lifetime') DEFAULT 'free',
    status       TINYINT DEFAULT 1,
    email_verified TINYINT DEFAULT 0,
    reset_token  VARCHAR(100) NULL,
    reset_expires DATETIME NULL,
    last_login   DATETIME NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$msgs[] = '✅ tool_users table created';

// 2. credit_plans table
$pdo->exec("CREATE TABLE IF NOT EXISTS credit_plans (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(50) NOT NULL,
    slug         VARCHAR(50) NOT NULL UNIQUE,
    credits      INT NOT NULL,
    price        DECIMAL(10,2) DEFAULT 0,
    period       ENUM('month','one-time') DEFAULT 'month',
    is_active    TINYINT DEFAULT 1,
    sort_order   INT DEFAULT 0
)");
$msgs[] = '✅ credit_plans table created';

// 3. user_credits table
$pdo->exec("CREATE TABLE IF NOT EXISTS user_credits (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL UNIQUE,
    credits      INT DEFAULT 0,
    total_used   INT DEFAULT 0,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tool_users(id) ON DELETE CASCADE
)");
$msgs[] = '✅ user_credits table created';

// 4. credit_transactions table
$pdo->exec("CREATE TABLE IF NOT EXISTS credit_transactions (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    type         ENUM('credit','debit') NOT NULL,
    amount       INT NOT NULL,
    balance_after INT NOT NULL,
    description  VARCHAR(255),
    tool_slug    VARCHAR(150) NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tool_users(id) ON DELETE CASCADE
)");
$msgs[] = '✅ credit_transactions table created';

// 5. Seed credit plans
$exists = $pdo->query("SELECT COUNT(*) FROM credit_plans")->fetchColumn();
if (!$exists) {
    $plans = [
        ['free',     'free',     30,   0,     'month',    1, 1],
        ['pro',      'pro',      300,  9.99,  'month',    1, 2],
        ['premium',  'premium',  1000, 19.99, 'month',    1, 3],
        ['lifetime', 'lifetime', 5000, 99.00, 'one-time', 1, 4],
    ];
    $stmt = $pdo->prepare("INSERT INTO credit_plans (name,slug,credits,price,period,is_active,sort_order) VALUES (?,?,?,?,?,?,?)");
    foreach ($plans as $p) $stmt->execute($p);
    $msgs[] = '✅ Credit plans seeded (Free/Pro/Premium/Lifetime)';
}

// 6. Add credit_cost column to ai_tools if not exists
try {
    $pdo->exec("ALTER TABLE ai_tools ADD COLUMN credit_cost_720 INT DEFAULT 5 AFTER pricing_plans");
    $pdo->exec("ALTER TABLE ai_tools ADD COLUMN credit_cost_1080 INT DEFAULT 10 AFTER credit_cost_720");
    $pdo->exec("ALTER TABLE ai_tools ADD COLUMN credit_cost_4k INT DEFAULT 25 AFTER credit_cost_1080");
    $msgs[] = '✅ Credit cost columns added to ai_tools';
} catch(Exception $e) {
    $msgs[] = 'ℹ️ Credit cost columns already exist';
}

// Update video-hd tool costs
$pdo->exec("UPDATE ai_tools SET credit_cost_720=5, credit_cost_1080=10, credit_cost_4k=25 WHERE slug='ai-video-hd'");
$msgs[] = '✅ AI Video HD credit costs set (720p=5, 1080p=10, 4K=25)';

@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Setup Done</title>
<style>body{font-family:Inter,sans-serif;max-width:580px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}ul{line-height:2.2;font-size:.9rem}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px}a{color:#0B3C33;font-weight:600}</style>
</head><body>
<h2>✅ User System Setup Complete</h2>
<div class="box"><ul><?php foreach($msgs as $m): ?><li><?= $m ?></li><?php endforeach; ?></ul>
<p style="font-size:.8rem;color:#065f46;margin:8px 0 0">🗑️ This file deleted itself.</p></div>
<p style="margin-top:20px">
  <a href="http://localhost/faheeminnovations/user/register">→ Register Page</a> &nbsp;|&nbsp;
  <a href="http://localhost/faheeminnovations/user/login">→ Login Page</a> &nbsp;|&nbsp;
  <a href="http://localhost/faheeminnovations/admin-panel/tool-users/">→ Admin: Manage Users</a>
</p>
</body></html>

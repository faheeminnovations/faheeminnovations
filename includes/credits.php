<?php
if (!defined('SITE_URL')) require_once __DIR__ . '/config.php';

function checkCredits($userId, $needed) {
    global $pdo;
    $row = $pdo->prepare("SELECT credits FROM user_credits WHERE user_id=?");
    $row->execute([$userId]);
    return (int)($row->fetchColumn() ?: 0) >= $needed;
}

function deductCredits($userId, $amount, $toolSlug, $description) {
    global $pdo;
    // Get current balance
    $stmt = $pdo->prepare("SELECT credits FROM user_credits WHERE user_id=?");
    $stmt->execute([$userId]);
    $current = (int)($stmt->fetchColumn() ?: 0);
    if ($current < $amount) return false;
    $newBalance = $current - $amount;
    // Update balance
    $pdo->prepare("UPDATE user_credits SET credits=?, total_used=total_used+? WHERE user_id=?")
        ->execute([$newBalance, $amount, $userId]);
    // Log transaction
    $pdo->prepare("INSERT INTO credit_transactions (user_id,type,amount,balance_after,description,tool_slug) VALUES (?,?,?,?,?,?)")
        ->execute([$userId, 'debit', $amount, $newBalance, $description, $toolSlug]);
    return true;
}

function addCredits($userId, $amount, $description) {
    global $pdo;
    // Ensure wallet row exists
    $pdo->prepare("INSERT IGNORE INTO user_credits (user_id, credits) VALUES (?,0)")->execute([$userId]);
    $stmt = $pdo->prepare("SELECT credits FROM user_credits WHERE user_id=?");
    $stmt->execute([$userId]);
    $current = (int)$stmt->fetchColumn();
    $newBalance = $current + $amount;
    $pdo->prepare("UPDATE user_credits SET credits=? WHERE user_id=?")->execute([$newBalance, $userId]);
    $pdo->prepare("INSERT INTO credit_transactions (user_id,type,amount,balance_after,description,tool_slug) VALUES (?,?,?,?,?,NULL)")
        ->execute([$userId, 'credit', $amount, $newBalance, $description]);
    return $newBalance;
}

function getCreditCost($toolSlug, $resolution = '1080') {
    global $pdo;
    $row = $pdo->prepare("SELECT credit_cost_720, credit_cost_1080, credit_cost_4k FROM ai_tools WHERE slug=?");
    $row->execute([$toolSlug]);
    $costs = $row->fetch();
    if (!$costs) return 10;
    return match(true) {
        $resolution <= 720  => (int)$costs['credit_cost_720'],
        $resolution <= 1080 => (int)$costs['credit_cost_1080'],
        default             => (int)$costs['credit_cost_4k'],
    };
}

function getUserTransactions($userId, $limit = 20) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM credit_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT $limit");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function resetMonthlyCredits() {
    // Called via cron or manually — resets free plan users each month
    global $pdo;
    $plans = $pdo->query("SELECT slug, credits FROM credit_plans WHERE is_active=1")->fetchAll();
    foreach ($plans as $plan) {
        if ($plan['slug'] === 'lifetime') continue; // lifetime never resets
        $pdo->prepare("UPDATE user_credits uc JOIN tool_users u ON uc.user_id=u.id SET uc.credits=? WHERE u.plan=?")
            ->execute([$plan['credits'], $plan['slug']]);
    }
}

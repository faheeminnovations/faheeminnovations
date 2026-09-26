<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('SITE_URL')) require_once __DIR__ . '/config.php';

function isUserLoggedIn() {
    return !empty($_SESSION['tool_user_id']);
}

function requireUserLogin($redirectBack = true) {
    if (!isUserLoggedIn()) {
        $back = $redirectBack ? '?redirect=' . urlencode($_SERVER['REQUEST_URI']) : '';
        header('Location: ' . SITE_URL . '/user/login.php' . $back);
        exit;
    }
}

function getUser() {
    if (!isUserLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.*, uc.credits FROM tool_users u LEFT JOIN user_credits uc ON u.id=uc.user_id WHERE u.id=? AND u.status=1");
    $stmt->execute([$_SESSION['tool_user_id']]);
    return $stmt->fetch();
}

function getUserCredits($userId = null) {
    global $pdo;
    $uid = $userId ?? $_SESSION['tool_user_id'] ?? 0;
    $row = $pdo->prepare("SELECT credits FROM user_credits WHERE user_id=?");
    $row->execute([$uid]);
    return (int)($row->fetchColumn() ?: 0);
}

function loginUser($user) {
    $_SESSION['tool_user_id']   = $user['id'];
    $_SESSION['tool_user_name'] = $user['name'];
    $_SESSION['tool_user_email']= $user['email'];
    $_SESSION['tool_user_plan'] = $user['plan'];
}

function logoutUser() {
    unset($_SESSION['tool_user_id'], $_SESSION['tool_user_name'],
          $_SESSION['tool_user_email'], $_SESSION['tool_user_plan']);
}

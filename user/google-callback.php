<?php
require_once '../includes/config.php';
require_once '../includes/user-auth.php';
require_once '../includes/credits.php';
require_once '../includes/google-oauth.php';

function oauthError($msg) {
    header('Location: ' . SITE_URL . '/user/login.php?error=' . urlencode($msg));
    exit;
}

// 1. Validate state
if (empty($_GET['state']) || $_GET['state'] !== ($_SESSION['google_state'] ?? '')) {
    oauthError('Invalid state. Please try again.');
}
unset($_SESSION['google_state']);

// 2. Check for error from Google
if (!empty($_GET['error'])) {
    oauthError('Google sign-in was cancelled.');
}

// 3. Exchange code for token
if (empty($_GET['code'])) {
    oauthError('No authorization code received.');
}

$tokens = exchangeGoogleCode($_GET['code']);
if (empty($tokens['access_token'])) {
    oauthError('Failed to get access token from Google.');
}

// 4. Get user info
$googleUser = getGoogleUserInfo($tokens['access_token']);
if (empty($googleUser['email'])) {
    oauthError('Failed to get user info from Google.');
}

$googleId = $googleUser['sub'];
$email    = $googleUser['email'];
$name     = $googleUser['name'] ?? $email;
$avatar   = $googleUser['picture'] ?? '';

// 5. Find existing user by google_id or email
$stmt = $pdo->prepare("SELECT * FROM tool_users WHERE google_id=? OR email=? LIMIT 1");
$stmt->execute([$googleId, $email]);
$user = $stmt->fetch();

if ($user) {
    // Update google_id if not set
    if (!$user['google_id']) {
        $pdo->prepare("UPDATE tool_users SET google_id=?, avatar=? WHERE id=?")
            ->execute([$googleId, $avatar, $user['id']]);
    }
    // Check if blocked
    if (!$user['status']) {
        oauthError('Your account has been suspended. Please contact support.');
    }
    // Update last login
    $pdo->prepare("UPDATE tool_users SET last_login=NOW() WHERE id=?")->execute([$user['id']]);
    loginUser($user);
    $redirect = $_SESSION['google_redirect'] ?? SITE_URL . '/user/dashboard.php';
    unset($_SESSION['google_redirect']);
    header('Location: ' . $redirect);
    exit;
}

// 6. New user — create account
$pdo->prepare("INSERT INTO tool_users (name, email, password, google_id, avatar, plan, status, email_verified) VALUES (?,?,?,?,?,'free',1,1)")
    ->execute([$name, $email, password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT), $googleId, $avatar]);

$userId = (int)$pdo->lastInsertId();

// Give 30 free credits
addCredits($userId, 30, 'Welcome bonus — Free plan (Google Sign-In)');

// Fetch and login
$newUser = $pdo->prepare("SELECT * FROM tool_users WHERE id=?");
$newUser->execute([$userId]);
loginUser($newUser->fetch());

$redirect = $_SESSION['google_redirect'] ?? SITE_URL . '/user/dashboard.php?welcome=1';
unset($_SESSION['google_redirect']);
header('Location: ' . $redirect);
exit;

<?php
require_once '../includes/config.php';
require_once '../includes/user-auth.php';
require_once '../includes/google-oauth.php';

if (isUserLoggedIn()) {
    header('Location: ' . SITE_URL . '/user/dashboard.php');
    exit;
}

// Store redirect destination in session
$_SESSION['google_redirect'] = $_GET['redirect'] ?? (SITE_URL . '/user/dashboard.php');
$_SESSION['google_state']    = bin2hex(random_bytes(16));

header('Location: ' . getGoogleAuthUrl($_SESSION['google_state']));
exit;

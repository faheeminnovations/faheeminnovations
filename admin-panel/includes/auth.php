<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ADMIN_ROOT = admin-panel/ directory
define('ADMIN_ROOT', dirname(__DIR__));
// SITE_ROOT = project root (one level above admin-panel/)
define('SITE_ROOT', dirname(ADMIN_ROOT));
require_once SITE_ROOT . '/includes/config.php';
require_once SITE_ROOT . '/includes/functions.php';

define('ADMIN_URL', SITE_URL . '/admin-panel');

function requireLogin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    $roles = ['editor' => 1, 'admin' => 2, 'super_admin' => 3];
    $userRole = $_SESSION['admin_role'] ?? 'editor';
    if (($roles[$userRole] ?? 0) < ($roles[$role] ?? 99)) {
        die('<p style="padding:40px;font-family:sans-serif">Access denied.</p>');
    }
}

function adminUser() {
    return [
        'id'   => $_SESSION['admin_id'] ?? null,
        'name' => $_SESSION['admin_name'] ?? '',
        'role' => $_SESSION['admin_role'] ?? '',
    ];
}

function isSuperAdmin() {
    return ($_SESSION['admin_role'] ?? '') === 'super_admin';
}

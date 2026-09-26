<?php
require_once '../includes/config.php';
require_once '../includes/user-auth.php';
logoutUser();
header('Location: ' . SITE_URL . '/user/login.php');
exit;

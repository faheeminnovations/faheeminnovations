<?php
// API endpoint for credit deduction from tool pages
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/user-auth.php';
require_once '../includes/credits.php';

if (!isUserLoggedIn()) {
    echo json_encode(['success'=>false,'error'=>'not_logged_in','message'=>'Please login to use this tool.']);
    exit;
}

$action    = $_POST['action'] ?? $_GET['action'] ?? '';
$userId    = $_SESSION['tool_user_id'];

if ($action === 'check') {
    $needed = (int)($_POST['credits'] ?? 10);
    $has    = getUserCredits($userId);
    echo json_encode(['success'=>true,'has'=>$has,'needed'=>$needed,'enough'=>$has>=$needed]);
    exit;
}

if ($action === 'deduct') {
    $toolSlug = $_POST['tool'] ?? 'unknown';
    $res      = (int)($_POST['resolution'] ?? 1080);
    $cost     = getCreditCost($toolSlug, $res);
    $desc     = $_POST['description'] ?? 'Tool usage: ' . $toolSlug;

    if (!checkCredits($userId, $cost)) {
        echo json_encode(['success'=>false,'error'=>'insufficient_credits','credits_needed'=>$cost,'credits_have'=>getUserCredits($userId)]);
        exit;
    }
    $ok = deductCredits($userId, $cost, $toolSlug, $desc);
    echo json_encode(['success'=>$ok,'credits_used'=>$cost,'credits_remaining'=>getUserCredits($userId)]);
    exit;
}

if ($action === 'status') {
    $user = getUser();
    echo json_encode([
        'success'  => true,
        'logged_in'=> true,
        'name'     => $user['name'],
        'plan'     => $user['plan'],
        'credits'  => getUserCredits($userId),
    ]);
    exit;
}

echo json_encode(['success'=>false,'error'=>'invalid_action']);

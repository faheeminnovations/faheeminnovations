<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid token.']);
    exit;
}

$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim($_POST['email'] ?? '');
$phone   = trim(strip_tags($_POST['phone'] ?? ''));
$company = trim(strip_tags($_POST['company'] ?? ''));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

if (!$name || !$email || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields correctly.']);
    exit;
}

try {
    // Save to DB
    $stmt = $pdo->prepare("INSERT INTO enquiries (name, email, phone, company, subject, message) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$name, $email, $phone, $company, $subject, $message]);

    $data = compact('name','email','phone','company','subject','message');

    // Send notification to admin (non-blocking — don't fail if email fails)
    @sendEnquiryNotification($data);

    // Send auto-reply to user (non-blocking)
    @sendEnquiryAutoReply($data);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}

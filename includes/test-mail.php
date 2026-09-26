<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';

echo "<h3>Testing SMTP...</h3>";

$result = sendMail(
    'hello@faheeminnovations.online',
    'Test',
    'Test Email from Faheem Innovations',
    '<p>This is a test email. If you receive this, SMTP is working!</p>'
);

if ($result) {
    echo "<p style='color:green'>✅ Email sent successfully!</p>";
} else {
    echo "<p style='color:red'>❌ Email failed. Check error log below.</p>";
}

// Also test DB save
echo "<h3>Testing DB save...</h3>";
try {
    $stmt = $pdo->prepare("INSERT INTO enquiries (name,email,phone,company,subject,message) VALUES (?,?,?,?,?,?)");
    $stmt->execute(['Test User','test@test.com','','','Test Subject','Test message']);
    echo "<p style='color:green'>✅ DB save works!</p>";
    // Clean up test record
    $pdo->exec("DELETE FROM enquiries WHERE email='test@test.com' AND subject='Test Subject'");
} catch(Exception $e) {
    echo "<p style='color:red'>❌ DB Error: " . $e->getMessage() . "</p>";
}

echo "<h3>PHP Error Log (last 20 lines):</h3><pre>";
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    $lines = file($logFile);
    echo htmlspecialchars(implode('', array_slice($lines, -20)));
} else {
    echo "Log file not found at: " . $logFile;
}
echo "</pre>";

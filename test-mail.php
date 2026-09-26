<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/phpmailer/PHPMailer.php';
require_once 'includes/phpmailer/SMTP.php';
require_once 'includes/phpmailer/Exception.php';

$configs = [
    ['host'=>'31.220.110.125', 'port'=>465, 'enc'=>PHPMailer::ENCRYPTION_SMTPS,  'label'=>'IP:465 SSL'],
    ['host'=>'31.220.110.125', 'port'=>587, 'enc'=>PHPMailer::ENCRYPTION_STARTTLS,'label'=>'IP:587 TLS'],
    ['host'=>'31.220.110.125', 'port'=>25,  'enc'=>'',                             'label'=>'IP:25 None'],
];

foreach ($configs as $cfg) {
    echo "<h4>Testing {$cfg['label']}...</h4>";
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug  = 0;
        $mail->isSMTP();
        $mail->Host       = $cfg['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hello@faheeminnovations.online';
        $mail->Password   = '8vvdJ5p6o>V*';
        $mail->Port       = $cfg['port'];
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 10;
        if ($cfg['enc']) $mail->SMTPSecure = $cfg['enc'];
        $mail->setFrom('hello@faheeminnovations.online','Faheem Innovations');
        $mail->addAddress('hello@faheeminnovations.online');
        $mail->Subject = 'SMTP Test - ' . $cfg['label'];
        $mail->Body    = 'Test from ' . $cfg['label'];
        $mail->send();
        echo "<p style='color:green;font-weight:700'>✅ SUCCESS with {$cfg['label']}</p>";
        break;
    } catch(Exception $e) {
        echo "<p style='color:red'>❌ Failed: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
    }
}
@unlink(__FILE__);
echo "<p style='color:#888;font-size:.8rem'>🗑️ Deleted.</p>";

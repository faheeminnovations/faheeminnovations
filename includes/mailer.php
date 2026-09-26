<?php
// Mail Configuration
define('SMTP_HOST',      'mail.faheeminnovations.online');
define('SMTP_PORT',      465);
define('SMTP_USER',      'hello@faheeminnovations.online');
define('SMTP_PASS',      '8vvdJ5p6o>V*');
define('SMTP_FROM',      'hello@faheeminnovations.online');
define('SMTP_FROM_NAME', 'Faheem Innovations');
define('SMTP_TO',        'hello@faheeminnovations.online');

require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
require_once __DIR__ . '/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Detect if running on live server or localhost
$isLive = (strpos(SITE_URL, 'localhost') === false && strpos(SITE_URL, '127.0.0.1') === false);

function sendMail($toEmail, $toName, $subject, $htmlBody, $replyTo = '') {
    global $isLive;

    if ($isLive) {
        // Live server — use PHPMailer SMTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            if ($replyTo) $mail->addReplyTo($replyTo);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    } else {
        // Localhost — use PHP mail()
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
        if ($replyTo) $headers .= "Reply-To: $replyTo\r\n";
        return mail($toEmail, $subject, $htmlBody, $headers);
    }
}
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            if ($replyTo) $mail->addReplyTo($replyTo);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    } else {
        // Localhost — use PHP mail() (XAMPP sendmail)
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
        if ($replyTo) $headers .= "Reply-To: $replyTo\r\n";
        return mail($toEmail, $subject, $htmlBody, $headers);
    }
}

function sendEnquiryNotification($data) {
    $html = '
    <div style="font-family:Inter,sans-serif;max-width:600px;margin:0 auto;background:#f8fafc;padding:20px">
      <div style="background:#0B3C33;padding:24px;border-radius:12px 12px 0 0;text-align:center">
        <h2 style="color:#fff;margin:0;font-size:1.3rem">📩 New Enquiry Received</h2>
      </div>
      <div style="background:#fff;padding:28px;border-radius:0 0 12px 12px;border:1px solid #e2e8f0">
        <table style="width:100%;border-collapse:collapse">
          <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;width:140px;color:#64748b;font-weight:600">Name</td><td style="padding:10px 0;border-bottom:1px solid #f1f5f9">' . htmlspecialchars($data['name']) . '</td></tr>
          <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600">Email</td><td style="padding:10px 0;border-bottom:1px solid #f1f5f9"><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></td></tr>
          <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600">Phone</td><td style="padding:10px 0;border-bottom:1px solid #f1f5f9">' . htmlspecialchars($data['phone'] ?: '—') . '</td></tr>
          <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600">Company</td><td style="padding:10px 0;border-bottom:1px solid #f1f5f9">' . htmlspecialchars($data['company'] ?: '—') . '</td></tr>
          <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600">Subject</td><td style="padding:10px 0;border-bottom:1px solid #f1f5f9">' . htmlspecialchars($data['subject']) . '</td></tr>
          <tr><td style="padding:10px 0;color:#64748b;font-weight:600;vertical-align:top">Message</td><td style="padding:10px 0">' . nl2br(htmlspecialchars($data['message'])) . '</td></tr>
        </table>
        <div style="margin-top:24px;text-align:center">
          <a href="' . SITE_URL . '/admin-panel/enquiries/" style="background:#0B3C33;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block">View in Admin Panel</a>
        </div>
      </div>
      <p style="text-align:center;font-size:.78rem;color:#94a3b8;margin-top:16px">Faheem Innovations — ' . date('d M Y, H:i') . '</p>
    </div>';

    return sendMail(SMTP_TO, 'Faheem Innovations', '📩 New Enquiry: ' . $data['subject'], $html, $data['email']);
}

function sendEnquiryAutoReply($data) {
    $html = '
    <div style="font-family:Inter,sans-serif;max-width:600px;margin:0 auto;background:#f8fafc;padding:20px">
      <div style="background:#0B3C33;padding:24px;border-radius:12px 12px 0 0;text-align:center">
        <h2 style="color:#fff;margin:0;font-size:1.3rem">Thank You for Contacting Us!</h2>
      </div>
      <div style="background:#fff;padding:28px;border-radius:0 0 12px 12px;border:1px solid #e2e8f0">
        <p style="margin:0 0 16px">Hi <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
        <p style="color:#64748b;line-height:1.7">Thank you for reaching out to Faheem Innovations. We have received your enquiry and our team will get back to you within <strong>24 hours</strong>.</p>
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:16px;margin:20px 0">
          <p style="margin:0;font-size:.9rem;color:#166534"><strong>Your enquiry:</strong> ' . htmlspecialchars($data['subject']) . '</p>
        </div>
        <p style="color:#64748b;line-height:1.7">Meanwhile, feel free to explore our services and products at <a href="' . SITE_URL . '" style="color:#0B3C33;font-weight:600">faheeminnovations.online</a>.</p>
        <div style="margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;font-size:.85rem;color:#94a3b8">
          <p style="margin:4px 0">📧 hello@faheeminnovations.online</p>
          <p style="margin:4px 0">📱 0304-1277320</p>
          <p style="margin:4px 0">📍 Depalpur, Pakistan</p>
        </div>
      </div>
    </div>';

    return sendMail($data['email'], $data['name'], 'We received your enquiry — Faheem Innovations', $html);
}

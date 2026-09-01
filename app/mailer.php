<?php
use PHPMailer\PHPMailer\PHPMailer;

function send_form_copy(string $subject, array $fields, string $recipient): bool {
    $autoload = ROOT_PATH . '/vendor/autoload.php';
    if (!is_readable($autoload)) {
        error_log('Email delivery skipped: PHPMailer is not installed. Run composer install or upload the vendor directory.');
        return false;
    }

    require_once $autoload;
    $body = '<p>Thank you for contacting Ellens Florist. We have received your message and will respond during our hours (09:00–20:00 WITA, UTC+8).</p><table>';
    foreach ($fields as $label => $value) {
        $body .= '<tr><th>' . e($label) . '</th><td>' . nl2br(e((string) $value)) . '</td></tr>';
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_USER');
        $mail->Password = env('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = (int) env('SMTP_PORT', '465');
        $mail->setFrom(env('SMTP_FROM'), 'Ellens Florist');
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->isHTML();
        $mail->Body = $body . '</table>';
        $mail->AltBody = strip_tags($body);
        $mail->send();
        return true;
    } catch (Throwable $e) {
        error_log('SMTP delivery failed for ' . $recipient . ': ' . $e->getMessage());
        return false;
    }
}

function notify_submission(string $subject, array $fields, string $customer): bool {
    $customer_sent = send_form_copy($subject, $fields, $customer);
    $support_sent = send_form_copy($subject, $fields, (string) env('SUPPORT_EMAIL'));
    return $customer_sent && $support_sent;
}

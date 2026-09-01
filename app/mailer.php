<?php
use PHPMailer\PHPMailer\PHPMailer;

function email_field_value(string $label, $value): string {
    $value = trim((string) $value);
    if (strtolower($label) === 'wedding date' && $value !== '') {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date) return strtoupper($date->format('d M Y'));
    }
    return $value === '' ? '—' : $value;
}

function email_message_html(array $fields): string {
    $rows = '';
    foreach ($fields as $label => $value) {
        $rows .= '<tr><td style="padding:13px 18px;border-bottom:1px solid #eee8e0;color:#8f8a83;font:700 11px Arial,Helvetica,sans-serif;letter-spacing:1px;text-transform:uppercase;vertical-align:top;width:34%;">' . e($label) . '</td><td style="padding:13px 18px;border-bottom:1px solid #eee8e0;color:#2b2b2b;font:15px/1.55 Arial,Helvetica,sans-serif;vertical-align:top;">' . nl2br(e(email_field_value($label, $value))) . '</td></tr>';
    }

    return '<!doctype html><html lang="en"><body style="margin:0;padding:0;background:#f3ede6;">'
        . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">Your message has been received by Ellens Florist.</div>'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3ede6;margin:0;padding:32px 12px;"><tr><td align="center">'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;background:#ffffff;border-collapse:separate;border-spacing:0;border-radius:14px;overflow:hidden;box-shadow:0 12px 32px rgba(43,43,43,.12);">'
        . '<tr><td align="center" style="padding:30px 24px 24px;background:#faf8f5;border-bottom:3px solid #e0b84f;"><img src="https://ellensflorist.com/assets/images/logo.png" width="62" height="62" alt="Ellens Florist" style="display:block;border:0;border-radius:50%;margin:0 auto 12px;"><div style="color:#111111;font:28px Georgia,serif;line-height:1.1;">Ellens Florist</div><div style="margin-top:7px;color:#8f8a83;font:10px Arial,Helvetica,sans-serif;letter-spacing:2px;text-transform:uppercase;">Blooms that tell your story</div></td></tr>'
        . '<tr><td style="padding:32px 28px 12px;color:#2b2b2b;font:16px/1.65 Arial,Helvetica,sans-serif;"><h1 style="margin:0 0 12px;color:#111111;font:29px Georgia,serif;font-weight:400;">Thank you for reaching out.</h1><p style="margin:0;">Thank you for contacting Ellens Florist. We have received your message and will respond during our hours during 09:00–20:00 WITA (UTC+8).</p></td></tr>'
        . '<tr><td style="padding:18px 28px 30px;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #eee8e0;border-radius:8px;border-collapse:separate;border-spacing:0;overflow:hidden;background:#ffffff;">' . $rows . '</table></td></tr>'
        . '<tr><td align="center" style="padding:22px 28px;background:#2b2b2b;color:#f3ede6;font:12px/1.6 Arial,Helvetica,sans-serif;"><strong style="color:#e0b84f;font:16px Georgia,serif;font-weight:400;">Ellens Florist</strong><br>Wedding florals &middot; Event decoration &middot; Bali, Indonesia<br><a href="https://ellensflorist.com" style="color:#f3ede6;text-decoration:underline;">ellensflorist.com</a></td></tr>'
        . '</table></td></tr></table></body></html>';
}

function send_form_copy(string $subject, array $fields, string $recipient, array $attachments = []): bool {
    $autoload = ROOT_PATH . '/vendor/autoload.php';
    if (!is_readable($autoload)) {
        error_log('Email delivery skipped: PHPMailer is not installed. Run composer install or upload the vendor directory.');
        return false;
    }

    require_once $autoload;
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_USER');
        $mail->Password = env('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = (int) env('SMTP_PORT', '465');
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->setFrom(env('SMTP_FROM'), 'Ellens Florist');
        if (filter_var($fields['Email'] ?? '', FILTER_VALIDATE_EMAIL) && $recipient !== $fields['Email']) $mail->addReplyTo($fields['Email']);
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->isHTML();
        $mail->Body = email_message_html($fields);
        $mail->AltBody = "Thank you for contacting Ellens Florist. We have received your message and will respond during our hours during 09:00–20:00 WITA (UTC+8).\n\n" . implode("\n", array_map(static fn ($label, $value) => $label . ': ' . email_field_value($label, $value), array_keys($fields), $fields));
        foreach ($attachments as $attachment) {
            $path = $attachment['path'] ?? '';
            if (is_readable($path)) $mail->addAttachment($path, $attachment['name'] ?? basename($path));
        }
        $mail->send();
        return true;
    } catch (Throwable $e) {
        error_log('SMTP delivery failed for ' . $recipient . ': ' . $e->getMessage());
        return false;
    }
}

function notify_submission(string $subject, array $fields, string $customer, array $attachments = []): bool {
    $customer_sent = send_form_copy($subject, $fields, $customer, $attachments);
    $support_sent = send_form_copy($subject, $fields, (string) env('SUPPORT_EMAIL'), $attachments);
    return $customer_sent && $support_sent;
}

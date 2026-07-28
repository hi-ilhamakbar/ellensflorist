<?php
use PHPMailer\PHPMailer\PHPMailer;
function send_form_copy(string $subject, array $fields, string $recipient): bool {
    $autoload = ROOT_PATH . '/vendor/autoload.php'; if (!is_readable($autoload)) return false; require_once $autoload;
    $body='<p>Thank you for contacting Ellen’s Florist. We have received your message and will respond during our hours (09:00–20:00).</p><table>';
    foreach($fields as $label=>$value) $body.='<tr><th>'.e($label).'</th><td>'.nl2br(e((string)$value)).'</td></tr>';
    try {$m=new PHPMailer(true);$m->isSMTP();$m->Host=env('SMTP_HOST');$m->SMTPAuth=true;$m->Username=env('SMTP_USER');$m->Password=env('SMTP_PASS');$m->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;$m->Port=(int)env('SMTP_PORT','465');$m->setFrom(env('SMTP_FROM'),"Ellen's Florist");$m->addAddress($recipient);$m->Subject=$subject;$m->isHTML();$m->Body=$body.'</table>';$m->AltBody=strip_tags($body);$m->send();return true;}catch(Throwable $e){error_log($e->getMessage());return false;}
}
function notify_submission(string $subject,array $fields,string $customer):void {send_form_copy($subject,$fields,$customer);send_form_copy($subject,$fields,env('SUPPORT_EMAIL'));}

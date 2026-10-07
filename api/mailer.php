<?php
/**
 * mailer.php — sends booking confirmation emails via PHPMailer/SMTP.
 *
 * Uses Composer's autoloader (composer.json already requires
 * phpmailer/phpmailer). Run `composer install` once on the server before
 * deploying.
 */

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Send the booking confirmation email over SMTP using PHPMailer.
 *
 * @return array{sent: bool, error: string}
 */
function sendBookingEmail(array $secrets, string $platformName, string $toEmail, string $toName, array $vars): array {
    if (empty($secrets['smtpHost']) || empty($secrets['smtpUser']) || empty($secrets['smtpPass'])) {
        return ['sent' => false, 'error' => 'SMTP configuration is incomplete. Set it up in the admin panel.'];
    }

    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        return [
            'sent' => false,
            'error' => 'PHPMailer is not installed. Run "composer install" on the server.'
        ];
    }

    $template = $secrets['emailTemplate'] ?? getDefaultSecretSettings()['emailTemplate'];
    $htmlBody = strtr($template, [
        '{{name}}' => $vars['name'],
        '{{platformName}}' => $platformName,
        '{{machine}}' => $vars['machine'],
        '{{symptom}}' => $vars['symptom'],
        '{{location}}' => $vars['location'],
        '{{datetime}}' => $vars['datetime'],
        '{{phone}}' => $vars['phone'],
        '{{email}}' => $toEmail
    ]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $secrets['smtpHost'];
        $mail->SMTPAuth = true;
        $mail->Username = $secrets['smtpUser'];
        $mail->Password = $secrets['smtpPass'];
        $port = (int) ($secrets['smtpPort'] ?: 587);
        $mail->Port = $port;
        $mail->SMTPSecure = $port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom($secrets['smtpUser'], $platformName);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = "Repair Scheduled: Booking Confirmed at {$platformName}";
        $mail->Body = $htmlBody;

        $mail->send();
        return ['sent' => true, 'error' => ''];
    } catch (PHPMailerException $e) {
        error_log('Error sending email via PHPMailer: ' . $mail->ErrorInfo);
        return ['sent' => false, 'error' => 'Could not send the confirmation email.'];
    } catch (Throwable $e) {
        error_log('Error sending email via PHPMailer: ' . $e->getMessage());
        return ['sent' => false, 'error' => 'Could not send the confirmation email.'];
    }
}

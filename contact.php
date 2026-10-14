
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=UTF-8');

// Load PHPMailer from the root folder.
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (
    $name === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $subject === '' ||
    $message === ''
) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please fill in all fields correctly.'
    ]);
    exit;
}

$mail = new PHPMailer(true);

try {
    // Gmail SMTP configuration.
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'online9702@gmail.com';

    // Enter your NEW Gmail App Password here.
    $mail->Password   = 'qvyo lhxu ccxb lmwv';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(
        'online9702@gmail.com',
        'Orovecta Contact Form'
    );

    $mail->addAddress('contact@orovecta.com');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Orovecta Contact: ' . $subject;

$mail->Body = '
<div style="margin:0; padding:30px 15px; background-color:#f3f5f8; font-family:Arial, Helvetica, sans-serif; color:#333333;">

    <div style="max-width:650px; margin:0 auto; background-color:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e7eb;">

        <!-- Header -->
        <div style="background-color:#10243a; padding:28px 30px; text-align:center;">
            <h1 style="margin:0; font-size:26px; letter-spacing:2px; color:#d4a900;">
                OROVECTA
            </h1>
            <p style="margin:8px 0 0; font-size:13px; color:#dce3eb;">
                Industrial Lubrication. Engineered for Performance.
            </p>
        </div>

        <!-- Gold Accent -->
        <div style="height:4px; background-color:#d4a900;"></div>

        <!-- Main Content -->
        <div style="padding:30px;">

            <h2 style="margin:0 0 10px; font-size:21px; color:#10243a;">
                New Contact Inquiry
            </h2>

            <p style="margin:0 0 25px; font-size:14px; line-height:1.7; color:#667085;">
                You have received a new inquiry through the Orovecta website.
                Please find the contact details below.
            </p>

            <!-- Contact Details -->
            <table role="presentation" style="width:100%; border-collapse:collapse; font-size:14px;">

                <tr>
                    <td style="width:115px; padding:13px 12px; background-color:#f8fafc; border:1px solid #e5e7eb; font-weight:bold; color:#10243a;">
                        Name
                    </td>
                    <td style="padding:13px 12px; border:1px solid #e5e7eb; color:#444444;">
                        ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '
                    </td>
                </tr>

                <tr>
                    <td style="padding:13px 12px; background-color:#f8fafc; border:1px solid #e5e7eb; font-weight:bold; color:#10243a;">
                        Email
                    </td>
                    <td style="padding:13px 12px; border:1px solid #e5e7eb; color:#444444; word-break:break-word;">
                        <a href="mailto:' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '" style="color:#b08b00; text-decoration:none;">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</a>
                    </td>
                </tr>

                <tr>
                    <td style="padding:13px 12px; background-color:#f8fafc; border:1px solid #e5e7eb; font-weight:bold; color:#10243a;">
                        Subject
                    </td>
                    <td style="padding:13px 12px; border:1px solid #e5e7eb; color:#444444;">
                        ' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '
                    </td>
                </tr>

            </table>

            <!-- Message -->
            <div style="margin-top:25px;">
                <h3 style="margin:0 0 12px; font-size:16px; color:#10243a;">
                    Message Details
                </h3>

                <div style="padding:18px; background-color:#f8fafc; border-left:4px solid #d4a900; border-radius:4px; font-size:14px; line-height:1.8; color:#444444; overflow-wrap:anywhere;">
                    ' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '
                </div>
            </div>

            <p style="margin:25px 0 0; font-size:13px; color:#667085; line-height:1.7;">
                Please review this inquiry and respond to the sender as appropriate.
            </p>

        </div>

        <!-- Footer -->
        <div style="padding:18px 25px; background-color:#10243a; text-align:center;">
            <p style="margin:0; font-size:12px; line-height:1.7; color:#dce3eb;">
                This email was automatically generated from the Orovecta website contact form.
            </p>
            <p style="margin:6px 0 0; font-size:12px; color:#d4a900;">
                &copy; ' . date('Y') . ' Orovecta. All rights reserved.
            </p>
        </div>

    </div>
</div>
';

    $mail->AltBody =
        "Name: $name\nEmail: $email\nSubject: $subject\n\n$message";

    $mail->send();

    echo json_encode([
        'status' => 'success',
        'message' => 'Your message has been sent successfully. Thank you! Our team will contact you shortly.'
    ]);

} catch (Exception $e) {
    error_log('Contact email error: ' . $mail->ErrorInfo);

    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => 'Your message could not be sent. Please try again later.'
    ]);
}

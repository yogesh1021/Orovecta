
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer manually.
// Expected folder: project/PHPMailer/src/
$phpMailerPath = __DIR__ . '/PHPMailer/';

if (file_exists($phpMailerPath . 'src/PHPMailer.php')) {
    $phpMailerPath .= 'src/';
}

$requiredFiles = [
    $phpMailerPath . 'Exception.php',
    $phpMailerPath . 'PHPMailer.php',
    $phpMailerPath . 'SMTP.php'
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        http_response_code(500);
        exit('PHPMailer file not found: ' .
            htmlspecialchars(basename($file), ENT_QUOTES, 'UTF-8'));
    }
}

require_once $requiredFiles[0];
require_once $requiredFiles[1];
require_once $requiredFiles[2];

// Only accept form submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inquiry.html');
    exit;
}

// Collect form values.
$name    = trim($_POST['name'] ?? '');
$mobile  = trim($_POST['mobile'] ?? '');
$email   = trim($_POST['email'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate required fields.
if (
    $name === '' ||
    $mobile === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $service === '' ||
    $message === ''
) {
    http_response_code(400);
    exit('Please complete all required fields correctly.');
}

// Validate field lengths.
if (
    strlen($name) > 150 ||
    strlen($mobile) > 30 ||
    strlen($email) > 254 ||
    strlen($service) > 150 ||
    strlen($message) > 10000
) {
    http_response_code(400);
    exit('One or more fields exceed the allowed length.');
}

// Validate mobile number.
if (!preg_match('/^[0-9+() \-]{10,30}$/', $mobile)) {
    http_response_code(400);
    exit('Please enter a valid mobile number.');
}

// Validate service selection.
$allowedServices = [
    'Metalworking Fluids',
    'Specialty Lubricants',
    'MRO Solutions',
    'MRO Lubricants',
    'Customized Lubrication',
    'Customized Lubrication Solutions',
    'Other'
];

if (!in_array($service, $allowedServices, true)) {
    http_response_code(400);
    exit('Please select a valid service or product.');
}

// Gmail configuration.
$smtpUsername = 'online9702@gmail.com';

// IMPORTANT:
// Replace this placeholder with your NEW Google App Password.
// Do not use your regular Gmail password.
$smtpPassword = 'qvyo lhxu ccxb lmwv';

$mail = new PHPMailer(true);

try {
    // SMTP configuration.
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->Timeout    = 20;

    // SMTP debugging is written to Apache's error log.
    // Do not display SMTP conversations to website visitors.
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function ($str, $level) {
        error_log("Orovecta SMTP DEBUG [$level]: $str");
    };

    // Sender and recipient.
    $mail->setFrom(
        $smtpUsername,
        'Orovecta Website Inquiry'
    );

    $mail->addAddress('contact@orovecta.com');
    $mail->addReplyTo($email, $name);

    // Safely escape user-submitted values for HTML.
    $safeName = htmlspecialchars(
        $name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'
    );

    $safeMobile = htmlspecialchars(
        $mobile, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'
    );

    $safeEmail = htmlspecialchars(
        $email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'
    );

    $safeService = htmlspecialchars(
        $service, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'
    );

    $safeMessage = nl2br(htmlspecialchars(
        $message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'
    ));

    // Email subject and HTML body.
    $mail->isHTML(true);
    $mail->Subject = 'New Product & Service Inquiry - Orovecta';

$mail->Body = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>New Inquiry - Orovecta</title>
</head>

<body style='margin:0;padding:25px 10px;background-color:#f3f5f8;font-family:Arial,Helvetica,sans-serif;color:#333333;'>

    <div style='max-width:650px;margin:0 auto;background-color:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;'>

        <!-- Header -->
        <div style='background-color:#10243a;padding:28px 20px;text-align:center;'>
            <h1 style='margin:0;font-size:27px;letter-spacing:2px;color:#d4a900;'>
                OROVECTA
            </h1>

            <p style='margin:8px 0 0;font-size:13px;line-height:1.6;color:#dce3eb;'>
                Industrial Lubrication. Engineered for Performance.
            </p>
        </div>

        <!-- Gold Divider -->
        <div style='height:4px;background-color:#d4a900;'></div>

        <!-- Main Content -->
        <div style='padding:28px 25px;'>

            <h2 style='margin:0 0 12px;font-size:22px;color:#10243a;'>
                New Website Inquiry
            </h2>

            <p style='margin:0 0 24px;font-size:14px;line-height:1.8;color:#667085;'>
                A visitor has submitted an inquiry through the Orovecta website.
                Please find the contact information and message below.
            </p>

            <!-- Contact Details -->
            <table role='presentation' cellpadding='0' cellspacing='0'
                   style='width:100%;border-collapse:collapse;font-size:14px;'>

                <tr>
                    <td style='width:38%;padding:13px 12px;background-color:#f8fafc;border:1px solid #e5e7eb;font-weight:bold;color:#10243a;'>
                        Full Name
                    </td>
                    <td style='padding:13px 12px;border:1px solid #e5e7eb;color:#444444;overflow-wrap:anywhere;'>
                        {$safeName}
                    </td>
                </tr>

                <tr>
                    <td style='padding:13px 12px;background-color:#f8fafc;border:1px solid #e5e7eb;font-weight:bold;color:#10243a;'>
                        Mobile Number
                    </td>
                    <td style='padding:13px 12px;border:1px solid #e5e7eb;color:#444444;'>
                        {$safeMobile}
                    </td>
                </tr>

                <tr>
                    <td style='padding:13px 12px;background-color:#f8fafc;border:1px solid #e5e7eb;font-weight:bold;color:#10243a;'>
                        Email Address
                    </td>
                    <td style='padding:13px 12px;border:1px solid #e5e7eb;overflow-wrap:anywhere;'>
                        {$safeEmail}
                    </td>
                </tr>

                <tr>
                    <td style='padding:13px 12px;background-color:#f8fafc;border:1px solid #e5e7eb;font-weight:bold;color:#10243a;'>
                        Service / Product
                    </td>
                    <td style='padding:13px 12px;border:1px solid #e5e7eb;color:#444444;overflow-wrap:anywhere;'>
                        {$safeService}
                    </td>
                </tr>

            </table>

            <!-- Message Section -->
            <div style='margin-top:25px;'>

                <h3 style='margin:0 0 12px;font-size:17px;color:#10243a;'>
                    Message Details
                </h3>

                <div style='padding:18px;background-color:#f8fafc;border-left:4px solid #d4a900;border-radius:4px;font-size:14px;line-height:1.8;color:#444444;overflow-wrap:anywhere;'>
                    {$safeMessage}
                </div>

            </div>

            <!-- Closing Note -->
            <p style='margin:25px 0 0;font-size:13px;line-height:1.8;color:#667085;'>
                Please review this inquiry and contact the visitor for any
                required clarification or further discussion.
            </p>

        </div>

        <!-- Footer -->
        <div style='padding:20px 20px;background-color:#10243a;text-align:center;'>

            <p style='margin:0;font-size:16px;font-weight:bold;letter-spacing:1px;color:#d4a900;'>
                OROVECTA
            </p>

            <p style='margin:7px 0;font-size:12px;line-height:1.7;color:#dce3eb;'>
                Industrial Lubrication. Engineered for Performance.
            </p>

            <div style='height:1px;background-color:#405367;margin:15px 0;'></div>

            <p style='margin:0;font-size:11px;line-height:1.7;color:#b8c5d3;'>
                This is an automated notification from the Orovecta website contact form.
            </p>

            <p style='margin:8px 0 0;font-size:11px;color:#d4a900;'>
                &copy; " . date('Y') . " Orovecta. All rights reserved.
            </p>

        </div>

    </div>

</body>
</html>";

    // Plain-text alternative.
    $mail->AltBody =
        "New Orovecta Inquiry\n\n" .
        "Name: $name\n" .
        "Mobile: $mobile\n" .
        "Email: $email\n" .
        "Service: $service\n\n" .
        "Message:\n$message";

    // Send the email.
    $mail->send();

    // Redirect back to the current page after successful SMTP submission.
$redirectPage = $_SERVER['HTTP_REFERER'] ?? 'index.html';

header('Location: ' . $redirectPage . '?status=success#inquiry');
exit;

} catch (Exception $e) {
    // Record the actual error privately.
    error_log('Orovecta email error: ' . $mail->ErrorInfo);

    http_response_code(500);

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Inquiry Error | Orovecta</title>
        <link href="assets/vendor/bootstrap/css/bootstrap.min.css"
              rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger">
                <h4>Unable to Send Inquiry</h4>
                <p>Your inquiry could not be sent. Please try again later.</p>
                <p>The technical details have been recorded in the
                   Apache error log.</p>
                <a href="inquiry.html#inquiry"
                   class="btn btn-primary">Back to Inquiry Form</a>
            </div>
        </div>
    </body>
    </html>';

    exit;
}

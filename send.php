<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include required PHPMailer files (since all are in the same folder)
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

// Function to determine rating based on percentage
function getRating($percent) {
    if ($percent >= 91) {
        return 'Excellent';
    } elseif ($percent >= 76) {
        return 'Good';
    } elseif ($percent >= 60) {
        return 'Average';
    } else {
        return 'Need attention';
    }
}

// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method.';
    exit;
}

// Get POST data
$eqPercent = isset($_POST['eqPercent']) ? floatval($_POST['eqPercent']) : 0;
$iqPercent = isset($_POST['iqPercent']) ? floatval($_POST['iqPercent']) : 0;
$fqPercent = isset($_POST['fqPercent']) ? floatval($_POST['fqPercent']) : 0;
$sqPercent = isset($_POST['sqPercent']) ? floatval($_POST['sqPercent']) : 0;
$recommendations = isset($_POST['recommendations']) ? $_POST['recommendations'] : '';
$pieChart = isset($_POST['pieChart']) ? $_POST['pieChart'] : '';
$barChart = isset($_POST['barChart']) ? $_POST['barChart'] : '';
$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
$user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Server settings - using environment variables for flexibility
    $mail->isSMTP();                                // Send using SMTP
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth   = true;                       // Enable SMTP authentication
    $mail->Username   = getenv('SMTP_USERNAME') ?: 'mywork3410@gmail.com'; // SMTP username
    $mail->Password   = getenv('SMTP_PASSWORD') ?: 'xfqxvnqjnwemefek'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS encryption
    $mail->Port       = getenv('SMTP_PORT') ?: 587; // SMTP port

    $mail->setFrom('mywork3410@gmail.com', 'MQS Quiz');
    $mail->isHTML(true);                            // Email format as HTML

    // Build HTML body for user
    $userBody = '<h2>Your MQS Quiz Results</h2>';
    $userBody .= '<p>Dear ' . htmlspecialchars($user_name) . ',</p>';
    $userBody .= '<h3>Performance Table</h3>';
    $userBody .= '<table border="1" style="border-collapse: collapse;">';
    $userBody .= '<tr><th>Category</th><th>Percentage</th><th>Rating</th></tr>';
    $userBody .= '<tr><td>Emotional Quotient (EQ)</td><td>' . number_format($eqPercent, 1) . '%</td><td>' . getRating($eqPercent) . '</td></tr>';
    $userBody .= '<tr><td>Intelligence Quotient (IQ)</td><td>' . number_format($iqPercent, 1) . '%</td><td>' . getRating($iqPercent) . '</td></tr>';
    $userBody .= '<tr><td>Financial Quotient (FQ)</td><td>' . number_format($fqPercent, 1) . '%</td><td>' . getRating($fqPercent) . '</td></tr>';
    $userBody .= '<tr><td>Social Quotient (SQ)</td><td>' . number_format($sqPercent, 1) . '%</td><td>' . getRating($sqPercent) . '</td></tr>';
    $userBody .= '</table>';

    if ($pieChart) {
        $userBody .= '<h3>Quotient Distribution Chart</h3>';
        $userBody .= '<img src="' . $pieChart . '" alt="Pie Chart" />';
    }

    if ($barChart) {
        $userBody .= '<h3>Category Scores Chart</h3>';
        $userBody .= '<img src="' . $barChart . '" alt="Bar Chart" />';
    }

    $userBody .= '<h3>Recommendations</h3>';
    $formattedRecommendations = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($recommendations));
    $userBody .= '<p>' . nl2br($formattedRecommendations) . '</p>';
    $userBody .= '<p>Want to connect to Expert? WhatsApp us at 9999633753</p>';
$userBody .= '<p>Thank you for taking the MQS Quiz!</p>';

    // Send email to user
    $mail->clearAddresses();
    $mail->addAddress($user_email, $user_name);
    $mail->Subject = 'Your MQS Quiz Results';
    $mail->Body = $userBody;
    $mail->send();

    // Send email to admin
    $adminBody = '<h2>New MQS Quiz Submission</h2>';
    $adminBody .= '<p><strong>User Name:</strong> ' . htmlspecialchars($user_name) . '</p>';
    $adminBody .= '<p><strong>User Email:</strong> ' . htmlspecialchars($user_email) . '</p>';
    $adminBody .= '<h3>Results</h3>';
    $adminBody .= '<table border="1" style="border-collapse: collapse;">';
    $adminBody .= '<tr><th>Category</th><th>Percentage</th><th>Rating</th></tr>';
    $adminBody .= '<tr><td>Emotional Quotient (EQ)</td><td>' . number_format($eqPercent, 1) . '%</td><td>' . getRating($eqPercent) . '</td></tr>';
    $adminBody .= '<tr><td>Intelligence Quotient (IQ)</td><td>' . number_format($iqPercent, 1) . '%</td><td>' . getRating($iqPercent) . '</td></tr>';
    $adminBody .= '<tr><td>Financial Quotient (FQ)</td><td>' . number_format($fqPercent, 1) . '%</td><td>' . getRating($fqPercent) . '</td></tr>';
    $adminBody .= '<tr><td>Social Quotient (SQ)</td><td>' . number_format($sqPercent, 1) . '%</td><td>' . getRating($sqPercent) . '</td></tr>';
    $adminBody .= '</table>';

    if ($pieChart) {
        $adminBody .= '<h3>Quotient Distribution Chart</h3>';
        $adminBody .= '<img src="' . $pieChart . '" alt="Pie Chart" />';
    }

    if ($barChart) {
        $adminBody .= '<h3>Category Scores Chart</h3>';
        $adminBody .= '<img src="' . $barChart . '" alt="Bar Chart" />';
    }

    $adminBody .= '<h3>Recommendations</h3>';
    $adminBody .= '<p>' . nl2br($formattedRecommendations) . '</p>';

    $mail->clearAddresses();
    $mail->addAddress('pvvraj1234433@gmail.com', 'Admin');
    $mail->Subject = 'New MQS Quiz Submission';
    $mail->Body = $adminBody;
    $mail->send();

    echo 'Emails sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>

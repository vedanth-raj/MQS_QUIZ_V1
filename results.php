<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include required PHPMailer files (since all are in the same folder)
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method.';
    exit;
}

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

// Build HTML for results
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
$userBody .= '<p>Thank you for taking the MQS Quiz!</p>';

// Display the results with UI matching quiz.html
echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>MQS Quiz Results</title>';
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
echo '<style>';
echo '* { margin: 0; padding: 0; box-sizing: border-box; }';
echo 'body { font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; min-height: 100vh; display: flex; justify-content: center; align-items: center; position: relative; overflow-x: hidden; background-size: cover; background-position: center; background-repeat: no-repeat; background-color: #f0f2f5; }';
echo '.floating-shapes { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }';
echo '.floating-shape { position: absolute; opacity: 0.1; animation: float 20s infinite linear; }';
echo '.shape-1 { top: 20%; left: 10%; width: 60px; height: 60px; background: #ff6b6b; border-radius: 50%; animation-delay: 0s; }';
echo '.shape-2 { top: 60%; left: 80%; width: 40px; height: 40px; background: #4ecdc4; border-radius: 20%; animation-delay: 5s; }';
echo '.shape-3 { top: 30%; left: 70%; width: 50px; height: 50px; background: #45b7d1; transform: rotate(45deg); animation-delay: 10s; }';
echo '.shape-4 { top: 80%; left: 20%; width: 70px; height: 70px; background: #f7b731; border-radius: 30%; animation-delay: 15s; }';
echo '@keyframes float { 0% { transform: translateY(0px) rotate(0deg); } 33% { transform: translateY(-30px) rotate(120deg); } 66% { transform: translateY(20px) rotate(240deg); } 100% { transform: translateY(0px) rotate(360deg); } }';
echo '.container { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-radius: 25px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2); padding: 40px; width: 95%; max-width: 1000px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.3); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; z-index: 10; }';
echo '.container:hover { transform: translateY(-8px) scale(1.02); box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3); }';
echo 'h1 { color: #ff6b6b; font-size: 2.5em; margin-bottom: 30px; text-shadow: 0 0 30px rgba(255, 255, 255, 0.5); font-weight: 700; }';
echo '.results-table { margin: 25px 0; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 20px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3); width: 100%; }';
echo '.results-table table { width: 100%; border-collapse: collapse; margin-top: 10px; }';
echo '.results-table th, .results-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.3); }';
echo '.results-table th { background: linear-gradient(45deg, #667eea, #764ba2); color: white; font-weight: bold; }';
echo '.results-table td { color: #2c3e50; font-weight: 600; }';
echo '#charts-container { display: flex; justify-content: space-around; margin: 20px 0; flex-wrap: wrap; }';
echo '.recommendation { font-size: 1.1em; color: #2c3e50; margin: 20px 0; line-height: 1.5; text-align: left; background: rgba(255, 255, 255, 0.8); padding: 15px; border-radius: 10px; border-left: 4px solid #ff6b6b; }';
echo 'button { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 15px 30px; border: none; border-radius: 25px; font-size: 1.1em; font-weight: bold; cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); margin: 10px; }';
echo 'button:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4); }';
echo 'button:active { transform: translateY(-1px) scale(1.02); }';
echo '.footer { margin-top: 30px; font-size: 0.9em; color: #2c3e50; text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8); }';
echo '.success-message { background: linear-gradient(45deg, #00f260, #0575e6); color: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; font-size: 1.2em; font-weight: bold; box-shadow: 0 8px 25px rgba(0, 242, 96, 0.3); }';
echo '@media (max-width: 768px) { .container { padding: 20px; } .results-table { padding: 15px; } #charts-container { flex-direction: column; } }';
echo '</style>';
echo '</head>';
echo '<body>';
echo '<div class="floating-shapes">';
echo '<div class="floating-shape shape-1"></div>';
echo '<div class="floating-shape shape-2"></div>';
echo '<div class="floating-shape shape-3"></div>';
echo '<div class="floating-shape shape-4"></div>';
echo '</div>';
echo '<div class="container">';
echo '<h1>Multidimensional Quotient scale (MQS)</h1>';
echo '<div class="results-table">';
echo '<h2>📊 Your Performance</h2>';
echo '<table>';
echo '<thead><tr><th>Category</th><th>Percentage</th><th>Rating</th></tr></thead>';
echo '<tbody>';
echo '<tr><td>Emotional Quotient (EQ)</td><td>' . number_format($eqPercent, 1) . '%</td><td>' . getRating($eqPercent) . '</td></tr>';
echo '<tr><td>Intelligence Quotient (IQ)</td><td>' . number_format($iqPercent, 1) . '%</td><td>' . getRating($iqPercent) . '</td></tr>';
echo '<tr><td>Financial Quotient (FQ)</td><td>' . number_format($fqPercent, 1) . '%</td><td>' . getRating($fqPercent) . '</td></tr>';
echo '<tr><td>Social Quotient (SQ)</td><td>' . number_format($sqPercent, 1) . '%</td><td>' . getRating($sqPercent) . '</td></tr>';
echo '</tbody></table></div>';
echo '<div class="recommendation"><strong>Recommendations:</strong><br>' . nl2br($formattedRecommendations) . '</div>';
echo '<div class="footer">&copy; 2025 Espiratia. All Rights Reserved.</div>';
echo '</div>';
echo '<script>';
echo 'console.log("Chart.js loaded:", typeof Chart !== "undefined");';
echo 'let quizResults = { eqPercent: ' . $eqPercent . ', iqPercent: ' . $iqPercent . ', fqPercent: ' . $fqPercent . ', sqPercent: ' . $sqPercent . ' };';
echo 'function showCharts() {';
echo '  console.log("showCharts called");';
echo '  const chartsContainer = document.getElementById("charts-container");';
echo '  if (!chartsContainer) { console.error("charts-container element not found"); return; }';
echo '  if (typeof Chart === "undefined") { alert("Chart.js library not loaded."); return; }';
echo '  if (!quizResults || Object.values(quizResults).every(v => v === 0)) { alert("No valid quiz data to display."); return; }';
echo '  chartsContainer.innerHTML = "";';
echo '  // Pie Chart';
echo '  const pieCanvas = document.createElement("canvas");';
echo '  pieCanvas.id = "pieChartCanvas";';
echo '  pieCanvas.style.width = "150px";';
echo '  pieCanvas.style.height = "150px";';
echo '  chartsContainer.appendChild(pieCanvas);';
echo '  const pieCtx = pieCanvas.getContext("2d");';
echo '  new Chart(pieCtx, {';
echo '    type: "pie",';
echo '    data: {';
echo '      labels: ["EQ", "IQ", "FQ", "SQ"],';
echo '      datasets: [{';
echo '        data: [quizResults.eqPercent, quizResults.iqPercent, quizResults.fqPercent, quizResults.sqPercent],';
echo '        backgroundColor: ["#ff6b6b", "#4ecdc4", "#45b7d1", "#f7b731"],';
echo '        borderWidth: 1';
echo '      }]';
echo '    },';
echo '    options: {';
echo '      responsive: true,';
echo '      maintainAspectRatio: true,';
echo '      hover: { mode: "nearest", intersect: false },';
echo '      plugins: {';
echo '        legend: { position: "bottom", labels: { font: { size: 11 } } },';
echo '        title: { display: true, text: "Quotient Distribution", font: { size: 13 } },';
echo '        tooltip: { callbacks: { label: function(context) { return context.label + ": " + context.parsed.toFixed(1) + "%"; } } }';
echo '      }';
echo '    }';
echo '  });';
echo '  // Bar Chart';
echo '  const barCanvas = document.createElement("canvas");';
echo '  barCanvas.id = "barChartCanvas";';
echo '  barCanvas.style.width = "250px";';
echo '  barCanvas.style.height = "150px";';
echo '  chartsContainer.appendChild(barCanvas);';
echo '  const barCtx = barCanvas.getContext("2d");';
echo '  const ratings = [';
echo '    quizResults.eqPercent >= 91 ? "Excellent" : quizResults.eqPercent >= 76 ? "Good" : quizResults.eqPercent >= 60 ? "Average" : "Need attention",';
echo '    quizResults.iqPercent >= 91 ? "Excellent" : quizResults.iqPercent >= 76 ? "Good" : quizResults.iqPercent >= 60 ? "Average" : "Need attention",';
echo '    quizResults.fqPercent >= 91 ? "Excellent" : quizResults.fqPercent >= 76 ? "Good" : quizResults.fqPercent >= 60 ? "Average" : "Need attention",';
echo '    quizResults.sqPercent >= 91 ? "Excellent" : quizResults.sqPercent >= 76 ? "Good" : quizResults.sqPercent >= 60 ? "Average" : "Need attention"';
echo '  ];';
echo '  new Chart(barCtx, {';
echo '    type: "bar",';
echo '    data: {';
echo '      labels: ["EQ", "IQ", "FQ", "SQ"],';
echo '      datasets: [{';
echo '        label: "Percentage",';
echo '        data: [quizResults.eqPercent, quizResults.iqPercent, quizResults.fqPercent, quizResults.sqPercent],';
echo '        backgroundColor: ["#ff6b6b", "#4ecdc4", "#45b7d1", "#f7b731"],';
echo '        borderWidth: 1,';
echo '        borderRadius: 4,';
echo '        hoverBackgroundColor: ["#ff5252", "#26a69a", "#2196f3", "#ffb300"]';
echo '      }]';
echo '    },';
echo '    options: {';
echo '      responsive: true,';
echo '      maintainAspectRatio: true,';
echo '      hover: { mode: "index", intersect: false, animationDuration: 200 },';
echo '      plugins: {';
echo '        legend: { display: false },';
echo '        title: { display: true, text: "Category Scores", font: { size: 13 } },';
echo '        tooltip: {';
echo '          backgroundColor: "rgba(0, 0, 0, 0.8)",';
echo '          titleFont: { size: 11 },';
echo '          bodyFont: { size: 11 },';
echo '          callbacks: {';
echo '            label: function(context) {';
echo '              return context.label + ": " + context.parsed.y.toFixed(1) + "% (" + ratings[context.dataIndex] + ")";';
echo '            }';
echo '          }';
echo '        }';
echo '      },';
echo '      scales: {';
echo '        x: { ticks: { font: { size: 11 } } },';
echo '        y: { beginAtZero: true, max: 100, ticks: { font: { size: 11 }, stepSize: 20 }, grid: { color: "rgba(0, 0, 0, 0.1)" } }';
echo '      },';
echo '      interaction: { intersect: false, mode: "index" }';
echo '    }';
echo '  });';
echo '}';
echo '</script>';
echo '</body>';
echo '</html>';

// Now send emails
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

    // Note: Emails sent after displaying results
} catch (Exception $e) {
    echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
}
?>

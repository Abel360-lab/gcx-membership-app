<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['token']) || empty($_POST['reason'])) {
    die("Invalid request.");
}

$token = $_POST['token'];
$reason = trim($_POST['reason']);

// DB settings
$host = 'localhost';
$dbname = 'gcx_applications';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Fetch applicant email from DB
$stmt = $db->prepare("SELECT applicant_data FROM membership_applications WHERE token = :token");
$stmt->execute([':token' => $token]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found.");
}

$applicantData = json_decode($app['applicant_data'], true);
$applicantEmail = $applicantData['email'] ?? null;

if (!$applicantEmail) {
    die("Applicant email not found.");
}

// Update application status
$stmt = $db->prepare("UPDATE membership_applications SET status = 'declined' WHERE token = :token");
$stmt->execute([':token' => $token]);

// Send notification email to applicant
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Update as needed
    $mail->SMTPAuth = true;
    $mail->Username = 'aokudjeto5@gmail.com'; // Use your sender email
    $mail->Password = 'jviv rwup kjni ljib'; // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('abelokudjeto5@gmail.com', 'GCX Applications');
    $mail->addAddress($applicantEmail);
    $mail->Subject = '❌ Your GCX Membership Application was Declined';
    $mail->Body = "Dear Applicant,\n\nUnfortunately, your application was declined for the following reason:\n\n\"$reason\"\n\nIf you believe this was in error, you may contact us to appeal.\n\nBest regards,\nGCX Team";

    $mail->send();
    echo "✅ Application declined. Applicant has been notified.";
} catch (Exception $e) {
    echo "❌ Mail could not be sent. Error: " . $mail->ErrorInfo;
}
?>

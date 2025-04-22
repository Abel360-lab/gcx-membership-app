<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

if (!isset($_POST['token'])) {
    die("Invalid submission.");
}

$token = $_POST['token'];
$floorRepData = json_encode($_POST);

// Database connection
$host = 'localhost';
$dbname = 'gcx_applications';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Update the database
$stmt = $db->prepare("UPDATE membership_applications SET floor_rep_data = :data, status = 'floor_rep_submitted' WHERE token = :token");
$stmt->execute([':data' => $floorRepData, ':token' => $token]);

// Create the next-step email
$local_ip = $_SERVER['SERVER_ADDR']; // Replace 'localhost' with IP for mobile access
$link = "http://$local_ip/gcx_app/legal_officer_form.php?token=" . urlencode($token);
$legalOfficerEmail = "aokudjeto5@gmail.com";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aokudjeto5@gmail.com';      // Your Gmail
    $mail->Password   = 'jviv rwup kjni ljib';    // Gmail App Password
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('abelokudjeto5@gmail.com', 'GCX Application');
    $mail->addAddress($legalOfficerEmail);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Action Required: Legal Officer - Application Continuation';
    $mail->Body    = "The floor representative has submitted their section.<br><br>"
                   . "Click the link below to continue:<br><a href='$link'>$link</a><br><br>"
                   . "If the link doesn't work, copy and paste this into your browser:<br>$link";

    $mail->send();
    echo "✅ Floor representative data submitted. Email sent to the legal officer.";
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}

// Optional debug link for testing manually
echo "<br><br><strong>Debug Link for Legal Officer:</strong> <a href='$link' target='_blank'>$link</a>";
?>

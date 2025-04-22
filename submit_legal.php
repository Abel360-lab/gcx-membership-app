<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

if (!isset($_POST['token'])) {
    die("Invalid submission.");
}
$token = $_POST['token'];
$legalOfficerData = json_encode($_POST);

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

// Save legal officer data
$stmt = $db->prepare("UPDATE membership_applications SET legal_officer_data = :data, status = 'legal_officer_submitted' WHERE token = :token");
$stmt->execute([':data' => $legalOfficerData, ':token' => $token]);

// Email the floor rep that the form is complete
$floorRepEmail = "aokudjeto5@gmail.com";
$local_ip = $_SERVER['SERVER_ADDR']; // e.g. 192.168.x.x for LAN access
$link = "http://$local_ip/gcx_app/view_application.php?token=" . urlencode($token);

$mail = new PHPMailer(true);

try {
    // Mail server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aokudjeto5@gmail.com';
    $mail->Password   = 'jviv rwup kjni ljib'; // Replace with Gmail app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Message setup
    $mail->setFrom('aokudjeto5@gmail.com', 'GCX Application System');
    $mail->addAddress($floorRepEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Application Finalized – View & Print';
    $mail->Body    = "The legal officer has completed the application.<br><br>"
                   . "Please <a href='$link'>click here</a> to view and print the full submission.<br><br>"
                   . "Or copy and paste this in your browser:<br>$link";

    $mail->send();
    echo "✅ Application finalized. Email sent to the Floor Representative.";
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}

// Debug fallback
echo "<br><br><strong>Debug Link:</strong> <a href='$link' target='_blank'>$link</a>";
?>

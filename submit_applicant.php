<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoload

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

// Create a unique token for this applicant
$token = bin2hex(random_bytes(16));

// Create folder to store applicant files
$uploadDir = "uploads/applicant_docs/$token/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file uploads
$uploadFields = [
    'cv', 
    'national_id_front', 
    'national_id_back', 
    'passport_picture', 
    'computer_literacy',
    // 'passport_picture', 
    // 'residence_proof'
];

$uploadedFiles = [];

foreach ($uploadFields as $field) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
        $originalName = basename($_FILES[$field]['name']);
        $safeName = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $originalName);
        $newName = $field . '_' . time() . '_' . $safeName;
        $targetPath = $uploadDir . $newName;

        if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
            $uploadedFiles[$field] = $targetPath;
        } else {
            die("❌ Failed to upload file: $field");
        }
    }
}

// Store form data and uploaded file paths
$applicantData = json_encode($_POST);
$fileData = json_encode($uploadedFiles);

$stmt = $db->prepare("INSERT INTO membership_applications (token, applicant_data, applicant_files) VALUES (:token, :data, :files)");
$stmt->execute([
    ':token' => $token,
    ':data' => $applicantData,
    ':files' => $fileData
]);

// Prepare email to Floor Rep
$floorRepEmail = "abelokudjeto5@gmail.com";  // Set your desired floor rep email
$local_ip = $_SERVER['SERVER_ADDR'];
$link = "http://$local_ip/gcx_app/floor_rep_review.php?token=" . urlencode($token);

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aokudjeto5@gmail.com'; // Your Gmail
    $mail->Password   = 'jviv rwup kjni ljib';  // App password
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('aokudjeto5@gmail.com', 'GCX Application');
    $mail->addAddress($floorRepEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Action Required: Floor Representative - Application Continuation';
    $mail->Body    = "An applicant has submitted their form.<br>Please <a href='$link'>click here</a> to continue.<br><br>Or paste this in your browser: $link";

    // $mail->SMTPDebug = 2;
    // $mail->Debugoutput = 'html';
    
    $mail->send();
    echo "✅ Application submitted successfully. Email sent to Floor Representative.";
} catch (Exception $e) {
    echo "❌ Email failed: {$mail->ErrorInfo}";
}

echo "<br><a href='$link'>Open Floor Rep Form (DEBUG LINK)</a>";
?>

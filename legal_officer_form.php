<?php
// legal_officer_form.php
if (!isset($_GET['token'])) {
    die("Invalid access.");
}
$token = $_GET['token'];

// Database connection settings
$host = 'localhost';
$dbname = 'gcx_applications';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Retrieve application data (you could display parts of the applicant and floor rep data if needed)
$stmt = $db->prepare("SELECT applicant_data, floor_rep_data FROM membership_applications WHERE token = :token");
$stmt->execute([':token' => $token]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Legal Officer Form</title>
</head>
<body>
  <h2>Legal Officer Approval</h2>
  <form action="submit_legal.php" method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    Certificate of Business Registration: <input type="text" name="certificate_registration" required><br>
    Certificate of Incorporation: <input type="text" name="certificate_incorporation" required><br>
    Latest Audit Report/Bank Statement: <input type="text" name="audit_report" required><br>
    Valid ID/Passport Verification: <input type="text" name="id_verification" required><br>
    Proof of Residence Verification: <input type="text" name="residence_proof" required><br>
    Signature of Legal Officer: <input type="text" name="legal_signature" required><br>
    Date: <input type="date" name="legal_date" required><br>
    <button type="submit">Finalize Application</button>
  </form>
</body>
</html>

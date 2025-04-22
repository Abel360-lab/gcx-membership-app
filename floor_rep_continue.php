<?php
if (!isset($_GET['token'])) {
    die("Access denied: Token not provided.");
}

$token = $_GET['token'];

$host = 'localhost';
$dbname = 'gcx_applications';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $db->prepare("SELECT applicant_data FROM membership_applications WHERE token = :token");
$stmt->execute([':token' => $token]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Invalid token.");
}

$applicantData = json_decode($app['applicant_data'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Floor Rep Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <h2 class="mb-4 text-primary">Floor Representative Section</h2>

  <p><strong>Applicant:</strong> <?= htmlspecialchars($applicantData['company_name'] ?? 'N/A') ?></p>
  <p><strong>Trader Type:</strong> <?= htmlspecialchars($applicantData['trader_type'] ?? '') ?></p>
  <hr>

  <form action="submit_floor_rep.php" method="POST" enctype="multipart/form-data" class="card p-4 bg-white shadow-sm">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <div class="mb-3">
      <label for="fr_first_name" class="form-label">First Name</label>
      <input type="text" class="form-control" id="fr_first_name" name="fr_first_name" required>
    </div>
    <div class="mb-3">
      <label for="fr_last_name" class="form-label">Last Name</label>
      <input type="text" class="form-control" id="fr_last_name" name="fr_last_name" required>
    </div>
    <div class="mb-3">
      <label for="fr_mobile" class="form-label">Mobile</label>
      <input type="tel" class="form-control" id="fr_mobile" name="fr_mobile" required>
    </div>
    <div class="mb-3">
      <label for="fr_email" class="form-label">Email</label>
      <input type="email" class="form-control" id="fr_email" name="fr_email" required>
    </div>
    <div class="mb-3">
      <label for="fr_address" class="form-label">Residential Address</label>
      <input type="text" class="form-control" id="fr_address" name="fr_address" required>
    </div>
    <div class="mb-3">
      <label for="member_represented" class="form-label">Name of Member Represented</label>
      <input type="text" class="form-control" id="member_represented" name="member_represented" required>
    </div>

    <hr>
    <!-- <h5 class="text-primary">Required Documents</h5>

    <div class="mb-3">
      <label class="form-label">CV (PDF)</label>
      <input type="file" name="cv" class="form-control" accept=".pdf" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Valid National ID (PDF)</label>
      <input type="file" name="national_id" class="form-control" accept=".pdf" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Passport Picture (JPEG/PNG)</label>
      <input type="file" name="passport_picture" class="form-control" accept="image/png, image/jpeg" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Computer Literacy Document (PDF)</label>
      <input type="file" name="computer_literacy" class="form-control" accept=".pdf" required>
    </div> -->

    <button type="submit" class="btn btn-primary w-100">Submit Floor Rep Form</button>
  </form>
</div>
</body>
</html>

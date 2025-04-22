<?php
// floor_rep_review.php

if (!isset($_GET['token'])) {
  die("Access denied: Token not provided.");
}

$token = $_GET['token'];

// DB credentials
$host = 'localhost';
$dbname = 'gcx_applications';
$username = 'root';
$password = '';

try {
  $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// Fetch application by token
$stmt = $db->prepare("SELECT * FROM membership_applications WHERE token = :token");
$stmt->execute([':token' => $token]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
  die("Invalid or unknown token.");
}

$applicantData = json_decode($app['applicant_data'], true);
$applicantFiles = json_decode($app['applicant_files'], true); // ✅ decode files

// Handle form submission if declined
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decline_reason'])) {
  $declineReason = trim($_POST['decline_reason']);

  $stmt = $db->prepare("UPDATE membership_applications SET status = 'declined', decline_reason = :reason WHERE token = :token");
  $stmt->execute([':reason' => $declineReason, ':token' => $token]);

  $to = $applicantData['email'] ?? '';
  if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $subject = "GCX Application Declined";
    $message = "Dear applicant,\n\nYour application has been declined by the Floor Representative.\nReason:\n$declineReason\n\nPlease review and resubmit if applicable.";
    $headers = "From: noreply@gcx.com";
    mail($to, $subject, $message, $headers);
  }

  echo "<p style='color:red;'>Application has been declined and the applicant has been notified.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Floor Rep Review - GCX Application</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/heading.css">
</head>

<body class="form-body">
  <div class="google-form-container">
    <h2 class="mb-4">Applicant Information Review</h2>
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Company Details</div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <!-- anything -->
          </div>
          <div class="col-md-6">
            <!-- anything -->
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Type of Trader and Industry</div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <!-- anything -->
            <p><strong>Trader Type:</strong> <?= htmlspecialchars($applicantData['trader_type'] ?? '-') ?></p>
          </div>
          <div class="col-md-6">
            <!-- anything -->
            <p><strong>Industry Type:</strong> <?= htmlspecialchars($applicantData['industry_type'] ?? '-') ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Company Details</div>
      <div class="card-body row">
        <div class="col-md-6">
          <p><strong>Company Name:</strong> <?= htmlspecialchars($applicantData['company_name'] ?? '-') ?></p>
          <p><strong>City:</strong> <?= htmlspecialchars($applicantData['city'] ?? '-') ?></p>
          <p><strong>Region:</strong> <?= htmlspecialchars($applicantData['region'] ?? '-') ?></p>
          <p><strong>Country:</strong> <?= htmlspecialchars($applicantData['country'] ?? '-') ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($applicantData['email'] ?? '-') ?></p>
        </div>
        <div class="col-md-6">
          <p><strong>Place of Business:</strong> <?= htmlspecialchars($applicantData['place_of_business'] ?? '-') ?></p>
          <p><strong>Telephone:</strong> <?= htmlspecialchars($applicantData['telephone'] ?? '-') ?></p>
          <p><strong>Place of Business:</strong> <?= htmlspecialchars($applicantData['place_of_business'] ?? '-') ?></p>
          <p><strong>Interested Commodities:</strong>
            <?= isset($applicantData['commodities']) ? implode(', ', $applicantData['commodities']) : '-' ?></p>
          <p><strong>Trading Capacity:</strong> <?= htmlspecialchars($applicantData['trading_capacity'] ?? '-') ?></p>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Next </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <!-- anything -->
          </div>
          <div class="col-md-6">
            <!-- anything -->
          </div>
        </div>
      </div>
    </div>

    <h4 class="mt-4">Uploaded Documents</h4>
    <ul class="list-group mb-4">
      <?php if (!empty($applicantFiles)): ?>
        <?php foreach ($applicantFiles as $label => $path): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= ucwords(str_replace('_', ' ', $label)) ?>
            <a href="<?= htmlspecialchars($path) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="list-group-item">No files uploaded.</li>
      <?php endif; ?>
    </ul>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Particulars</th>
            <th class="text-center">Answer</th>
            <th>Comment</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $declarations = [
            ['key' => 'offence', 'comment' => 'offence_comment', 'text' => 'Have you ever been pronounced guilty of a criminal offence involving moral turpitude?'],
            ['key' => 'bankrupt', 'comment' => 'bankrupt_comment', 'text' => 'Have you ever been adjudged bankrupt or proved to be insolvent?'],
            ['key' => 'litigation', 'comment' => 'litigation_comment', 'text' => 'Have you ever been involved in litigation or financial liabilities?'],
            ['key' => 'fraud', 'comment' => 'fraud_comment', 'text' => 'Have you ever been convicted of fraud or dishonesty?'],
            ['key' => 'disciplinary', 'comment' => 'disciplinary_comment', 'text' => 'Has any disciplinary action been taken against you?'],
            ['key' => 'denied', 'comment' => 'denied_comment', 'text' => 'Have you ever been denied membership?'],
            ['key' => 'liquidator', 'comment' => 'liquidator_comment', 'text' => 'Have you had a liquidator or receiver appointed against you?'],
            ['key' => 'law', 'comment' => 'law_comment', 'text' => 'Have you committed any act against the law that could lead to winding-up?'],
            ['key' => 'defaulter', 'comment' => 'defaulter_comment', 'text' => 'Have you ever been declared a defaulter or suspended by a stock exchange?'],
            ['key' => 'court_case', 'comment' => 'court_case_comment', 'text' => 'Is there any court case pending against your company or staff?'],
            ['key' => 'incompetent', 'comment' => 'incompetent_comment', 'text' => 'Have you ever been declared incompetent to enter into contracts in Ghana?']
          ];

          foreach ($declarations as $i => $decl) {
            $answer = isset($applicantData[$decl['key']]) ? ucfirst($applicantData[$decl['key']]) : 'N/A';
            $comment = isset($applicantData[$decl['comment']]) ? htmlspecialchars($applicantData[$decl['comment']]) : '';
            echo "<tr>
            <td>" . ($i + 1) . "</td>
            <td>{$decl['text']}</td>
            <td class='text-center'>{$answer}</td>
            <td>{$comment}</td>
          </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <form method="post" class="mb-4">
      <h4 class="text-danger">Decline Application</h4>
      <div class="mb-3">
        <label for="decline_reason" class="form-label">Reason:</label>
        <textarea name="decline_reason" id="decline_reason" class="form-control" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn btn-danger">Decline Application</button>
    </form>

    <a href="floor_rep_continue.php?token=<?= urlencode($token) ?>" class="btn btn-success">Proceed to Fill Floor Rep
      Form</a>
  </div>
</body>

</html>
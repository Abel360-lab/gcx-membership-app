<?php
if (!isset($_GET['token'])) {
    die("Invalid access.");
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

$stmt = $db->prepare("SELECT applicant_data, floor_rep_data, legal_officer_data, token_created_at 
                      FROM membership_applications 
                      WHERE token = :token");
$stmt->execute([':token' => $token]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found.");
}

// Token expiry
$createdAt = strtotime($app['token_created_at']);
if ($createdAt < strtotime('-24 hours')) {
    die("⏰ This application link has expired.");
}

$applicantData = json_decode($app['applicant_data'], true);
$floorRepData = json_decode($app['floor_rep_data'], true);
$legalOfficerData = json_decode($app['legal_officer_data'], true);

function safe($data, $default = '-') {
    return htmlspecialchars($data ?? $default);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/heading.css">
  <title>Final Application</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .section { margin-bottom: 2rem; }
    h2 { border-bottom: 2px solid #ddd; padding-bottom: 0.5rem; }
    .label { font-weight: bold; display: inline-block; width: 250px; }
    .value { display: inline-block; }
    @media print {
      button { display: none; }
      body { margin: 0; }
    }
  </style>
</head>
<body>
<div>
        <h2 class="text-center mb-3">TRADING MEMBER APPLICATION REQUIREMENTS</h2>

        <h4>INSTRUCTIONS</h4>
        <ol>
          <li>Please read the <strong>Terms and Conditions</strong> carefully.</li>
          <li>Complete the Membership Application, attaching any required documents along with the application.</li>
          <li>Please send this application form to one of the GCX office addresses listed on the last page. You will be
            notified once your application is received.</li>
        </ol>

        <h4 class="mt-4">KEY NOTES</h4>
        <ol>
          <li>Approval of Membership Application will take <strong>fifteen (15) working days</strong> from the day that
            all the required documents are received by the Exchange.</li>
          <li>Once the membership application is approved, the applicant will be required to undertake a
            <strong>Membership Training</strong>, after which the applicant shall be entitled to:
            <ul>
              <li>A GCX Membership Number</li>
              <li>A GCX Certificate</li>
            </ul>
          </li>
        </ol>

        <h4 class="mt-4">TERMS AND CONDITIONS FOR MEMBERSHIP AT THE GCX</h4>
        <ol>
          <li><strong>A Trading Member (TM)</strong>
            <ul>
              <li>Can <strong>BUY</strong> and / or <strong>SELL</strong> for self only.</li>
              <li>Can <strong>Settle</strong> all commodities for self only.</li>
            </ul>
          </li>
          <li>A TRADING applicant shall submit this application form along with proof of the following requirements
            (duly attested by the applicant):
            <ul>
              <li>Company Registration Documents</li>
              <li>Tax Clearance Certificate or VAT Registration Certificate</li>
              <li>Latest Auditor report or Statement of Affairs or Bank Statement (last 3 months)</li>
              <li>Proof of Capital Adequacy of not less than <strong>GH₵ 50,000</strong></li>
              <li>Signed GCX Risk Disclosure form (to be provided by GCX)</li>
              <li>At least nominate one (1) trading representative</li>
              <li>Recent Passport Picture</li>
              <li>Valid ID Card (Ghana Card - coloured copy - Front and Back)</li>
              <li>Proof of Residence (Utility Bill, Tenancy Agreement, etc)</li>
            </ul>
          </li>
          <li>An <strong>Admission and Processing fee</strong> as specified herein the application form. GCX reserves
            the right to reject this Membership Application if these fees are not paid.</li>
          <li>GCX reserves the right to accept or reject any Membership Application and shall offer reasons for
            rejection thereof where applicable.</li>
          <li>Membership shall be subject to renewal every year on GCX applicable terms.</li>
        </ol>
        <hr>
        <div class="table-responsive table-hover">
          <h2>PROPOSED 2018 GCX MEMBERSHIP FEES - Subject to approval/renewal by GCX Board and Market Council</h2>
          <table class="table table-bordered align-middle ">
            <thead class="table-light">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Membership Type</th>
                <th scope="col">Admission Fee (One Off Payment) GH₵</th>
                <th scope="col">Annual Renewal Fee GH₵ </th>
                <th scope="col">Membership Application Processing Fee GH₵</th>
              </tr>
            </thead>
            <tbody class="table-group-divider text-center">
              <tr>
                <td>1</td>
                <td>Trading Member</td>
                <td>2,200.00</td>
                <td>1,100.00</td>
                <td>50.00</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

  <div class="section">
    <h2>Applicant (Trading Member) Details</h2>
    <p><span class="form-label">Company Name:</span> <span class="value"><?= safe($applicantData['company_name']) ?></span></p>
    <!-- Add more fields as needed -->
    <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Company Name:</strong> <?= htmlspecialchars($applicantData['company_name'] ?? '-') ?></p>
                        <p><strong>Trader Type:</strong> <?= htmlspecialchars($applicantData['trader_type'] ?? '-') ?></p>
                        <p><strong>Region:</strong> <?= htmlspecialchars($applicantData['region'] ?? '-') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($applicantData['email'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Place of Business:</strong> <?= htmlspecialchars($applicantData['place_of_business'] ?? '-') ?></p>
                        <p><strong>Telephone:</strong> <?= htmlspecialchars($applicantData['telephone'] ?? '-') ?></p>
                        <p><strong>Interested Commodities:</strong> <?= isset($applicantData['commodities']) ? implode(', ', $applicantData['commodities']) : '-' ?></p>
                        <p><strong>Trading Capacity:</strong> <?= htmlspecialchars($applicantData['trading_capacity'] ?? '-') ?></p>
                    </div>
      </div>
  </div>
  <div class="section">
    <h2>Floor Representative Details</h2>
    <p><span class="label">Representative Name:</span> 
       <span class="value"><?= safe($floorRepData['fr_first_name']) ?> <?= safe($floorRepData['fr_last_name']) ?></span></p>
    <!-- Add more fields -->
  </div>
  <div class="section">
    <h2>Legal Officer Approval</h2>
    <p><span class="label">Certificate of Business Registration:</span> 
       <span class="value"><?= safe($legalOfficerData['certificate_registration']) ?></span></p>
    <!-- Add more fields -->
  </div>

  <button onclick="window.print()">🖨️ Print this Application</button>
</body>
</html>

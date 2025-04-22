<?php
require __DIR__ .'/includes/auth.php';
$db = require __DIR__ . '/includes/db.php';

// Fetch application counts
$total = $db->query("SELECT COUNT(*) FROM membership_applications")->fetchColumn();
$pending = $db->query("SELECT COUNT(*) FROM membership_applications WHERE status IS NULL")->fetchColumn();
$approved = $db->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'approved'")->fetchColumn();
$declined = $db->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'declined'")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/heading.css">
  
</head>
<body class="container py-4">
  <div class="d-flex justify-content-between mb-4">
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
  </div>

  <div class="row g-3">
    <div class="col-md-3"><div class="card p-3 bg-light">Total Applications: <strong><?= $total ?></strong></div></div>
    <div class="col-md-3"><div class="card p-3 bg-warning text-dark">Pending: <strong><?= $pending ?></strong></div></div>
    <div class="col-md-3"><div class="card p-3 bg-success text-white">Approved: <strong><?= $approved ?></strong></div></div>
    <div class="col-md-3"><div class="card p-3 bg-danger text-white">Declined: <strong><?= $declined ?></strong></div></div>
  </div>
</body>
</html>

<?php
declare(strict_types=1);

// includes/db.php

$host   = 'localhost';
$db     = 'gcx_applications';
$user   = 'root';
$pass   = '';
$charset= 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log error in production
    exit('Database Connection Failed: ' . htmlspecialchars($e->getMessage()));
}

// Return the PDO instance
return $pdo;

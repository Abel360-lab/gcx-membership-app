<?php
$to = "abelokudjeto5@gmail.com";  // Replace with a valid email address
$subject = "Test Mail from XAMPP";
$message = "This is a test email sent from PHP using XAMPP sendmail.";
$headers = "From: aokudjeto5@gmail.com" . "\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail sent successfully.";
} else {
    echo "Mail failed to send.";
}
?>

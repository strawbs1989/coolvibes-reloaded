<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes and autoload the required libraries
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Create a new PHPMailer instance
$mail = new PHPMailer();

// Set mailer to use SMTP
$mail->isSMTP();

// Replace the following SMTP settings with your own
$mail->Host = 'smtp.gmail.com';  // Your SMTP server
$mail->SMTPAuth = true;
$mail->Username = 'jayaubs89@gmail.com'; // Your SMTP username
$mail->Password = 'eawdimrjlashvwrp'; // Your SMTP password
$mail->SMTPSecure = 'tls';          // Use 'tls' or 'ssl' based on your server configuration
$mail->Port = 587;                 // SMTP port, usually 587 for TLS

// Set the sender and recipient email addresses
$mail->setFrom('coolvibes1989@gmail.com', 'CoolVibes-Reloaded');
$mail->addAddress($to);
$mail->Subject = $subject;

// Set email subject and message
$message = "<html><body>";
$message .= "<p><img src='https://coolvibes-reloaded.com/img/favicon.png' alt='' width='708' height='142'></p>";
$mail->Subject = 'Great To Have You Onboard';
$mail->Body = "Welcome to our website. We're excited to have you as a member.";

// Send the email
if ($mail->send()) {
    echo 'Email sent successfully.';
} else {
    echo 'Email sending failed. Error: ' . $mail->ErrorInfo;
}
?>
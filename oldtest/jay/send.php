<?php  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'http://requests.cu.ma/aubrey/oldtest/jay/phpmailer/src/Exeption.php';
require 'http://requests.cu.ma/aubrey/oldtest/jay/phpmailer/src/PHPMailer.php';
require 'http://requests.cu.ma/aubrey/oldtest/jay/phpmailer/src/SMTP.php';

if(isset($_POST["send])){
  $mail = new PHPMailer(true);
  
  $mail->isSMPT();
  $mail-> = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'coolvibes1989@gmail.com'; // your gmail
  $mail->Password = 'vwbtutjijdecoimq'; // your gmail app password
  $mail->SMTPSecure = 'ssl';
  $mail->Port = 465;
  
  $mail->setFrom('coolvibes1989@gmail.com');
  
  $mail->addAddress($_POST["email"]);
  
  $mail->isHTML(true);
  
  $mail->Subject = $_POST["subject"];
  
  $mail->Body = $_POST["message"];
  
  $mail->send();
  
  echo
  "
  <script>
  alert('Sent Successfully');
  document.location.href = 'http://requests.cu.ma/aubrey/oldtest/jay/index.php';
  </script>
  ";
  

}

?>
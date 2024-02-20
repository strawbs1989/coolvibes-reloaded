<?php
error_report(0);
$msg"";
if(isset($_POST['submit'])){
  $to = "coolvibes1989@gmail.com";
  $subject = "Form Submission";
  $name = $_POST['Name'];
  $to = $_POST['To'];
  $djselect = $_POST['DJ Select'];
  $message = $_POST['Message'];
  $songrequest = $_POST['Song Request'];
  
  $msgBody = 'Name : '.$name.' requested a song : '.$message;
  $header  = 'From:' .$email;
  
  $secretKey = "6LeJTFopAAAAANb85XdaiWGO0Y4PkHItFC_hS-Vk";
  $responseKey = $_POST['g-recaptcha'];
  
  $url = "https://www.google.com/recaptcha/api/siteverify?secret=$
    secretKey&res"
}
?>
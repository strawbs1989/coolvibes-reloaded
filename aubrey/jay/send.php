<?php
$email = $_POST['email'];

// edit below
$from = "this is the name it says the email is from";
$fromemail = "this is the email that is says the message is from";
$reply = "this is the email that receives the replies";

$subject = "SUBJECT HERE";
$body = "BODY HERE";

// send code, do not edit unless you know what your doing
$header .= "Reply-To: Support <$reply>\r\n"; 
    $header .= "Return-Path: Support <$reply>\r\n"; 
    $header .= "From: $from <$fromemail>\r\n"; 
    $header .= "Organization: getFreexBoxLiveCodes\r\n"; 
    $header .= "Content-Type: text/plain\r\n"; 
 
    mail("$email", "$subject", "$body", $header);
?>
<?php

if(isset($_POST) {
   $yourName = $_POST['name'];
   $trackName = $_POST['track'];
   $artistName = $_POST['artist'];
   $message = $_POST['message'];

   $to = "coolvibes1989@gmail.com";
   $body = ";
   
   $body .= "From: ".$yourName. "\r\n"
   $body .= "Track: ".$trackName. "\r\n"
   $body .= "Artist: ".$artistName. "\r\n"
   $body .= "Message: ".$message. "\r\n"
   
   mail($to,$message,$body);
   
   ?>
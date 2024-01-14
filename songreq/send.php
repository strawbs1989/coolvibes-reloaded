<?php

if (isset($_POST['dedicate'])) {
$yourname =$_POST[yourname];
$subject =$_POST[subject];	
$trackname =$_POST[trackname];	
$artistname =$_POST[artistname];	
$dedicateit =$_POST[dedicateit];

$mailTo= "requests@requests.cu.ma";
$headers = "From: ".$mailFrom;
$txt = "You Have received an email from ".$name.".\n\n".$message;

mail($mailTo, $subject, $txt, $headers);
header("Location: index.php?mailsent");	
}
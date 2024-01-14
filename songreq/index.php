<?php

if (isset($_POST['dedicate'])) {
$name =$_POST[name];
$artistname =$_POST[artistname];	
$artist_song =$_POST[artisttrack];	
$dedication =$_POST[dedication];	
$headers =$_POST[headers];

$mailTo= "requests@requests.cu.ma";
$headers = "From: ".$mailFrom;
$txt = "You Have received an email from ".$name.".\n\n".$message;

mail($mailTo, $subject, $txt, $headers);
header("Location: coolvibes-reloaded.com/songreq/index.php?mailsent");	
}
?>
<?php

if (isset($_POST['dedicate'])) {
$artist =$_POST[artist];
$artist_song =$_POST['artist_song'];	
$artist_song =$_POST[artist_song];	
$dedication =$_POST[dedication];	
$headers =$_POST[headers];

$mailTo= "requests@requests.cu.ma";
$headers = "From: ".$mailFrom;
$txt = "You Have received an email from ".$name.".\n\n".$message;

mail($mailTo, $subject, $txt, $headers);
header("Location: index.php?mailsent");	
}
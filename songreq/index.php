<?php

$name =$_POST[name];
$artistname =$_POST[artistname];	
$artist_song =$_POST[artisttrack];	
$dedication =$_POST[dedication];	
$headers =$_POST[headers];

$mailTo= "coolvibes1989@gmail.com";
$headers = "From: ".$mailFrom;
$txt = "You Have received an email from ".$name.".\n\n".$message;

mail($mailTo, $name, $txt, $headers);
header("Location: coolvibes-reloaded.com/songreq/index.php?mailsent");	

?>
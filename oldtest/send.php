<?php
 
if (isset($_POST['dedicate'])) {
$yourname =$_POST[yourname];
$subject =$_POST[subject];  
$trackname =$_POST[trackname];  
$artistname =$_POST[artistname];    
$dedicateit =$_POST[dedicateit];

$to  = "coolvibes1989@gmail.com";
$headers = "From: jay@requests.cu.ma";
$txt = "You Have received an email from ".$name.".\n\n".$message;
 
mail($mailTo, $subject, $txt, $headers, $trackname);
header("Location: index.php?requestsuccessful"); 
}
?>

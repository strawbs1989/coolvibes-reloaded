<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
<title>Requests</title>
<link rel="stylesheet" type="text/css" href="https://coolvibes-reloaded.com/mail/style.css/mail/css/">
</head>
<body>

<?php
if(!empty($_POST["send"])) {
	$yourName = $_POST["yourName"];
  $trackName = $_POST["trackName"];
	$artistName = $_POST["artistName"];
	$yourdedicationMessage = $_POST["yourdedicationMessage"];
	$toEmail = "coolvibes1989@gmail.com";
  
	$mailHeaders = "Name: " . $yourName .
	"\r\n Track: ". $trackName  . 
	"\r\n Artist: ". $artistName  . 
	"\r\n Dedication: " . $yourdedicationMessage . "\r\n";

	if(mail($toEmail, $userName, $mailHeaders)) {
	    $message = "Request successful and should play shortly.";
	}
}
?>

<div class="form-container">
  <form name="contactFormEmail" method="post">
    <div class="input-row">
      <label>Your Name: <em>*</em></label> 
      <input type="text" name="userName" required id="userName"> 
    </div>
    <div class="input-row">
      <label>Track Name: <em>*</em></label> 
      <input type="email" name="trackName" required id="trackName"> 
    </div>
    <div class="input-row">
      <label>Artist Name: <em>*</em></label> 
      <input type="text" name="artistName" required id="artistName">
    </div>
    <div class="input-row">
      <label>Your Dedication message: <em>*</em></label> 
     <!-- <textarea name="userMessage" required id="userMessage"> -->
    </div>
    <div class="input-row">
      <input type="submit" name="send" value="Submit">
      <?php if (! empty($message)) {?>
      <div class='success'>
        <strong><?php echo $message; ?>	</strong>
      </div>
      <?php } ?>
    </div>
  </form>
</div>

</body>
</html>



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

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link rel="stylesheet" href="form.css" media="all">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>    
    <script src="main.js"></script>
</head>

<body>


<h3>Request successful and should play shortly</h3>


    <div class="container">
        <form action="form.php" method="POST" class="form">
            <div class="form-group">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Jane Doe" tabindex="1" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="jane@doe.com" tabindex="2" required>
            </div>
            <div class="form-group">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="Hello There!" tabindex="3" required>
            </div>
            <div class="form-group">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" rows="5" cols="50" id="message" name="message" placeholder="Enter Message..." tabindex="4"></textarea>
            </div>
            <div>
                <button type="submit" class="btn">Send Message!</button>
            </div>
        </form>
    </div>
	<?php
	endif;
	?>
</body>

</html>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link rel="stylesheet" href="https://coolvibes-reloaded.com/mail/test/form.css" media="all">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>    
    <script src="https://coolvibes-reloaded.com/mail/test/main.js"></script>
</head>

<body>
<?php
if(message_sent);
?>


<h3>Request successful and should play shortly</h3>

<?php
else:
?>
<center><p>Track Information</p></center>
<div class="container">
        <form action="https://coolvibes-reloaded.com/mail/test/form.php" method="POST" class="form">
            <div class="form-group">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" tabindex="1" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Track Name</label>
                <input type="text" class="form-control" id="name" name="email" placeholder="Track Name" tabindex="2" required>
            </div>
            <div class="form-group">
                <label for="subject" class="form-label">Artist Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Artist Name!" tabindex="3" required>
            </div>
            <div class="form-group">
                <label for="message" class="form-label">Dedication Message</label>
                <textarea class="form-control" rows="5" cols="50" id="message" name="message" placeholder="Dedicate" tabindex="4"></textarea>
            </div>
            <div>
                <button type="submit" class="btn">Request It!</button>
            </div>
        </form>
    </div>
	<?php
	endif;
	?>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
 
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://coolvibes-reloaded.com/newreq/style.css">
  <title>Document</title>

</head>
 
<body>
  <div class="container">
    <form id="contact" action="https://coolvibes-reloaded.com/newreq/mail.php" method="post">
      <h1>Requests</h1>
 
      <fieldset>
        <input placeholder="Your name" name="name" type="text" tabindex="1" autofocus>
      </fieldset>
      <fieldset>
        <input placeholder="Track Name" name="track" type="track" tabindex="2">
      </fieldset>
      <fieldset>
        <input placeholder="Artist Name" type="text" name="artist" tabindex="4">
      </fieldset>
      <fieldset>
        <textarea name="message" placeholder="your Dedication Message..." tabindex="5"></textarea>
      </fieldset>
 
      <fieldset>
        <button type="dedicate" name="send" id="contact-dedicate">Dedication Now</button>
      </fieldset>
    </form>
  </div>
</body>
 
</html>
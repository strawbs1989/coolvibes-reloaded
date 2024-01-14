<?php

#Receive user input
$your_name = $_POST['your_name'];
$track_name = $_POST['track_name'];
$artist_name = $_POST['artist_name'];
$dedication = $_POST['dedication'];

#Filter user input
function filter_email_header($form_field) {  
return preg_replace('/[nr|!/<>^$%*&]+/','',$form_field);
}



#Send email
$headers = "From: jayaubs89@gmail.com";
$sent = mail('coolvibes1989@gmail.com', $your_name, $track_name, $artist_name, $dedication);

#Thank user or notify them of a problem
if ($sent) {

?><html>
<head>
<title>Success</title>
</head>
<body>
<h1>Request successful</h1>
<p>should play shortly.</p>
</body>
</html>
<?php

} else {

?><html>
<head>
<title>Something went wrong</title>
</head>
<body>
<h1>Something went wrong</h1>
<p>We could not send your feedback. Please try again.</p>
</body>
</html>
<?php
}
?>
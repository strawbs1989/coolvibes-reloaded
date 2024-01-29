<?php

#Receive user input
$yourname = $_POST['yourname'];
$subject = $_POST['subject'];
$trackname = $_POST['trackname'];
$artistname = $_POST['artistname'];
$dedication = $_POST['dedication'];

#Filter user input
function filter_email_header($form_field) {  
return preg_replace('/[nr|!/<>^$%*&]+/','',$form_field);
}



#Send email
$headers = "From: jayaubs89@gmail.com";
$sent = mail('coolvibes1989@gmail.com', $yourname, $subject, $trackname, $artistname, $dedication);

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
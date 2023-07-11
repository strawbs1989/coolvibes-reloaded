<?php
$h = date('G'); //set variable $h to the hour of the day
$d = date('w'); //set variable $d to the day of the week.
$year = date('Y'); //set variable $year to the current year
//G is the date key for hours in 24 format (not 12), with no leading 0s, like 02.
// Adjust 2 hour offset for MST below.
$h = $h-0;


// MONDAY SCHEDULE
if ($d == 1 && $h >= 0 && $h < 4) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h >= 4 && $h < 14) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h >= 14 && $h < 16) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h >= 16 && $h < 18) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h >= 18 && $h < 20) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h >= 20 && $h < 22) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h >= 22 && $h < 24) $img = '/images/stories/DJs/akky4.jpg';
else if ($d == 2 && $h < 0) $img = '/images/stories/DJs/kittydj.jpg';


// TUESDAY SCHEDULE
if ($d == 2 && $h >= 0 && $h < 4) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 2 && $h >= 4 && $h < 14) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 2 && $h >= 14 && $h < 16) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 2 && $h >= 16 && $h < 19) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 2 && $h >= 19 && $h < 20) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 2 && $h >= 20 && $h < 22) $img = '/images/stories/DJs/johnny.jpg';
else if ($d == 2 && $h >= 22 && $h < 24) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 3 && $h < 0) $img = '/images/stories/DJs/kittydj.jpg';


// WEDNESDAY SCHEDULE
if ($d == 3 && $h >= 0 && $h < 4) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 3 && $h >= 4 && $h < 14) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 3 && $h >= 14 && $h < 16) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 3 && $h >= 16 && $h < 18) $img = 'https://static.wixstatic.com/media/sexyspin.png';
else if ($d == 3 && $h >= 18 && $h < 19) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 3 && $h >= 19 && $h < 21) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 3 && $h >= 21 && $h < 24) $img = '/images/stories/DJs/BRIAN1.jpg';
else if ($d == 4 && $h >= 0) $img = '/images/stories/DJs/kittydj.jpg';




// THURSDAY SCHEDULE
if ($d == 4 && $h >= 0 && $h < 4) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 4 && $h >= 4 && $h < 14) $img = '/images/stories/DJs/kitty-dj.jpg';
else if ($d == 4 && $h >= 14 && $h < 16) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 4 && $h >= 16 && $h < 18) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 4 && $h >= 18 && $h < 20) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 4 && $h >= 20 && $h < 22) $img = '/images/stories/DJs/johnny.jpg';
else if ($d == 4 && $h >= 22 && $h < 24) $img = '/images/stories/DJs/akky4.jpg';
else if ($d == 5 && $h < 0) $img = '/images/stories/DJs/kittydj.jpg';




// FRIDAY SCHEDULE
if ($d == 5 && $h >= 0 && $h < 4) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 5 && $h >= 4 && $h < 14) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 5 && $h >= 14 && $h < 16) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 5 && $h >= 16 && $h < 18) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 5 && $h >= 18 && $h < 20) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 5 && $h >= 20 && $h < 22) $img = '/images/stories/DJs/Sammy-.jpg';
else if ($d == 5 && $h >= 22 && $h < 24) $img = '/images/stories/DJs/mrsjones.jpg';
else if ($d == 6 && $h >= 0 && $h < 2) $img = '/images/stories/DJs/kittydj.jpg';




// SATURDAY SCHEDULE
if ($d == 6 && $h >= 0 && $h < 4) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 6 && $h >= 4 && $h < 16) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 6 && $h >= 16 && $h < 18) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 6 && $h >= 18 && $h < 20) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 6 && $h >= 20 && $h < 22) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 6 && $h >= 22 && $h < 24) $img = '/images/stories/DJs/thecocos.jpg';
else if ($d == 0 && $h < 0) $img = '/images/stories/DJs/kittydj.jpg';




// SUNDAY SCHEDULE
if ($d == 0 && $h >= 0 && $h < 2) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 0 && $h >= 2 && $h < 10) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 0 && $h >= 10 && $h < 14) $img = '/images/stories/DJs/thecocos.jpg';
else if ($d == 0 && $h >= 14 && $h < 16) $img = '/images/stories/DJs/BRIAN1.jpg';
else if ($d == 0 && $h >= 16 && $h < 18) $img = '/images/stories/DJs/mrsjones.jpg';
else if ($d == 0 && $h >= 18 && $h < 20) $img = '/images/stories/DJs/johnny.jpg';
else if ($d == 0 && $h >= 20 && $h < 22) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 0 && $h >= 22 && $h < 24) $img = '/images/stories/DJs/kittydj.jpg';
else if ($d == 1 && $h < 0) $img = '/images/stories/DJs/kittydj.jpg';
?>


<html>
<meta http-equiv="Content-Language" content="en-us">
<body bgcolor="#000000">
<div align="left">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="170" height="134" id="AutoNumber1" align="left">
<tr>

<p align="center">
<img src="<?php echo $img; ?>"height="100" width="100"><p align="left">
</td>
</tr>
</table>
</div>
</body>
</html>
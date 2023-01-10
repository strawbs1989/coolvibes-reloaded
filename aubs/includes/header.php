<!DOCTYPE html> 
<html lang="en">
<head>
<title><?php echo $sitename; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if($style)
{
if(!isset($_GET['style']))
{
echo "<link rel='stylesheet' href='files/aubs.css' type='text/css'>";
}
else
{
echo "<link rel='stylesheet' href='files/aubs2.css' type='text/css'>";
}
}
?>
</head>
<body>
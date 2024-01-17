<html>
<head>
</head>
<body>  

<?php
// define variables and set to empty values
$name = $trackname = $artistname = $dedication = $gender = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $yourname = test_input($_POST["name"]);
  $trackname = test_input($_POST["trackname"]);
  $artistname = test_input($_POST["artistname"]);
  $dedication = test_input($_POST["dedication"]);
  $gender = test_input($_POST["gender"]);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
<?php
echo $yourname;
echo "<br>";
echo $trackname;
echo "<br>";
echo $artistname;
echo "<br>";
echo $dedication;
echo "<br>";
echo $gender;
?>


Thank You <?php echo $_POST["name"]; ?><br>
Request successful and should play shortly
</body>
</html>
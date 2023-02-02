<?php
if(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = null; }
include('includes/conf.php'); 
if($errors) 
{
 ini_set('display_errors',1);
  $deb = '<h1>Debug mode  ON</h1>';
   }
   include("includes/header.php");
   
if(!$offline) 
{

include("includes/nav.php");
 echo "<div class='right'><small>";
include("includes/radio.php");
echo "</small></div>";
if(isset ($p) && file_exists("includes/$p.php")) 
{
include("includes/$p.php");
 }
 else if(isset ($p) && !file_exists("includes/$p.php"))
 {
 include("includes/404.php");
 }
  else 
  { 
include("includes/home.php");

} 
} 
else 
{ 
echo "<font color='red'>Website is offline</font>";}
echo $deb; 
include('includes/footer.php'); 
?>
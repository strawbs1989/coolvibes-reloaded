<?php
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

if(isset ($_GET['p'])) 
{
include("includes/$_GET[p].php");
 }
  else 
  { 
  $string ='i am learning php and it is Boring';
$string=str_replace('Boring','amazing',$string);
 echo "$string<br>"; 
} 
} 
else 
{ 
echo "<font color='red'>Website is offline</font>";}
echo $deb; 
include('includes/footer.php'); 
?>
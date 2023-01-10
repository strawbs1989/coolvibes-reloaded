<div class='radio_text'><?php
$open = fsockopen("virtual-nexus.de","10000"); 
if ($open) {

fputs($open,"GET /7.html HTTP/1.1\nUser-Agent:Mozilla\n\n"); 
$read = fread($open,1000); 
$text = explode("content-type:text/html",$read); 
$text = explode(",",$text[1]); 
} else { $er="Connection Refused!"; } 
if ($text[1]==1) { $state = "Up"; } else { $state = "Down"; } 
if ($er) { echo $er; exit; } 
$listener = str_replace('<HTML><meta http-equiv="Pragma" content="no-cache"></head><body>',"",$text[0]); 
echo "
Listeners: <font color='blue'>$listener of $text[3] ($text[4] Unique)</font><br> 
Listener Peak: <font color='blue'>$text[2]</font><br> 
Server State: <font color='blue'><b>$state</b></font><br> 
Bitrate: <font color='blue'>$text[5] Kbps</font><br> 
</b><hr>";
?>
</div>
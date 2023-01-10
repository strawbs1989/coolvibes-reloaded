<div class="container"><?php
$text = file_get_contents("files/demo.txt");
$text = str_replace("[red]","<font color='red'>",$text);
$text = str_replace("[/red]","</font>",$text);
$text = str_replace("[high]","<h1>",$text);
$text = str_replace("[/high]","</h1>",$text);
$text = str_replace("[break]","<br>",$text);
echo $text;
?></div>
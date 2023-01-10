<center><img src="https://hottunez-radio.com/images/player%20text.png" style="width:300px;" alt="logo"></center><hr>
<nav>
<ul><?php
///format: "page from includes => link title, example for penis.php "penis" => "My Giant Dong", equals: <a href="?p=penis">My Giant Dong</a> 
$navbar = array("?" => "Home","?p=presenters" => "Team List","?p=info" => "Info","?p=text" => "Text");
foreach($navbar as $link => $title)
{
echo "<li><a class='myButton' href='$link'>$title</a></li>";
}
?>
<span><li><a  class="myButton" href="?p=login">Login</a></li></span>
<span><li><a  class="myButton" href="?p=register">Sign Up</a></li></span>
</ul><hr>
</nav>
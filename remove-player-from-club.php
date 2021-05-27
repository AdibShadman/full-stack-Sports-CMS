<?php

include("./includes/initialize.php");

if((!isset($_POST["playerID"])) || !$account->isLoggedIn())
{
	redirect("./index.php");
}
else
{
	$contentManager->removePlayerFromClub($_POST["playerID"], $_POST["clubID"]);
}

?>
<?php

include("./includes/initialize.php");

if((!isset($_POST["accountID"])) || !$account->isLoggedIn())
{
	redirect("./index.php");
}
else
{
	$contentManager->removeTournamentDirector($_POST["accountID"]);
}

?>
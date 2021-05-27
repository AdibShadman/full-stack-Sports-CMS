<?php

require("./includes/initialize.php");

	if(!$account->isLoggedIn())
	{
		redirect("./index.php");
	}
	else
	{
		//check for club expiration.
		if($account->getAccessLevel() > 1)
		{
			$exp = $account->getClubExp();
			
			if ( (time() - strtotime($exp)) > 0 )
			{
				//club expired
				$_SESSION['club-exp'] = $exp;
				redirect('./index.php');
			}
			$clubID = $account->getClubID();
		}
		else
		{
			//not an admin so get club id from post
			$clubID = $_POST['admin-select-club'];
		}
	
		$eventType = $_POST["event-type"];
		$countryID = $_POST["country-id"];
		$stateID = $_POST["state-name"];
		$sportID = $_POST["sport-type"];
		$eventDate = $_POST["event-date"];

		$playersValid = true;

		for ($i = 0; $i < count($_POST['winner-id']); $i++)
		{
			if(!$contentManager->playerExists($_POST['winner-id'][$i]) || !$contentManager->playerExists($_POST['loser-id'][$i]))
			{
				$playersValid = false;
			}
		}

		if(!$contentManager->eventDetailsValid($countryID, $stateID, $sportID, $eventType, $eventDate) && $playersValid)
		{
			redirect("./upload-event.php");
		}
		else
		{
			if(isset($_POST['edit-event-id']))
			{
				//event is being edited. We will need to reset all the players ratings and delete all evidence of the event from the database. 
				$contentManager->resetPlayersRatings($_POST['edit-event-id']);
				$contentManager->deleteEvent($_POST['edit-event-id']);
			}
			
			$eventName = trim($_POST["event-name"]);
			$eventName = preg_replace('/[^A-Za-z0-9" "\-]/', '', $eventName);
			$eventID = $contentManager->createEvent($eventName, $countryID, $stateID, $sportID, $eventType, $eventDate, $clubID);
			
			$mapleFileManager = new MapleFileManager($eventID, $_POST['event-date'], $eventType);
			
			if (!strcmp($eventType, "Single"))
			{
				//singles
				
				//create new game and game_result in the database for each match
				for ($i = 0; $i < count($_POST['winner-id']); $i++)
				{
					$winnerStats = $contentManager->getPlayerCurrentStats($_POST['winner-id'][$i]);
					$loserStats = $contentManager->getPlayerCurrentStats($_POST['loser-id'][$i]);
					
					//create new game in db and get the id
					$gameID = $contentManager->newGame($_POST['winner-id'][$i],$winnerStats['mean'],$winnerStats['standard_deviation'],$_POST['loser-id'][$i],$loserStats['mean'],$loserStats['standard_deviation'],$_POST['winner-set-score'][$i],$_POST['loser-set-score'][$i],$eventID, true);
					
					//add the game to the maple manager
					$mapleFileManager->addMatchData($_POST['winner-id'][$i],$winnerStats['mean'],$winnerStats['standard_deviation'],$winnerStats['last_played'],$_POST['loser-id'][$i],$loserStats['mean'],$loserStats['standard_deviation'],$loserStats['last_played'],$gameID);
				}
			}
			else
			{
				//doubles
				
				for ($i = 0; $i < count($_POST['winner-id']); $i++)
				{
					if (($i % 2) == 0)
					{
						//first player of teams
						$winnerPlayer1 = $_POST['winner-id'][$i];
						$loserPlayer1 = $_POST['loser-id'][$i];
					}
					else
					{
						//second player in teams
						$winnerPlayer2 = $_POST['winner-id'][$i];
						$loserPlayer2 = $_POST['loser-id'][$i];
						
						//for each team check they exist or create them
						if ($contentManager->teamExists($winnerPlayer1,$winnerPlayer2))
						{
							//team exists
							$winnerTeam = $contentManager->getTeamID($winnerPlayer1, $winnerPlayer2);
						}
						else
						{
							//team does not exist
							$winnerTeam = $contentManager->createTeam($winnerPlayer1,$winnerPlayer2);
						}
						
						if ($contentManager->teamExists($loserPlayer1,$loserPlayer2))
						{
							//team exists
							$loserTeam = $contentManager->getTeamID($loserPlayer1, $loserPlayer2);
						}
						else
						{
							//team does not exist
							$loserTeam = $contentManager->createTeam($loserPlayer1,$loserPlayer2);
						}
						
						//get rating for teams for given sport. (note that this helper function will create the rating if needed)
						$winnerStats = $contentManager->getTeamRating($winnerTeam,$sportID);
						$loserStats = $contentManager->getTeamRating($loserTeam,$sportID);
						
						$winnerSetScore = $_POST['winner-set-score'][intdiv($i,2)];
						$loserSetScore = $_POST['loser-set-score'][intdiv($i,2)];
						
						//add match to db and maple file manager
						//create new game in db and get the id
						$gameID = $contentManager->newGame($winnerTeam,$winnerStats['mean'],$winnerStats['standard_deviation'],$loserTeam,$loserStats['mean'],$loserStats['standard_deviation'],$winnerSetScore,$loserSetScore,$eventID, false);
						
						$mapleFileManager->addMatchData($winnerTeam,$winnerStats['mean'],$winnerStats['standard_deviation'],$winnerStats['last_played'],$loserTeam,$loserStats['mean'],$loserStats['standard_deviation'],$loserStats['last_played'],$gameID);
						
					}
					
				}
			}
			
			$mapleFileManager->write();
			$mapleFileManager->addToQueue();
		}
	}
		// redirect and show user some confirmation
		//this will be a redirect. The following is for testing only. 
		//[AJAX query and on result success open popup box?]
	$_SESSION['upload-success'] = true;
	redirect("./account.php");
		
?>

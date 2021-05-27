<?php
require("./includes/initialize.php");

switch($_POST['ajaxMethod'])
{
	case "player-event-history":
		$result = $contentManager->getPlayersRecentEvents($_POST['playerID'], $_POST['sportID'], $_POST['limitOffset']);

		$response = array();

		while ($row = $result->fetch())
		{
			$response[] = array($row);
		}

		echo json_encode($response);
		break;
	case "team-event-history":
		$result = $contentManager->getTeamRecentEvents($_POST['teamID'], $_POST['sportID'], $_POST['limitOffset']);

		$response = array();

		while ($row = $result->fetch())
		{
			$response[] = array($row);
		}

		echo json_encode($response);
		break;
	case "get-player-rating":
		$result = $contentManager->getPlayerRating($_POST['playerID'],$_POST['sportID']);

		$response = array("mean"=>$result['mean'],"sd"=>$result['standard_deviation']);
		echo json_encode($response);
		break;
	case "get-team-rating":
		$result = $contentManager->getTeamRating($_POST['teamID'],$_POST['sportID']);

		$response = array("mean"=>$result['mean'],"sd"=>$result['standard_deviation']);
		echo json_encode($response);
		break;
	case "update-player-teams":
		$playerTeams = $contentManager->getTeamID($_POST['playerID']);

		$response = array();

		while($teams = $playerTeams->fetch())
		{
			$teamPlayers = $contentManager->getTeamPlayersBySport($teams['team_id'], $_POST['sportID']);
			$playerNames = $contentManager->getTeamPlayerNames($teamPlayers['player_one_id'], $teamPlayers['player_two_id']);

			if($playerNames != NULL)
			{
				$response[] = array("teamID"=>$teams['team_id'], "player1"=>$playerNames['player_one'], "player2"=>$playerNames['player_two']);
			}
			else {

			}
		}

 		echo json_encode($response);
		break;
	case "activate-account":
		$account->activateAccount($_POST['accountID']);
		break;
	case "remove-account":
		$account->removeAccount($_POST['accountID']);
		break;
	case "retrieveClubInformation":
		$clubInformation = $account->getClubDetails($_POST['clubID']);

		echo '
			<div id="club-name" class="club-field">
				<p class="club-detail-headers">Name: </p>
				<p id="club-name-value">' . $clubInformation["name"] . '</p>
			</div>
			<div id="club-sport" class="club-field">
				<p class="club-detail-headers">Sport: </p>
				<p id="club-sport-value">' . $clubInformation["sport"] . '</p>
			</div>
			<div id="club-country" class="club-field">
				<p class="club-detail-headers">Country: </p>
				<p id="club-country-value">' . $clubInformation["country"] . '</p>
			</div>
			<div id="club-state" class="club-field">
				<p class="club-detail-headers">State: </p>
				<p id="club-state-value">' . $clubInformation["state"] . '</p>
			</div>';

		break;
	case "editPlayerModal":
		$playerDetails = $contentManager->getSpecificPlayerInformation($_POST['playerID']);

		$editPlayerModal = '
			<div class="register-input-group-double">
          		<input type="text" id="edit-given-name" name="given-name" value="' . $playerDetails["given_name"] . '" placeholder="Given Name" pattern="[a-zA-Z\s]{1,45} required title="Given name must be within 1-45 characters">
          		<input type="text" id="edit-family-name" name="family-name" value="' . $playerDetails["family_name"] . '" placeholder="Family Name" pattern="[a-zA-Z\s]{1,45} required title="Family name must be within 1-45 characters">
        	</div>

        	<div class="register-input-group-double">
          		<select id="player-gender">';

          			if($playerDetails["gender"] == "M")
          			{
          				$editPlayerModal .= '<option value="M" selected>Male</option>
                							 <option value="F">Female</option>';
          			}
          			else
          			{
          				$editPlayerModal .= '<option value="M">Male</option>
                							 <option value="F" selected>Female</option>';
          			}

          		$editPlayerModal .= '</select>
          		<input class="edit-player-date" class="event-field-date" name="event-date" id="event-date" onfocus="(this.type=\'date\')" onblur="(this.type=\'text\')" value="' . $playerDetails["date_of_birth"] . '">
        	</div>

        	<input type="email" value="' . $playerDetails["email"] . '" id="edit-player-email" name="email" placeholder="Email" pattern="{7,75}" required title="Email must not exceed 75 characters">

        	<div class="register-input-group-double">
        	<select id="edit-player-country" name="select-country">';
                $countries = $contentManager->getAllCountries();

                while ($country = $countries->fetch())
                {
                	if($country["name"] == $playerDetails["country_name"])
                	{
                		$editPlayerModal .= '<option value="' . $country["country_id"] . '" selected>' . $country["name"] . '</option>';
                	}
                	else
                	{
                		$editPlayerModal .= '<option value="' . $country["country_id"] . '">' . $country["name"] . '</option>';
                	}
                }

        	$editPlayerModal .= '
        		</select><select id="edit-player-state" name="state-name"></select></div>
        		<button type="button" name="edit-player" id="edit-player-button">Confirm</button>';

        	echo $editPlayerModal;
		break;
	case "editPlayer":
		$contentManager->editPlayer($_POST["playerID"], $_POST["givenName"], $_POST["familyName"], $_POST["gender"], $_POST["dob"], $_POST["email"], $_POST["country"], $_POST["state"]);
		break;
	case "promoteAccount":
		$contentManager->promoteToAccessLevel($_POST["accountID"], 1);
		break;
	case "promoteDirector":
		$contentManager->promoteToDirector($_POST["accountID"], $_POST["clubID"]);
		break;
	case "editAccountModal":
		$editAccountModal = '<div class="register-input-group-double">
          						<input type="text" id="edit-account-given-name" name="given-name" value="' . $_POST["givenName"] . '" placeholder="Given Name" pattern="[a-zA-Z\s]{1,45}" required title="Given name must be within 1-45 characters">
          						<input type="text" id="edit-account-family-name" name="family-name" value="' . $_POST["familyName"] . '" placeholder="Family Name" pattern="[a-zA-Z\s]{1,45}" required title="Family name must be within 1-45 characters">
        					</div>
        					<input type="email" value="' . $_POST["email"] . '" id="edit-account-email" name="email" placeholder="Email" pattern="{7,75}" required title="Email must not exceed 75 characters">
        					<button type="submit" name="update-account-details" id="update-account-details-button">Update</button>';

        echo $editAccountModal;
		break;
	case "initial-rating-Manager":
		if(isset($_POST["setRating"]))
		{
		  $playerID = $_POST["playerID"];
		  $sportID = $_POST["sportID"];

		  $ratingExists = $contentManager->initialRatingExists($playerID, $sportID);

		  if($ratingExists == "true")
		  {
			  echo "1";
		  }
		  else
		  {
			echo "0";
		  }
		}

		if((isset($_POST["meanID"]) && (isset($_POST["sdID"]))))
		{
		  $playerID = $_POST["playerID"];
		  $sportID = $_POST["sportID"];
		  $mean = $_POST["meanID"];
		  $sd = $_POST["sdID"];

		  $result = $contentManager->insertInitialRating($mean, $sd, $playerID, $sportID);
		}
		break;
	case "add-player-manager":
		if((isset($_POST["playerGivenName"])) && (isset($_POST["playerFamilyName"])) && (isset($_POST["playerGenderID"])) && (isset($_POST["playerBirthDate"])) && (isset($_POST["playerEmail"])) && (isset($_POST["playerClubID"])))
		{


		  $contentManager->addPlayer($_POST["playerGivenName"], $_POST["playerFamilyName"], $_POST["playerGenderID"], $_POST["playerBirthDate"], $_POST["playerEmail"], $_POST["playerClubID"]);
		}
		break;
	case "get-all-player":
		$result = $contentManager->getAllPlayersByAdvancedSearch($_POST['name']);
		$response = array();

		while ($row = $result->fetch())
		{
			$response[] = array("id"=>$row['player_id'],"label"=>$row['family_name'].", ".$row['given_name'] . " (" . $row['state'] . ", " . $row['country'] . ")");
		}

		echo json_encode($response);
		break;
	case "is-email-taken":
		$result = $account->emailExists($_POST["email"]);

		if($result)
		{
			echo "true";
		}

		unset($_POST["email"]);
		$account = null;
		break;
	case "get-states-by-country-ID":
		$result = $contentManager->getStatesByCountryID($_POST["countryID"]);
		echo json_encode($result->fetchAll());
		break;
	case "get-player-by-state":
		$result = $contentManager->getPlayersByNameAndState($_POST['name'],$_POST['state']);
		$response = array();

		while ($row = $result->fetch())
		{
			$response[] = array("id"=>$row['player_id'],"label"=>$row['family_name'].", ".$row['given_name']);
		}

		echo json_encode($response);
		break;
	case "get-event-info":
		$result = $contentManager->getEventInformation($_POST['eventID']);
		echo json_encode($result);
		break;
	case "get-event-matches":
		$result = $contentManager->getEventMatches($_POST['eventID'],$_POST['singles']);
		$response = array();

		while ($row = $result->fetch())
		{
			$response[] = $row;
		}

		echo json_encode($response);
		break;
	case "add-existing-player":
		$contentManager->addExistingPlayer($_POST["playerID"], $_POST["clubID"]);
        break;
	default:
		echo "Post Error";
		var_dump($_POST);

}
?>

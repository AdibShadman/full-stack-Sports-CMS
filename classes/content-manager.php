<?php

class ContentManager
{
	private $database;

	public function __construct($database)
	{
		$this->database = $database;
	}


	public function getAllPlayers()
	{
		$query = "SELECT * FROM player";
		$result = $this->database->query($query, null);

		return $result;
	}

	public function createPlayer($givenName, $familyName, $gender, $dob, $email, $country, $state, $club)
	{
		$query = "INSERT INTO player (given_name, family_name, gender, date_of_birth, email, country_id, state_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$result = $this->database->query($query, [$givenName, $familyName, $gender, $dob, $email, $country, $state]);

		$query = "SELECT LAST_INSERT_ID() AS player_id FROM player";
		$result = $this->database->query($query, null)->fetch();
		$playerID = $result["player_id"];

		$query = "INSERT INTO membership (membership.player_id, membership.club_id) VALUES (?, ?)";
		$result = $this->database->query($query, [$playerID, $club]);

		$query = "SELECT club.sport_id AS sport_id FROM club where club.club_id = ?";
		$result = $this->database->query($query, [$club])->fetch();
		$sportID = $result["sport_id"];

		$query = "INSERT INTO rating (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES(0, 0, CURRENT_TIMESTAMP(), $sportID, $playerID, NULL)";
		$result = $this->database->query($query, null);

	}

	public function getSpecificPlayerInformation($playerID)
	{
		$query = "SELECT
										player.player_id,
										player.given_name,
										player.family_name,
										player.gender,
										player.email,
										player.last_played,
										player.date_of_birth,
										country.name AS country_name,
										state.name AS state_name,
										club.name AS club_name
							 FROM
							 			player INNER JOIN
										country ON player.country_id = country.country_id INNER JOIN
										state ON player.state_id = state.state_id INNER JOIN
										membership ON player.player_id = membership.player_id INNER JOIN
										club ON membership.club_id = club.club_id
							 WHERE
							 			player.player_id = ?";

		$result = $this->database->query($query, [$playerID])->fetch();

		return $result;
	}

	public function promoteToAccessLevel($accountID, $access_level)
	{
		$query = "UPDATE account SET access_level = ? WHERE account_id = ?";
		$result = $this->database->query($query, [$access_level, $accountID]);
	}

	public function editPlayer($playerID, $givenName, $familyName, $gender, $dob, $email, $country, $state)
	{
		$query = "UPDATE player set player.given_name = ?, player.family_name = ?, player.gender = ?, player.date_of_birth = ?, player.email = ?, player.country_id = ?, player.state_id = ? WHERE player.player_id = ?";
		$result = $this->database->query($query, [$givenName, $familyName, $gender, $dob, $email, $country, $state, $playerID]);
	}

	public function getPlayerClub($player_id)
	{
		$query = "SELECT club.name FROM club INNER JOIN membership on membership.club_id = club.club_id WHERE player_id = ?";
		$result = $this->database->query($query, [$player_id])->fetch();

		return $result;
	}

	public function promoteToDirector($accountID, $clubID)
	{
		$query = "INSERT INTO director_of (account_id, club_id) VALUES (?, ?)";
		$result = $this->database->query($query, [$accountID, $clubID]);
	}

	public function getPlayerSports($playerID)
	{
		$query = "SELECT DISTINCT rating.sport_id, sport.name FROM rating INNER JOIN sport ON rating.sport_id = sport.sport_id WHERE player_id = ? ";

		$result = $this->database->query($query, [$playerID]);

		return $result;
	}

	public function getPlayerRating($playerId, $sportID)
	{
		$query = "SELECT * FROM `rating` WHERE `player_id`= ? AND `sport_id`= ?";
		$result = $this->database->query($query, [$playerId, $sportID])->fetch();

		return $result;
	}

	public function getPlayersRecentEvents($playerId, $sportID, $limitOffset = 0, $limitCount = 5)
	{
		$query = 	"SELECT * FROM
					( SELECT
					event.event_id, event.name as event_name, event.start_date AS event_date, MAX(game_result.game_result_id) AS lastGameResultID
					FROM event
					JOIN game ON event.event_id = game.event_id
					JOIN game_result ON game.game_id = game_result.game_id
					WHERE
					game_result.player_id = ?
					AND
					event.sport_id = ?
					GROUP BY event.event_id
					ORDER BY lastGameResultID DESC
					) AS playerEvents,
					( SELECT
					 game_result.game_result_id AS gameResult,
					 CAST(
						CASE
								WHEN game_result.won = 'Y' THEN
									game.mean_before_winning
								WHEN game_result.won = 'N' THEN
									game.mean_before_losing
								END
							AS SIGNED)
						AS meanBefore,
						CAST(
							CASE
								WHEN game_result.won = 'Y' THEN
									game.mean_after_winning
								WHEN game_result.won = 'N' THEN
									game.mean_before_losing
								END
							AS SIGNED)
						AS meanAfter,
						CAST(
							CASE
								WHEN game_result.won = 'Y' THEN
									game.standard_deviation_before_winning
								WHEN game_result.won = 'N' THEN
									game.standard_deviation_before_losing
								END
							AS SIGNED)
						AS SDBefore,
						CAST(
							CASE
								WHEN game_result.won = 'Y' THEN
									game.standard_deviation_after_winning
								WHEN game_result.won = 'N' THEN
									game.standard_deviation_after_losing
								END
							AS SIGNED)
						AS SDAfter
					 FROM game_result
					 JOIN game ON game_result.game_id = game.game_id
					 ) AS playerRatings
					 WHERE playerRatings.gameResult = playerEvents.lastGameResultID
					LIMIT ?,?";

		$this->database->fixLimitProblem(false);

		$result = $this->database->query($query, [$playerId, $sportID, $limitOffset, $limitCount]);

		$this->database->fixLimitProblem(true);

		return $result;

	}

	public function getAllCountries()
	{
		$query = "SELECT * FROM country";
		$result = $this->database->query($query, null);

		return $result;
	}


	public function getStatesByCountryID($countryID)
	{
		$query = "SELECT state_id, name FROM state WHERE country_id = ?";
		$result = $this->database->query($query, [$countryID]);

		return $result;
	}


	public function getAllSports()
	{
		$query = "SELECT * FROM sport";
		$result = $this->database->query($query, null);

		return $result;
	}


	public function getAllClubs()
	{
		$query = "SELECT club.club_id, club.name FROM club";
		$result = $this->database->query($query, null);

		return $result;
	}


	public function createEvent($name, $countryID, $stateID, $sportType, $eventType, $date, $clubID)
	{
		$formatedDate = date_format(date_create($date), 'Y-m-d');

		$query = "INSERT INTO event (name, type, country_id, state_id, sport_id, start_date) VALUES (?, ?, ?, ?, ?, ?)";

		$result = $this->database->query($query, [$name, $eventType, $countryID, $stateID, $sportType, $formatedDate]);

		$idQuery = $this->database->query("SELECT LAST_INSERT_ID()", null);
		$id = $idQuery->fetchColumn();

		$query = "INSERT INTO `plays_at` (`club_id`, `event_id`) VALUES (?, ?);";
		$result = $this->database->query($query,[$clubID,$id]);

		return $id;
	}

	public function newGame($winnerID, $winnerMean, $winnerSD, $loserID, $loserMean, $loserSD, $winnerScore, $loserScore, $eventID, $singles)
	{
		//create game
		$query = "INSERT INTO `game` (`game_id`, `mean_before_winning`, `mean_after_winning`, `standard_deviation_before_winning`, `standard_deviation_after_winning`, `mean_before_losing`, `mean_after_losing`, `standard_deviation_before_losing`, `standard_deviation_after_losing`, `winner_score`, `loser_score`, `event_id`) VALUES (NULL, ?, NULL, ?, NULL, ?, NULL, ?, NULL, ?, ?, ?)";

		$result = $this->database->query($query,[$winnerMean,$winnerSD,$loserMean,$loserSD,$winnerScore,$loserScore,$eventID]);

		//get game id
		$idQuery = $this->database->query("SELECT LAST_INSERT_ID()", null);
		$gameID = $idQuery->fetchColumn();

		//create game result for both winner and loser
		if ($singles)
		{
			$query = "INSERT INTO `game_result` (`game_result_id`, `won`, `player_id`, `game_id`) VALUES (NULL, ?, ?, ?)";
		}
		else
		{
			$query = "INSERT INTO `game_result` (`game_result_id`, `won`, `team_id`, `game_id`) VALUES (NULL, ?, ?, ?)";
		}
		$result = $this->database->query($query,['Y', $winnerID, $gameID]);
		$result = $this->database->query($query,['N', $loserID, $gameID]);

		return $gameID;

	}

	public function getPlayerCurrentStats($playerID)
	{
		$query = 	"SELECT player.last_played, rating.mean, rating.standard_deviation
					FROM player, rating
					WHERE
						rating.player_id = ?
						AND
						rating.player_id = player.player_id";
		$result = $this->database->query($query,[$playerID])->fetch();

		return $result;
	}


	public function getPlayersByNameAndState($nameFilter, $stateID)
	{
		$unfiltered = preg_replace("/[^a-zA-Z0-9\s]/", "", $nameFilter);
		$nameString = explode(" ", $unfiltered);

		if(count($nameString) == 2)
		{
			$query = "SELECT `player_id`, `given_name`, `family_name` FROM player WHERE (state_id = '" . $stateID . "') AND (given_name LIKE '%" . $nameString[0] . "%' OR
				family_name LIKE '%" . $nameString[1] . "%')";
		}
		else if(count($nameString) == 1)
		{
			$query = "SELECT `player_id`, `given_name`, `family_name` FROM player WHERE (state_id = '" . $stateID . "') AND (given_name LIKE '%" . $nameFilter . "%' OR
				family_name LIKE '%" . $nameFilter . "%')";
		}
		else
		{
			//No error handling atm
		}

		$result = $this->database->query($query, null);

		return $result;
	}

    public function getAllPlayersByAdvancedSearch($nameFilter)
    {
        $unfiltered = preg_replace("/[^a-zA-Z0-9\s]/", "", $nameFilter);
		$nameString = explode(" ", $unfiltered);

		if(count($nameString) == 2)
		{
			$query = "SELECT `player_id`, `given_name`, `family_name`, country.name AS country, state.name AS state FROM player JOIN state ON player.state_id = state.state_id JOIN country ON player.country_id = country.country_id WHERE given_name LIKE '%" . $nameString[0] . "%' OR
				family_name LIKE '%" . $nameString[1] . "%'";
		}
		else if(count($nameString) == 1)
		{
			$query = "SELECT `player_id`, `given_name`, `family_name`, country.name AS country, state.name AS state FROM player JOIN state ON player.state_id = state.state_id JOIN country ON player.country_id = country.country_id WHERE given_name LIKE '%" . $nameFilter . "%' OR
				family_name LIKE '%" . $nameFilter . "%'";
		}
		else
		{
			//No error handling atm
		}

		$result = $this->database->query($query, null);

		return $result;
    }


	public function getEventSport($eventID)
	{
		$query = "SELECT sport_id FROM event WHERE event_id = ?";
		$result = $this->database->query($query,[$eventID])->fetch();

		return $result["sport_id"];
	}


	public function playerExists($playerID)
	{
		$query = "SELECT player_id FROM player WHERE player_id = ?";
		$result = $this->database->query($query, [$playerID]);

		return ($result->rowCount() > 0);
	}


	public function teamExists($playerID1, $playerID2)
	{
		$query = "SELECT team.team_id FROM team
					WHERE
					( team.player_one_id = ? OR team.player_two_id = ?)
					AND
					( team.player_one_id = ? OR team.player_two_id = ?)";
		$result = $this->database->query($query, [$playerID1,$playerID1,$playerID2,$playerID2]);

		return ($result->rowCount() > 0);
	}

	public function getTeamID($playerID)
	{
		$query = "SELECT team_id FROM team WHERE player_one_id = ? OR player_two_id = ?";

		$result = $this->database->query($query, [$playerID, $playerID]);

		return $result;
	}

	public function getTeamSports($teamID)
	{
		$query = "SELECT DISTINCT rating.sport_id, sport.name FROM rating INNER JOIN sport ON rating.sport_id = sport.sport_id WHERE rating.team_id = ?";

		$result = $this->database->query($query, [$teamID]);

		return $result;
	}

	/*public function listPlayerTeams($playerID)
	{
		$query = "SELECT team_id FROM team WHERE player_one_id = ? OR player_two_id = ?";

		$result = $this->database->query($query, [$playerID, $playerID])->fetch();

		return $result;
	}*/

	public function createTeam($playerID1, $playerID2)
	{
		$query = "INSERT INTO `team` (`team_id`, `player_one_id`, `player_two_id`) VALUES (NULL, ?, ?);";
		$result = $this->database->query($query, [$playerID1, $playerID2]);

		//get team id
		$idQuery = $this->database->query("SELECT LAST_INSERT_ID()", null);
		$id = $idQuery->fetchColumn();

		return $id;
	}


	//returns team rating for a given sport.
	//if they have never played a given sport a rating is created
	//based on clients requirements
	public function getTeamRating($teamID, $sportID)
	{
		$selectQuery = "SELECT rating.*, LEAST(player1.last_played, player2.last_played) AS last_played
						FROM rating
						JOIN team ON team.team_id = rating.team_id
						JOIN player player1 ON player1.player_id = team.player_one_id
						JOIN player player2 ON player2.player_id = team.player_two_id
						WHERE rating.team_id = ? AND rating.sport_id = ?";
		$result = $this->database->query($selectQuery, [$teamID, $sportID]);

		if ($result->rowCount() == 0)
		{
			//never played the sport need to create a rating.
			//By definition rating is defined as
			//rating = (player_a + player_b) / 2
			//SD = ((player_a + player_b) / 2) + 50

			$players = $this->getTeamPlayersBySport($teamID, $sportID);

			$player1Rating = $this->getPlayerRating($players['player_one_id'],$sportID);
			$player2Rating = $this->getPlayerRating($players['player_two_id'],$sportID);

			$teamMean = ($player1Rating['mean'] + $player2Rating['mean']) / 2;
			$teamSD = (($player1Rating['mean'] + $player2Rating['mean']) / 2) + 50;


			$query = "INSERT INTO `rating` (`rating_id`, `mean`, `standard_deviation`, `last_calculated`, `sport_id`, `player_id`, `team_id`) VALUES (NULL, ?, ?, NOW(), ?, NULL, ?)";
			$insertResult = $this->database->query($query, [$teamMean, $teamSD, $sportID, $teamID]);

			$result = $this->database->query($selectQuery, [$teamID, $sportID]);
		}

		return $result->fetch();
	}

	public function getTeamRecentEvents($teamID, $sportID, $limitOffset = 0, $limitCount = 5)
	{
		$query = 	"SELECT * FROM
					( SELECT
					event.event_id, event.name as event_name, event.start_date AS event_date, MAX(game_result.game_result_id) AS lastGameResultID
					FROM event
					JOIN game ON event.event_id = game.event_id
					JOIN game_result ON game.game_id = game_result.game_id
					WHERE
					game_result.team_id = ?
					AND
					event.sport_id = ?
					GROUP BY event.event_id
					ORDER BY lastGameResultID DESC
					) AS teamEvents,
					( SELECT
					 game_result.game_result_id AS gameResult,
						CAST(
							CASE
									WHEN game_result.won = 'Y' THEN
										game.mean_before_winning
									WHEN game_result.won = 'N' THEN
										game.mean_before_losing
									END
								AS SIGNED)
							AS meanBefore,
						CAST(
							CASE
								WHEN game_result.won = 'Y' THEN
									game.mean_after_winning
								WHEN game_result.won = 'N' THEN
									game.mean_before_losing
								END
							AS SIGNED)
						AS meanAfter,
						CAST(
							CASE
								WHEN game_result.won = 'Y' THEN
									game.standard_deviation_before_winning
								WHEN game_result.won = 'N' THEN
									game.standard_deviation_before_losing
								END
							AS SIGNED)
						AS SDBefore,
						CAST(
							CASE
								WHEN game_result.won = 'Y' THEN
									game.standard_deviation_after_winning
								WHEN game_result.won = 'N' THEN
									game.standard_deviation_after_losing
								END
							AS SIGNED)
						AS SDAfter
					 FROM game_result
					 JOIN game ON game_result.game_id = game.game_id
					 ) AS teamRatings
					 WHERE teamRatings.gameResult = teamEvents.lastGameResultID
					LIMIT ?,?";

		$this->database->fixLimitProblem(false);

		$result = $this->database->query($query, [$teamID, $sportID, $limitOffset, $limitCount]);

		$this->database->fixLimitProblem(true);

		return $result;

	}

	public function getTeamPlayersBySport($teamID, $sportID)
	{
		$query = "SELECT * FROM team INNER JOIN rating on rating.team_id = team.team_id
					WHERE
					team.team_id = ? AND rating.sport_id = ?";
		$result = $this->database->query($query, [$teamID, $sportID])->fetch();

		return $result;
	}

	public function getTeamPlayerNames($playerOneID, $playerTwoID)
	{
		$query = "SELECT * FROM
				  (
				  	SELECT
						CONCAT_WS(' ', given_name, family_name) AS player_one
					FROM
						player
					WHERE
						player_id = ?
				  ) AS one,
				  (
				  	SELECT
				  		CONCAT_WS(' ', given_name, family_name) AS player_two
				  	FROM
				  		player
				  	WHERE
				  		player_id = ?
				  ) AS two";

		$result = $this->database->query($query, [$playerOneID, $playerTwoID])->fetch();

		return $result;
	}


	public function countryExists($countryID)
	{
		$query = "SELECT country_id FROM country WHERE country_id = ?";
		$result = $this->database->query($query, [$countryID]);

		return ($result->rowCount() > 0);
	}


	public function stateExists($stateID)
	{
		$query = "SELECT state_id FROM state WHERE state_id = ?";
		$result = $this->database->query($query, [$stateID]);

		return ($result->rowCount() > 0);
	}


	public function sportExists($sportID)
	{
		$query = "SELECT sport_id FROM sport WHERE sport_id = ?";
		$result = $this->database->query($query, [$sportID]);

		return ($result->rowCount() > 0);
	}


	public function eventTypeIsValid($eventType)
	{
		$isValid = false;

		if(strcmp($eventType, 'Single') == 0 || strcmp($eventType, 'Double') == 0)
		{
			$isValid = true;
		}

		return $isValid;
	}


	public function eventDateIsValid($eventDate)
	{
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\z/', $eventDate))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public function eventDetailsValid($countryID, $stateID, $sportID, $eventType, $eventDate)
	{
		$isValid = true;

		if(!$this->countryExists($countryID) || !$this->stateExists($stateID) || !$this->sportExists($sportID) || !$this->eventTypeIsValid($eventType) || !$this->eventDateIsValid($eventDate))
		{
			$isValid = false;
		}

		return $isValid;
	}


	public function getEventsAttendedByClub($clubID, $start, $amount, $searchTerm)
	{
		$query = "SELECT DISTINCT
					event.name AS event_name, event.event_id, event.type, DATE_FORMAT(event.start_date, '%d %M %Y') as start_date, country.name AS country_name
				  FROM
				  	event
				  INNER JOIN
				  	plays_at on event.event_id = plays_at.event_id
				  INNER JOIN
				  	country on event.country_id = country.country_id
				  WHERE
				  	plays_at.club_id = ?
				  AND
				  	event.name
				  LIKE
				  	?
				  ORDER BY
				  	start_date
				  DESC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, [$clubID, "$searchTerm%"]);
		return $result;
	}

	public function getTotalNumberOfAttendedEvents($clubID, $searchTerm)
	{
		$query = "SELECT DISTINCT
					event.name AS event_name, event.event_id, event.type, event.start_date, country.name AS country_name
				  FROM
				  	event
				  INNER JOIN
				  	plays_at on event.event_id = plays_at.event_id
				  INNER JOIN
				  	country on event.country_id = country.country_id
				  WHERE
				  	plays_at.club_id = ?
				  AND
				  	event.name
				  LIKE
				  	?";

		$result = $this->database->query($query, [$clubID, "$searchTerm%"]);
		return $result->rowCount();
	}

	public function getPlayersByClub($clubID, $start, $amount, $searchTerm)
	{
		$query = "SELECT
					DISTINCT CONCAT(player.given_name, ' ', player.family_name) AS player_name, player.player_id, player.gender, DATE_FORMAT(player.date_of_birth, '%d %M %Y') as date_of_birth,
					CAST(rating.mean AS SIGNED) AS mean,
					CAST(rating.standard_deviation AS SIGNED) AS standard_deviation
				  FROM
				  	club
				  JOIN
				  	membership on membership.club_id = club.club_id
                  JOIN
                    player ON player.player_id = membership.player_id
				  JOIN
				  	rating on rating.player_id = player.player_id AND rating.sport_id = club.sport_id
				  WHERE
				  	membership.club_id = ?
				  AND
				  	(player.given_name
				  LIKE
				  	?
				  OR
				  	player.family_name
				  LIKE
				  	?)
				  ORDER BY
				  	player_name
				  ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, [$clubID, "$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumPlayersByClub($clubID, $searchTerm)
	{
		$query = "SELECT
					DISTINCT CONCAT(player.given_name, ' ', player.family_name) AS player_name, player.email, player.gender, player.date_of_birth, rating.mean
				  FROM
				  	player
				  INNER JOIN
				  	membership on membership.player_id = player.player_id
				  INNER JOIN
				  	rating on rating.player_id = player.player_id
				  INNER JOIN
				    club on club.sport_id = rating.sport_id
				  WHERE
				  	membership.club_id = ?
				  AND
				  	(player.given_name
				  LIKE
				  	?
				  OR
				  	player.family_name
				  LIKE
				  	?)";

		$result = $this->database->query($query, [$clubID, "$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function getClubDirectors($clubID, $start, $amount, $searchTerm)
	{
		$query = "SELECT
					DISTINCT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  FROM
				  	account
				  INNER JOIN
				  	director_of on director_of.account_id = account.account_id
				  WHERE
				  	director_of.club_id = ?
				  AND
				  	(account.given_name
				  LIKE
				  	?
				  OR
				  	account.family_name
				  LIKE
				  	?)
				  ORDER BY
				  	account_name
				  ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, [$clubID, "$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumClubDirectors($clubID, $searchTerm)
	{
		$query = "SELECT
					DISTINCT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  FROM
				  	account
				  INNER JOIN
				  	director_of on director_of.account_id = account.account_id
				  WHERE
				  	director_of.club_id = ?
				  AND
				  	(account.given_name
				  LIKE
				  	?
				  OR
				  	account.family_name
				  LIKE
				  	?)";

		$result = $this->database->query($query, [$clubID, "$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function removeTournamentDirector($account_id)
	{

		$query = "DELETE FROM director_of WHERE director_of.account_id = ?";
		$result = $this->database->query($query, [$account_id]);

		return $result;
	}

	public function getAdministrators($start, $amount, $searchTerm)
	{
		$query = "SELECT
					DISTINCT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  FROM
				  	account
				  WHERE
				  	account.access_level = 1
				  AND
				  	(account.given_name
				  LIKE
				  	?
				  OR
				  	account.family_name
				  LIKE
				  	?)
				  ORDER BY
				  	account_name
				  ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumAdministrators($searchTerm)
	{
		$query = "SELECT
					DISTINCT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  FROM
				  	account
				  WHERE
				  	account.access_level = 1
				  AND
				  	(account.given_name
				  LIKE
				  	?
				  OR
				  	account.family_name
				  LIKE
				  	?)";

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function getInactiveAccounts($start, $amount, $searchTerm)
	{
		$query = "SELECT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  from account where active = 'N' AND (account.given_name LIKE ? OR account.family_name LIKE ? OR account.email LIKE ?)
				  ORDER BY account.date_created ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumInactiveAccounts($searchTerm)
	{
		$query = "SELECT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  from account where active = 'N' AND (account.given_name LIKE ? OR account.family_name LIKE ? OR account.email LIKE ?)";

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function getPotentialAdministrators($start, $amount, $searchTerm)
	{
		$query = "SELECT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  from account where active = 'Y' AND account.access_level = 2 AND account.account_id NOT IN (SELECT director_of.account_id FROM director_of) AND (account.given_name LIKE ? OR account.family_name LIKE ? OR account.email LIKE ?) ORDER BY account.date_created ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumPotentialAdministrators($searchTerm)
	{
		$query = "SELECT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  from account where active = 'Y' AND account.access_level = 2 AND account.account_id NOT IN (SELECT director_of.account_id FROM director_of) AND (account.given_name LIKE ? OR account.family_name LIKE ? OR account.email LIKE ?)";

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function getPotentialExistingPlayers($start, $amount, $searchTerm, $clubID)
	{
		$query = "SELECT DISTINCT CONCAT(player.given_name, ' ', player.family_name) AS player_name, player.player_id, player.email
				  from player WHERE player.given_name LIKE ? OR player.family_name LIKE ? OR player.email LIKE ? ORDER BY player_name ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumPotentialExistingPlayers($searchTerm, $clubID)
	{
		$query = "SELECT DISTINCT CONCAT(player.given_name, ' ', player.family_name) AS player_name, player.player_id, player.email
				  from player WHERE (player.given_name LIKE ? OR player.family_name LIKE ? OR player.email LIKE ?)";

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function getPotentialDirectors($start, $amount, $searchTerm)
	{
		$query = "SELECT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  from account where active = 'Y' AND account.access_level = 2 AND account.account_id NOT IN (SELECT director_of.account_id FROM director_of) AND (account.given_name LIKE ? OR account.family_name LIKE ? OR account.email LIKE ?) ORDER BY account.date_created ASC LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result;
	}

	public function getNumPotentialDirectors($searchTerm)
	{
		$query = "SELECT CONCAT(account.given_name, ' ', account.family_name) AS account_name, account.account_id, account.email
				  from account where active = 'Y' AND account.access_level = 2 AND account.account_id NOT IN (SELECT director_of.account_id FROM director_of) AND (account.given_name LIKE ? OR account.family_name LIKE ? OR account.email LIKE ?)";

		$result = $this->database->query($query, ["$searchTerm%", "$searchTerm%", "$searchTerm%"]);
		return $result->rowCount();
	}

	public function removePlayerFromClub($playerID, $clubID)
	{
		$query = "DELETE FROM membership WHERE player_id = ? AND club_id = ?";

		$result = $this->database->query($query, [$playerID, $clubID]);
		return $result;
	}

	/*
	 * After running maple script this function updates the ratings for
	 * winners and losers of each match in a tournament.
	 *
	 */
	public function updateAfterMatchStatisticComputed($tournamentDate, $sportID, $matchID, $winnerID, $winnerNewMean, $winnerNewSD, $loserID, $loserNewMean, $loserNewSD, $doubles)
	{
		//update entry in game
		$query = "UPDATE game
					SET
						game.mean_after_winning = ?,
						game.standard_deviation_after_winning = ?,
						game.mean_after_losing = ?,
						game.standard_deviation_after_losing = ?
					WHERE
						game.game_id = ?;";

		$result = $this->database->query($query,[$winnerNewMean,$winnerNewSD, $loserNewMean, $loserNewSD, $matchID]);

		//update players ratings.

		if ($doubles)
		{
			//doubles match
			$query = "UPDATE rating, team, player
						SET
							player.last_played = STR_TO_DATE(?,'%d/%m/%Y'),
							rating.mean = ?,
							rating.standard_deviation = ?,
							rating.last_calculated = NOW()
						WHERE
							team.team_id= ? AND
							team.team_id = rating.team_id AND
							rating.sport_id = ? AND
                            (
                                player.player_id = team.player_one_id OR
                                player.player_id = team.player_two_id
                             );";
		}
		else
		{
			//singles match
			$query = "UPDATE player, rating
			SET
				player.last_played = STR_TO_DATE(?,'%d/%m/%Y'),
				rating.mean = ?,
				rating.standard_deviation = ?,
				rating.last_calculated = NOW()
			WHERE
				player.player_id = ? AND
				player.player_id = rating.player_id AND
				rating.sport_id = ?;";
		}

		$result = $this->database->query($query,[$tournamentDate,$winnerNewMean,$winnerNewSD,$winnerID,$sportID]);

		$result = $this->database->query($query,[$tournamentDate,$loserNewMean,$loserNewSD,$loserID,$sportID]);
	}

	public function playerSearchFilter($playerName, $playerAgeMin, $playerAgeMax, $lastPlayed, $clubName, $countryName, $stateName, $start, $amount)
	{
		$query = "SELECT
						player.player_id,
						CONCAT_WS(' ', player.family_name, player.given_name) AS player_name,
						TIMESTAMPDIFF(YEAR, player.date_of_birth, CURDATE()) AS player_age,
						DATE_FORMAT(player.last_played, '%d %M %Y') AS last_played,
						club.name AS club_name,
						country.name AS country_name,
						state.name AS state_name
					FROM
						player INNER JOIN
						membership ON membership.player_id = player.player_id INNER JOIN
						club ON club.club_id = membership.club_id INNER JOIN
						country ON country.country_id = player.country_id INNER JOIN
						state ON state.state_id = player.state_id
					WHERE
						(player.family_name LIKE ? OR player.given_name LIKE ? OR CONCAT_WS(' ', player.family_name, player.given_name) LIKE ? OR CONCAT_WS(' ', player.given_name, player.family_name) LIKE ?) AND
						(TIMESTAMPDIFF(YEAR, player.date_of_birth, CURDATE()) BETWEEN ? AND ?) AND
						player.last_played LIKE ? AND
						club.name LIKE ? AND
						country.name LIKE ? AND
						state.name LIKE ?
						ORDER BY CONCAT_WS(' ', player.family_name, player.given_name)
						LIMIT " . $start . ", " . $amount;

		$result = $this->database->query($query, ["$playerName%", "$playerName%", "$playerName%", "$playerName%", $playerAgeMin, $playerAgeMax, "$lastPlayed%", "%$clubName%", "$countryName%", "$stateName%"]);


		return $result;
	}

	public function playerSearchFilterRowCount($playerName, $playerAgeMin, $playerAgeMax, $lastPlayed, $clubName, $countryName, $stateName)
	{
		$query = "SELECT
						player.player_id,
						CONCAT_WS(' ', player.family_name, player.given_name) AS player_name,
						TIMESTAMPDIFF(YEAR, player.date_of_birth, CURDATE()) AS player_age,
						DATE_FORMAT(player.last_played, '%d %M %Y') AS last_played,
						club.name AS club_name,
						country.name AS country_name,
						state.name AS state_name
					FROM
						player INNER JOIN
						membership ON membership.player_id = player.player_id INNER JOIN
						club ON club.club_id = membership.club_id INNER JOIN
						country ON country.country_id = player.country_id INNER JOIN
						state ON state.state_id = player.state_id
					WHERE
						(player.family_name LIKE ? OR player.given_name LIKE ? OR CONCAT_WS(' ', player.family_name, player.given_name) LIKE ? OR CONCAT_WS(' ', player.given_name, player.family_name) LIKE ?) AND
						(TIMESTAMPDIFF(YEAR, player.date_of_birth, CURDATE()) BETWEEN ? AND ?) AND
						player.last_played LIKE ? AND
						club.name LIKE ? AND
						country.name LIKE ? AND
						state.name LIKE ?";

		$result = $this->database->query($query, ["$playerName%", "$playerName%", "$playerName%", "$playerName%", $playerAgeMin, $playerAgeMax, "$lastPlayed%", "%$clubName%", "$countryName%", "$stateName%"]);

		return $result->rowCount();
	}

	public function checkIfPlayerInMultipleClubs($playerID)
	{
		$isPlayerInMultipleClubs = false;

		$getClubs = $this->getPlayerClub($playerID);

		if($getClubs->rowCount() > 1)
		{
			$isPlayerInMultipleClubs = true;
		}

		return $isPlayerInMultipleClubs;
	}

	/**
	 * Retrieves cookie that stores bookmarked players, retrieves their details from the database
	 * and returns an array of players.
	 */
	public function getBookmarkedPlayers()
	{
		$cookie_name = "bookmarked_players";
		$bookmarked = json_decode($_COOKIE[$cookie_name]);
		$bookmarkedPlayers = [];

		foreach ($bookmarked as $b)
		{
			array_push($bookmarkedPlayers, $this->getSpecificPlayerInformation($b));
		}

		return $bookmarkedPlayers;
	}


    public function addPlayer($givenName, $familyName, $gender, $dateOfBirth, $email, $clubID)
    {

    	$filteredGivenName = ucfirst(trim($givenName));
   	 	$filteredFamilyName = ucfirst(trim($familyName));

   		$gender = $gender;

    	$formattedDateOfBirth = date_format(date_create($dateOfBirth),'Y-m-d');

	   	$filteredEmail = strtolower(trim($email));

	    $query1 = "SELECT  country_id FROM club WHERE club_id = ?";
	    $result1 = $this->database->query($query1, [$clubID])->fetch();

	    $query2 = "SELECT  state_id FROM club WHERE club_id = ?";
	    $result2 = $this->database->query($query2, [$clubID])->fetch();


	    $query = "INSERT INTO player (given_name, family_name, gender, date_of_birth, email, country_id, state_id, last_played) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
	    $result = $this->database->query($query, [$filteredGivenName, $filteredFamilyName, $gender, $formattedDateOfBirth, $filteredEmail, $result1["country_id"], $result2["state_id"]]);

	    $query = "select MAX(player_id) as player from player";
	    $playerResult = $this->database->query($query,[])->fetch();

	    $query = "INSERT INTO membership (club_id, player_id) VALUES (?,?)";
	    $result = $this->database->query($query,[$clubID, $playerResult['player']]);
    }

  public function addExistingPlayer($playerID, $clubID)
  {
  	  $query = "INSERT INTO membership (player_id, club_id) VALUES(?, ?)";
  	  $result = $this->database->query($query, [$playerID, $clubID]);
  }


  public function initialRatingExists($playerID, $sportID)
  {
    $query = "SELECT rating.player_id, rating.sport_id from rating where rating.player_id = ? and rating.sport_id = ? and rating.mean != 0";
      $result = $this->database->query($query,[$playerID, $sportID]);

    if($result->rowCount() > 0)
    {
    	return "true";
    }
    else
    {
    	return "false";
    }
  }
    public function insertInitialRating($mean, $sd, $playerID, $sportID)
  {
	if ( $this->getPlayerRating($playerID,$sportID)['mean'] == 0 )
	{
		//player has a rating of 0 for this sport and it need to be updated.
		$query = "UPDATE `rating` SET `mean` = ?,  `standard_deviation` = ? WHERE `rating`.`player_id` = ? AND `rating`.`sport_id` = ?";
	}
	else
	{
		//player has no rating entry of this sport.
		$query = "INSERT INTO rating(mean, standard_deviation, player_id, sport_id, last_calculated) VALUES(?, ?, ?, ?,NOW())";
	}
    $result = $this->database->query($query, [$mean, $sd, $playerID, $sportID]);
  }

  public function searchClubs($search)
  {
		$search = "%" . $search . "%";
		$query = "SELECT club.club_id, club.name as club, sport.name as sport, CONCAT(state.name, ', ', country.name) as region FROM club
			JOIN country ON club.country_id = country.country_id
			JOIN state ON club.state_id = state.state_id
			JOIN sport ON club.sport_id = sport.sport_id
			WHERE
			club.name LIKE ?
			OR state.name LIKE ?
			OR country.name LIKE ?
			OR sport.name LIKE ?";
		$result = $this->database->query($query,[$search,$search,$search,$search]);
		return $result;
  }

  public function searchClubsEXP($search)
  {
	  $search = "%" . $search . "%";
		$query = "SELECT club.club_id, club.name as club, sport.name as sport, CONCAT(state.name, ', ', country.name) as region, club.club_exp FROM club
			JOIN country ON club.country_id = country.country_id
			JOIN state ON club.state_id = state.state_id
			JOIN sport ON club.sport_id = sport.sport_id
			WHERE
			club.name LIKE ?
			OR state.name LIKE ?
			OR country.name LIKE ?
			OR sport.name LIKE ?
			ORDER BY club.club_exp ASC, club.name ASC";
		$result = $this->database->query($query,[$search,$search,$search,$search]);
		return $result;
  }

  public function updateClubEXP($clubID, $exp)
  {
	  $query = "UPDATE `club` SET `club_exp` = ? WHERE `club`.`club_id` = ?;";
	  $result = $this->database->query($query, [$exp, $clubID]);
  }

  public function searchEvents($search)
  {
	  $search = "%" . $search . "%";
		$query = "SELECT event.event_id, event.name as eventName, club.name as clubName, DATE_FORMAT(event.start_date, '%d %M %Y') as date, event.type, CONCAT(state.name, ', ', country.name) as region FROM event
		JOIN plays_at ON plays_at.event_id = event.event_id
		JOIN club ON plays_at.club_id =club.club_id
		JOIN state ON event.state_id = state.state_id
		JOIN country ON event.country_id = country.country_id
		WHERE
		event.name LIKE ? OR
		club.name LIKE ? OR
		country.name LIKE ? OR
		state.name LIKE ?
		ORDER BY event.start_date DESC";
		$result = $this->database->query($query,[$search,$search,$search,$search]);
		return $result;
  }

  public function getEventInformation($eventID)
  {
	  $query = "SELECT event.event_id, event.name, club.name AS club, event.type, DATE_FORMAT(event.start_date, '%d %M %Y') as date, CONCAT(state.name, ', ', country.name) as region, state.state_id, country.country_id, COUNT(game.game_id) AS number_matches FROM event
				JOIN plays_at ON plays_at.event_id = event.event_id
				JOIN club ON plays_at.club_id =club.club_id
				JOIN state ON event.state_id = state.state_id
				JOIN country ON event.country_id = country.country_id
                LEFT JOIN game ON event.event_id = game.event_id
				WHERE event.event_id = ?
                GROUP BY
                	event.event_id,
                    club.name";
		$result = $this->database->query($query,[$eventID])->fetch();
		return $result;
  }

  public function getEventMatches($eventID, $singles)
  {
	  if ($singles)
	  {
		  $query = "SELECT
					game.mean_before_winning, game.mean_after_winning, game.standard_deviation_before_winning, game.standard_deviation_after_winning,
					game.winner_score, CONCAT(winning_player.given_name, ' ', winning_player.family_name) AS winning_name, winning_player.player_id AS winning_id,

					game.mean_before_losing, game.mean_after_losing, game.standard_deviation_before_losing, game.standard_deviation_after_losing,
					game.loser_score, CONCAT(loser_player.given_name, ' ', loser_player.family_name) AS losing_name, loser_player.player_id AS losing_id

					FROM game

					JOIN game_result AS winner_game_result ON (winner_game_result.game_id = game.game_id AND winner_game_result.won = 'Y')

					JOIN game_result AS loser_game_result ON (loser_game_result.game_id = game.game_id AND loser_game_result.won = 'N')

					JOIN player AS winning_player ON winner_game_result.player_id = winning_player.player_id

					JOIN player AS loser_player ON loser_game_result.player_id = loser_player.player_id

					WHERE game.event_id = ?";
	  }
	  else
	  {
		  //doubles

		  $query = "SELECT
					game.mean_before_winning, game.mean_after_winning, game.standard_deviation_before_winning, game.standard_deviation_after_winning,
					game.winner_score, CONCAT(winning_player1.given_name, ' ', winning_player1.family_name) AS winning_name1, CONCAT(winning_player2.given_name, ' ', winning_player2.family_name) AS winning_name2, winning_team.team_id AS winning_id, winning_player1.player_id AS winning_id1, winning_player2.player_id AS winning_id2,

					game.mean_before_losing, game.mean_after_losing, game.standard_deviation_before_losing, game.standard_deviation_after_losing,
					game.loser_score, CONCAT(loser_player1.given_name, ' ', loser_player1.family_name) AS losing_name1, CONCAT(loser_player2.given_name, ' ', loser_player2.family_name) AS losing_name2, losing_team.team_id AS losing_id, loser_player1.player_id AS losing_id1, loser_player2.player_id AS losing_id2

					FROM game

					JOIN game_result AS winner_game_result ON (winner_game_result.game_id = game.game_id AND winner_game_result.won = 'Y')

					JOIN game_result AS loser_game_result ON (loser_game_result.game_id = game.game_id AND loser_game_result.won = 'N')

					JOIN team AS winning_team ON winner_game_result.team_id = winning_team.team_id

                    JOIN team AS losing_team ON loser_game_result.team_id = losing_team.team_id

                    JOIN player AS winning_player1 ON winning_team.player_one_id = winning_player1.player_id

                    JOIN player AS winning_player2 ON winning_team.player_two_id = winning_player2.player_id

					JOIN player AS loser_player1 ON losing_team.player_one_id = loser_player1.player_id

                    JOIN player AS loser_player2 ON losing_team.player_two_id = loser_player2.player_id

					WHERE game.event_id = ?";
	  }
	  $result = $this->database->query($query,[$eventID]);
	  return $result;
  }

  public function getClubInformation($clubID)
  {
	  $query = "SELECT club.club_id, club.name AS club_name, sport.name AS sport_name, CONCAT(state.name, ', ', country.name) as region FROM club
				JOIN sport ON club.sport_id = sport.sport_id
				JOIN state ON club.state_id = state.state_id
				JOIN country ON club.country_id = country.country_id
				WHERE club.club_id = ?";
		$result = $this->database->query($query,[$clubID])->fetch();
		return $result;
  }

  public function getClubsPlayers($clubID)
  {
	  $query = "SELECT player.player_id, CONCAT(player.given_name, ' ', player.family_name) AS player_name, TIMESTAMPDIFF(YEAR, player.date_of_birth, CURDATE()) AS player_age,
				DATE_FORMAT(player.last_played, '%d %M %Y') AS last_played
				FROM player
				JOIN membership ON membership.player_id = player.player_id
				WHERE membership.club_id = ?";
	  $result = $this->database->query($query,[$clubID]);
	  return $result;
  }

  public function getClubEvents($clubID)
  {
	  $query = "SELECT event.event_id, event.name, event.start_date AS date, event.type, CONCAT(state.name, ', ', country.name) as region FROM event
				JOIN plays_at ON plays_at.event_id = event.event_id
				JOIN state ON event.state_id = state.state_id
				JOIN country ON event.country_id = country.country_id
				WHERE plays_at.club_id = ?";
		$result = $this->database->query($query,[$clubID]);
		return $result;
  }

  public function resetPlayersRatings($eventID)
  {
	  $query = "SELECT DISTINCT rating.rating_id,
				CASE
						WHEN game_result.won = 'Y' THEN
							game.mean_before_winning
						WHEN game_result.won = 'N' THEN
							game.mean_before_losing
						END
					AS mean,
				  CASE
						WHEN game_result.won = 'Y' THEN
							game.standard_deviation_before_winning
						WHEN game_result.won = 'N' THEN
							game.standard_deviation_before_losing
						END
					AS sd FROM game_result
					JOIN game ON game_result.game_id = game.game_id
					JOIN event ON game.event_id = event.event_id
					JOIN rating ON
						(rating.player_id = game_result.player_id OR rating.team_id = game_result.team_id)
						 AND rating.sport_id = event.sport_id

					WHERE game.event_id = ?";
		$result = $this->database->query($query,[$eventID]);

		$updateQuery = "UPDATE rating SET mean = ?, standard_deviation = ? WHERE rating_id = ?";

		while ($row = $result->fetch())
		{
			$updateResult = $this->database->query($updateQuery,[$row['mean'],$row['sd'],$row['rating_id']]);
		}
  }

  public function deleteEvent($eventID)
  {
	  $fk =  "SET foreign_key_checks = 0";
	  $result = $this->database->query($fk,[]);
	  $query = "DELETE game_result, game FROM game_result
				JOIN game
				WHERE
                game_result.game_id = game.game_id AND
                game.event_id = ?";
		$result = $this->database->query($query,[$eventID]);
		$query = "DELETE FROM game WHERE game.event_id = ?";
		$result = $this->database->query($query,[$eventID]);
		$query = "DELETE FROM plays_at WHERE plays_at.event_id = ?";
		$result = $this->database->query($query,[$eventID]);
		$query = "DELETE FROM event WHERE event.event_id = ?";
		$result = $this->database->query($query,[$eventID]);

		$fk =  "SET foreign_key_checks = 1";
	  $result = $this->database->query($fk,[]);
  }

}

?>

<?php

class Account
{
	private $database;

	private $accountId;
	private $loggedIn = false;

	public function __construct($database)
	{
		$this->database = $database;
		
		session_start();

		if(isset($_SESSION["accountId"]))
		{
			$this->accountId = $_SESSION["accountId"];
			$this->loggedIn = true;
		}
	}


	public function register($givenName, $familyName, $organisation, $email, $password)
	{
		$filteredGivenName = trim($givenName);
		$filteredFamilyName = trim($familyName);
		$filteredOrganisation = trim($organisation);
		$filteredEmail = trim($email);
		$filteredPassword = trim($password);

		$filteredGivenName = ucfirst($filteredGivenName);
		$filteredFamilyName = ucfirst($filteredFamilyName);
		$filteredOrganisation = ucwords($filteredOrganisation);
		$filteredEmail = strtolower($filteredEmail);

		$hashedPassword = password_hash($filteredPassword, PASSWORD_DEFAULT);

		$query = "INSERT INTO account (given_name, family_name, organisation, email, password) VALUES (?, ?, ?, ?, ?)";
		$result = $this->database->query($query, [$filteredGivenName, $filteredFamilyName, $filteredOrganisation, $filteredEmail, $hashedPassword]);
	}


	public function login($email, $password)
	{
		$filteredEmail = trim($email);
		$filteredPassword = trim($password);

		$filteredEmail = strtolower($filteredEmail);

		if($this->accountIsAuthenticated($filteredEmail, $filteredPassword))
		{
			$isActive = $this->getActiveState($email);
			
			if($isActive == "Y")
			{
				$query = "SELECT account_id FROM account WHERE email = ?";
				$result = $this->database->query($query, [$filteredEmail])->fetch();

				$_SESSION["accountId"] = $result["account_id"];
				$this->accountId = $result["account_id"];
				$this->loggedIn = true;

				redirect("./account.php");
			}
			else
			{
				$_SESSION['account-inactive'] = "accountInactive";
			}
		}
		else
		{
			$_SESSION['login-incorrect'] = "loginIncorrect";
		}
	}


	public function logout()
	{
		unset($this->userId);
		$this->loggedIn = false;

		$_SESSION = array();
		session_destroy();
	}


	public function accountIsAuthenticated($email, $password)
	{
		$authenticated = false;

		$query = "SELECT email, password FROM account WHERE email = ? LIMIT 1";
		$result = $this->database->query($query, [$email])->fetch();

		if(password_verify($password, $result["password"]))
		{
			$authenticated = true;
		}

		return $authenticated;
	}


	public function isLoggedIn()
	{
		return $this->loggedIn;
	}


	public function getAccessLevel()
	{
		$query = "SELECT access_level FROM account WHERE account_id = ?";
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $result["access_level"];
	}


	public function getAccountName()
	{
		$query = "SELECT given_name, family_name FROM account WHERE account_id = ?";
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $name = $result["given_name"] . " " . $result["family_name"];
	}


	public function setAccessLevel($accountID, $access_level)
	{
		$query = "UPDATE account SET access_level = ? WHERE account_id = ?";
		$result = $this->database->query($query, [$access_level, $accountID]);	
	}


	public function getActiveState($email)
	{
		$query = "SELECT active FROM account WHERE email = ?";
		$result = $this->database->query($query, [$email])->fetch();

		return $result["active"];
	}

	public function createClubAndAssignAccount($name, $sportID, $countryID, $stateID)
	{
		$query = "INSERT INTO club (name, sport_id, country_id, state_id, club_exp) VALUES (?, ?, ?, ?, CURDATE() + INTERVAL 1 MONTH)";
		$result = $this->database->query($query, [$name, $sportID, $countryID, $stateID]);

		$query = "INSERT INTO director_of (account_id, club_id) VALUES (?, (SELECT MAX(club_id) FROM club))";
		$result = $this->database->query($query, [$this->getAccountID()]);
	}


	public function setActiveState($email, $state)
	{
		$query = "UPDATE account SET active = ? WHERE email = ?";
		$result = $this->database->query($query, [$state, $email]);		
	}

	public function activateAccount($accountID)
	{
		$query = "UPDATE account SET active = 'Y' WHERE account_id = ?";
		$result = $this->database->query($query, [$accountID]);
	}

	public function removeAccount($accountID)
	{
		$query = "DELETE from account where account_id = ?";
		$result = $this->database->query($query, [$accountID]);
	}

	public function getAccountID()
	{
		return $this->accountId;
	}

	public function hasClubAssigned()
	{
		$query = "SELECT * from director_of WHERE account_id = ?";
		$result = $this->database->query($query, [$this->getAccountID()]);

		return ($result->rowCount() > 0);
	}

	public function getClubID()
	{
		$query = "SELECT director_of.club_id from director_of WHERE account_id = ?";
		$result = $this->database->query($query, [$this->getAccountID()])->fetch();

		return $result["club_id"];
	}

	public function getClubDetails($clubID)
	{
		$query = "SELECT club.name, sport.name AS sport, country.name AS country, state.name AS state FROM club INNER JOIN sport on sport.sport_id = club.sport_id 
		INNER JOIN country on country.country_id = club.country_id INNER JOIN state on state.state_id = club.state_id WHERE club.club_id = ?";
		$result = $this->database->query($query, [$clubID])->fetch();

		return $result;
	}

	public function getAllInactiveAccounts()
	{
		$query = "SELECT * FROM account WHERE active = ?";
		$result = $this->database->query($query, ["N"]);

		return $result;
	}


	public function emailExists($email)
	{
		$filteredEmail = strtolower($email);

		$query = "SELECT email FROM account WHERE email = ?";
		$result = $this->database->query($query, [$filteredEmail]);

		return ($result->rowCount() > 0);	
	}


	public function setToken($email, $token)
	{
		$filteredEmail = trim($email);
		$datetime = new DateTime();
		$datetime->setTimezone(new DateTimeZone('Australia/Melbourne'));
		$datetime->modify("+60 minutes");
		$dateString = $datetime->format('Y-m-d H:i:s');

		$query = "UPDATE account SET token = ?, token_expiration_date = ? WHERE email = ?";
		$result = $this->database->query($query, [$token, $dateString, $filteredEmail]);
	}


	public function sendRecoveryEmail($email, $token)
	{
		$mailer = new PHPMailer();
		$mailer->isSMTP();
		$mailer->Host = 'smtp.gmail.com';
		$mailer->Port = 587;
		$mailer->SMTPAuth = true;
		$mailer->SMTPSecure = 'tls';

		$mailer->Username = 'grantaupson@gmail.com';
		$mailer->Password = 'passwordFAKE';

		$mailer->setFrom('grantaupson@gmail.com', 'Grant');
		$mailer->addAddress($email, 'Grant');
		$mailer->isHTML(true);

  		$mailer->Subject = "Reset Password";
  		$mailer->Body = "Hello, <br><br> In order to reset your password, please click on the link below: <br>
  					  <a href='http://localhost/Sports-CMS/index.php?email=$email&token=$token'>http://localhost/Sports-CMS/index.php?email=$email&token=$token</a> <br><br>Kind regards,<br> Peterman Ratings";

  		if(!$mailer->send()) 
  		{
   			echo 'Message could not be sent.';
   			echo 'Mailer Error: ' . $mailer->ErrorInfo;
   			exit;
		}
	}


	public function tokenVerified($email, $token)
	{
		$query = "SELECT account_id FROM account WHERE email = ? AND token = ? AND token_expiration_date > NOW()";
		$result = $this->database->query($query, [$email, $token]);

		return ($result->rowCount() > 0);
	}


	public function changePassword($email, $password)
	{
		$filteredPassword = trim($password);
		$hashedPassword = password_hash($filteredPassword, PASSWORD_DEFAULT);

		$query = "UPDATE account SET password = ? WHERE email = ?";
		$result = $this->database->query($query, [$hashedPassword, $email]);
	}


	public function getRegisteredClubID()
	{
		$query = "SELECT club.club_id FROM club INNER JOIN director_of ON director_of.club_id = club.club_id WHERE director_of.account_id = ?";
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $result["club_id"];
	}


	public function getRegisteredClubName()
	{
		$query = "SELECT name FROM club INNER JOIN director_of ON director_of.club_id = club.club_id WHERE director_of.account_id = ?";
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $result["name"];
	}
	
	public function getRegisteredClubSportID()
	{
		$query = "SELECT club.sport_id FROM club INNER JOIN director_of ON director_of.club_id = club.club_id WHERE director_of.account_id = ?";
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $result["sport_id"];
	}

	public function getAccountDetails()
	{
		$query = "SELECT account.account_id, account.given_name, account.family_name, account.email FROM account where account.account_id = ?";
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $result;
	}
	
	public function getRegisteredClubRegion()
	{
		$query = "SELECT state.state_id, state.name AS state_name, country.country_id, country.name AS country_name, CONCAT(state.name, ', ', country.name) as region FROM account JOIN director_of ON account.account_id = director_of.account_id JOIN club ON director_of.club_id = club.club_id JOIN state ON club.state_id = state.state_id JOIN country ON club.country_id = country.country_id WHERE account.account_id = ?";
		
		$result = $this->database->query($query, [$this->accountId])->fetch();

		return $result;
	}
	
	public function getClubExp()
	{
		$query = "SELECT club.club_exp FROM club
			JOIN director_of ON director_of.club_id = club.club_id
			JOIN account ON director_of.account_id = account.account_id
			WHERE account.account_id = ?";

		$result = $this->database->query($query, [$this->accountId])->fetch();
		
		return $result['club_exp'];
	}
	
	public function updateAccDetails($firstName, $lastName, $email)
	{
		$query = "UPDATE `account` SET `given_name` = ?, family_name = ?, email = ? WHERE `account`.`account_id` = ?";
		
		$result = $this->database->query($query, [$firstName, $lastName, $email, $this->accountId]);
		
	}
}

?>

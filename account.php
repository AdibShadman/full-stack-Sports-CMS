<?php 
    $title = "Peterman Ratings | Account";

    include("./includes/header.php");
    include("./includes/navigation.php");

    if(!$account->isLoggedIn())
    {
    	redirect("./index.php");
    }

    if(isset($_POST["create-club"]))
    {
    	$account->createClubAndAssignAccount($_POST["club-name"], $_POST["select-sport"], $_POST["select-country"], $_POST["state-name"]);
    }

    if(isset($_POST["create-player"]))
    {
    	$contentManager->createPlayer($_POST["player-given-name"], $_POST["player-family-name"], $_POST["create-player-gender"], $_POST["event-date"], $_POST["email"], $_POST["select-country"], $_POST["state-name"], $_POST["hidden-club-ID"]);
    }
    
    if(isset($_POST["update-account-details"]))
    {
		$account->updateAccDetails($_POST['given-name'], $_POST['family-name'], $_POST['email']);
	}
	
	if(isset($_POST["change-password"]))
    {
		$_SESSION['reset-email'] = $account->getAccountDetails()['email'];
		include("./includes/reset-password-modal.php");
	}

?>

<article id="account-page-article">

<?php
	if($account->getAccessLevel() < 2)
	{
		echo ' <div id="account-container-requests">
			       <div id="account-requests-section">
				       <div id="requests-header" class="account-page-header">
						   <h2>Administrator Requests</h2>
						   <div class="account-searchbar-container">
							   <input type="text" name="account-requests-searchbar" class="account-input-fields" id="requests-searchbar" placeholder="Search Requests.."/> 
							   <input type="image" src="./resources/images/search-icon.png" class="account-search-buttons" id="account-search-requests-button"/>
						   </div>
					   </div>
					   <div id="account-requests-information"> </div>
					   <div id="account-confirm-requests-submission"> </div>
				   </div>
			   </div>

			   <div id="account-container-administration">
				   <div id="account-administration-section">
					   <div id="administration-header" class="account-page-header">
						   <h2>Administrator Details</h2>
						   <div class="account-searchbar-container">
							   <input type="text" name="account-administration-searchbar" class="account-input-fields" id="administration-searchbar" placeholder="Search Administrators.."/> 
							   <input type="image" src="./resources/images/search-icon.png" class="account-search-buttons" id="account-search-administrators-button"/>
						   </div>
					   </div>
					   <div id="account-administrator-information"> </div>
					   <div id="account-remove-administrators-submission"> </div>
				   </div>
			   </div>';
	}
?>

<div id="account-container-clubs">
	<div id="account-club-section">
		<div id="clubs-header" class="account-page-header">
			<h2>Club Management</h2>
			<?php
				$tableOutput = "";

				if($account->getAccessLevel() < 2)
				{
					$tableOutput .= "<select name='Club' id='admin-change-club'>";
					
					$clubs = $contentManager->getAllClubs();

					while ($club = $clubs->fetch())
					{
						$tableOutput .= "<option value=\"".$club["club_id"]."\">".$club["name"]."</option>";
					}

					$tableOutput .= "</select>";
					echo $tableOutput;
				}
			?>
			<div class="account-searchbar-container">
				<input type="text" name="directors-searchbar" class="account-input-fields" id="directors-searchbar" placeholder="Search Club Directors.."/> 
				<input type="image" src="./resources/images/search-icon.png" class="account-search-buttons" id="account-search-directors-button"/>
			</div>
		</div>
		<div id="account-club-information">
			<div id="account-club-details">
				<?php
					if($account->getAccessLevel() > 1)
					{
						if($account->hasClubAssigned())
						{
							$clubID = $account->getClubID();
							$clubInformation = $account->getClubDetails($clubID);

							echo '
							<p id="account-hidden-club-id">' . $clubID . '</p>
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
						}
						else
						{
							echo "<div id='create-club-container'>
									<p id='create-club-text'> Wanting to track the ratings of the players in your club? </p>
							      	<button type='button' id='account-create-club-button' onclick='showCreateClubModal()'>Create Club</button>
							      </div>";
						}
					}
					
				?>
			</div>
			<div id="account-club-directors-header">
				<p>Tournament Directors</p>
			</div>	
			<div id="account-directors-information">
			</div>
			<div id="account-remove-director-submission">
			</div>
		</div>
	</div>
</div>

<div id="account-container-players">
	<div id="account-players-section">
		<div id="players-header" class="account-page-header">
			<h2>Club Members</h2>
			<?php
				$tableOutput = "";

				if($account->getAccessLevel() < 2)
				{
					$tableOutput .= "<select name='Club' id='admin-change-club-members'>";
					
					$clubs = $contentManager->getAllClubs();

					while ($club = $clubs->fetch())
					{
						$tableOutput .= "<option value=\"".$club["club_id"]."\">".$club["name"]."</option>";
					}

					$tableOutput .= "</select>";
					echo $tableOutput;
				}
			?>
			<div class="account-searchbar-container">
				<input type="text" name="club-players-searchbar" class="account-input-fields" id="club-players-searchbar" placeholder="Search Club Players.."/> 
				<input type="image" src="./resources/images/search-icon.png" class="account-search-buttons" id="account-search-players-button"/>
			</div>
		</div>
		<div id="account-players-information">		
		</div>
	</div>
</div>

<div id="account-container-events">
	<div id="account-event-section">
		<div id="event-header" class="account-page-header">
			<h2>Recent Club Events</h2>
			<?php
				$tableOutput = "";

				if($account->getAccessLevel() < 2)
				{
					$tableOutput .= "<select name='Club' id='admin-change-club-events'>";
					
					$clubs = $contentManager->getAllClubs();

					while ($club = $clubs->fetch())
					{
						$tableOutput .= "<option value=\"".$club["club_id"]."\">".$club["name"]."</option>";
					}

					$tableOutput .= "</select>";
					echo $tableOutput;
				}
			?>
			<div class="account-searchbar-container">
				<input type="text" name="recent-events-searchbar" class="account-input-fields" id="event-searchbar" placeholder="Search Recent Club Events.."/> 
				<input type="image" src="./resources/images/search-icon.png" class="account-search-buttons" id="account-search-event-button"/>
			</div>
		</div>
		<div id="account-event-information">
		</div>
		<div id="account-edit-event-submission">
		</div>
	</div>
</div>

<div id="account-container-personal">
	<div id="account-personal-section">
		<div id="personal-header" class="account-page-header">
			<h2>Personal Information</h2>
		</div>
		<div id="account-personal-information">	
			<?php
				$personalInformation = $account->getAccountDetails();

				echo '
				<p id="account-hidden-id">' . $personalInformation["account_id"] . '</p>
				<div id="account-given-name" class="account-field">
					<p class="account-detail-headers">Given Name: </p>
					<p id="account-given-name-value">' . $personalInformation["given_name"] . '</p>
				</div>
				<div id="account-family-name" class="account-field">
					<p class="account-detail-headers">Family Name: </p>
					<p id="account-family-name-value">' . $personalInformation["family_name"] . '</p>
				</div>
				<div id="account-email" class="account-field">
					<p class="account-detail-headers">Email: </p>
					<p id="account-email-value">' . $personalInformation["email"] . '</p>
				</div>

				<button type="button" id="edit-account-details-button">Edit</button>';
			?>
			<form action='./account.php' method='post'>
				<button type="submit" id="edit-account-details-button-change-password" name="change-password">Change Password</button>
			</form>
		</div>
	</div>
</div>
   
</article>

<?php
    include("./includes/footer.php");
    include("./includes/administrator-modal.php");
    include("./includes/director-modal.php");
    include("./includes/create-club-modal.php");
    include("./includes/edit-player-modal.php");
    include("./includes/create-player-modal.php");
    include("./includes/edit-account-modal.php");
    include("./includes/add-existing-player-modal.php");
?>

<script src="./javascript/pagination.js"></script>









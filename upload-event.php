<?php
	$title = "Peterman Ratings | Upload Event";

	include("./includes/header.php");
	include("./includes/navigation.php");
	
	if(!$account->isLoggedIn())
	{
		redirect("./index.php");
	}
	
	$accessLevel = $account->getAccessLevel();
	
	//check for club expiration.
	if($accessLevel > 1)
	{
		$exp = $account->getClubExp();
		
		if ( (time() - strtotime($exp)) > 0 )
		{
			//club expired
			$_SESSION['club-exp'] = $exp;
			redirect('./index.php');
		}
	}
	
	if (isset($_POST['editEventID']))
	{
		$editEventID = $_POST['editEventID'];
	}
	else
	{
		$editEventID = -1;
	}
	
?>

<div>
	<article class="event-details-border">

		<form class="event-upload-form" id="event-upload-form" autocomplete="off" action=".\process-event.php" method="post">
			<input value=<?php echo ("'" . $account->getRegisteredClubRegion()['state_id'] . "'");?> id="home-state" hidden />
			<input value=<?php echo ("'" . $editEventID . "'");?> id="edit-event-id" name="edit-event-id" hidden />
			<h1 class="event-details-header">Event Details</h1>

			<div class="event-form" action="">
				<div class="event-field">
					<input class="event-field-input" type="text" id="event-name" name="event-name"
					placeholder="Event Name" pattern="[a-zA-Z0-9\s]{1,30}" required
					title="Event name must be within 1-30 characters and can contain letters and numbers">
				</div>
				<div class="event-rows">
					<div class="event-details-row">
						<?php
						
						if ($accessLevel < 2)
						{
							?>
						
						<select class="event-type" name='admin-select-club' id='admin-select-club'>
								<?php
									$clubs = $contentManager->getAllClubs();
									while ($club = $clubs->fetch())
									{
										echo "<option value=\"".$club["club_id"]."\">".$club["name"]."</option>";
									}
								?>
						</select>
						
						<?php
						}
						?>
						<input class="event-field-date" name="event-date" id="event-date" placeholder="Event Start Date"
						required type="text" onfocus="(this.type='date')" onblur="(this.type='text')">
						<select class="Host-country" name="country-id" id="country-id">
						<?php
							$countryToSelect = $account->getRegisteredClubRegion()['country_id'];
							$countries = $contentManager->getAllCountries();
							while ($country = $countries->fetch())
							{
								if ($country["country_id"] == $countryToSelect)
								{
									echo "<option selected value=\"".$country["country_id"]."\">".$country["name"]."</option>";
								}
								else
								{
									echo "<option value=\"".$country["country_id"]."\">".$country["name"]."</option>";
								}
							}
						?>
						</select>
						<select class="Host-state" name="state-name" id="state-name"></select>
					</div>

					<div class="event-details-row2">
						
						<div class="event-details-row">
						<?php
						
						if ($accessLevel < 2)
						{
							?>
						
						<select class="event-type" name='sport-type' id='sport-type'>
								<?php
									$sports = $contentManager->getAllSports();
									while ($sport = $sports->fetch())
									{
										echo "<option value=\"".$sport["sport_id"]."\">".$sport["name"]."</option>";
									}
								?>
						</select>
						
						<?php
						}
						else
						{
							?>
							<input value=<?php echo ("'".$account->getRegisteredClubSportID()."'");?> id="sport-type" name="sport-type" hidden />
							<?php
						}
						?>
						
						<select class="event-type" id="event-type" name="event-type" required onchange="changeValue();">
							<option disabled selected value="">Match type</option>
							<option value="Single">Singles</option>
							<option value="Double">Doubles</option>
						</select>
						<input class="match-input" id="match-field-input" type="number" id="match-number"
						name="match-number" placeholder="Number of Matches" pattern="[0-9]{1,3}"
						title="Number must be within 1-300" onkeyup = "changeMatchNumber();" >
						<button class="match-number-input" id="match-number-submit" name="match-number-submission"
						value="Add Matches" type="button" onclick="changeMatchNumber();">Add Matches</button>
					</div>
				</div>
				</div>
				<div class="input-table">
						<p class="fill-help"> Need Help? Click <a class="popover-content" data-toggle="popover"  data-html="true"  data-content="
						<p class=step>Step 1</p><p> Enter your event details in the fields above. Remember, all fields are compulsory to submit the event!</p>
						<p class=step>Step 2</p><p>Pre-fill the page with your event matches by entering the required number of matches into 'Number of Matches'. Finally, click 'Add Matches' to begin entering match details.</p>
						<p class=note>Note: If you make a mistake, don't worry - you can always add or delete additional matches by clicking 'Add more matches' at the bottom of the page, or the 'Delete' button next to each match.</p> 
                        <p class=step>Step 3</p><p> In Match Details, enter details of each match such as the winning and losing player names - if the player exists in our system they will be shown in a drop down menu. If you can't find a player, select 'Advanced Search' - this will find players registered outside of your region. Still can't find them? Select 'Add them here', and fill in their player details.</p>
                        <p class=step>Step 4</p><p> Double check your event details, and click the 'Submit event' button to upload the details. Results will be available to players shortly.</p>">here!</a>
					</p>
					
					<table class="match-input-table" id="match-input-table"></table>
				</div>

			</div>

			<div class="ui-widget" id="submit_event">
				<p class="more-matches"> Need more matches? Click<a name="add-button" id="add-button"
					onclick="addMoreRows(); return false;" href="#"> Here</a></p>
				<input class="match-submit" id="match-final-submit" type="submit" name="event-page-submission"
					value="Submit Event">
			</div>
		</form>
	</article>
</div>

<?php
	include("./includes/advancedPlayerSearch.php");
	include("./includes/add-player.php");
	include("./includes/initialRating.php");
	include("./includes/event-type-notification-modal.php");
	include("./includes/change-match-number-modal.php");
	include("./includes/footer.php");
?>

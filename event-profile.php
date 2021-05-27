<?php
$title = "Peterman Ratings | Event-profile";

include( "./includes/header.php" );
include( "./includes/navigation.php" );


if (isset($_GET['id'])){
	$eventID = $_GET['id'];
}
else
{
	//somehow the user has got to the event profile page without an event id
	//send them to the event search page
	redirect("./events.php");
}


$eventInfo =  $contentManager->getEventInformation($eventID);


function sign( $number ) { 
    return ( $number > 0 ) ? 1 : ( ( $number < 0 ) ? -1 : 0 ); 
} 

?>

<article id="event-profile-page-article">
	<div class="events-information-container">
		<h1><?php echo($eventInfo['name']) ?></h1>
		<h2><?php echo($eventInfo['club']) ?></h2>
		<h2><?php echo($eventInfo['type'] . 's') ?></h2>
		<h2><?php echo($eventInfo['date']) ?></h2>
		<h2><?php echo($eventInfo['region']) ?></h2>
	</div>
	
	<?php
		if (strcmp($eventInfo['type'],"Single") == 0 )
		{
			//singles event
			
			$result = $contentManager->getEventMatches($eventID, true);
			while ($row = $result->fetch())
			{
			?>
			
			<div class="matches-table-container">
		<table class='matches-table'>
			<tr>
				<th> </th>
				<th>
					<?php echo("<a id='player-link' href='./profile.php?profile-id=" . $row['winning_id'] . "'>" . $row['winning_name'] . "</a>"); ?>
				</th>
				<th>defeats</th>
				<th>
					<?php echo("<a id='player-link' href='./profile.php?profile-id=" . $row['losing_id'] . "'>" . $row['losing_name'] . "</a>"); ?>
				</th>
			</tr>
			<tr>

				<tr>
					<td class="strong">Previous Ranking:</td>
					<td>
					<?php echo((int)$row['mean_before_winning'] . " &plusmn" . (int)$row['standard_deviation_before_winning']); ?>
					</td>
					<td>-</td>
					<td>
					<?php echo((int)$row['mean_before_losing'] . " &plusmn" . (int)$row['standard_deviation_before_losing']); ?>
					</td>
				</tr>
				<tr>
					<td class="strong">Ranking Change:</td>
					<td>
					<?php
						$change = ((int)$row['mean_after_winning'] - (int)$row['mean_before_winning']);
						if (sign($change) < 0)
						{
							//negative change
							echo($change);
						}
						else
						{
							echo("+" . $change);
						}
					?>
					</td>
					<td>-</td>
					<td>
					<?php
						$change = (int)($row['mean_after_losing'] - $row['mean_before_losing']);
						if (sign($change) < 0)
						{
							//negative change
							echo($change);
						}
						else
						{
							echo("+" . $change);
						}
					?>
					</td>
				</tr>
				<tr>
					<td class="strong">New Ranking:</td>
					<td>
					<?php echo((int)$row['mean_after_winning'] . " &plusmn" . (int)$row['standard_deviation_after_winning']); ?>
					</td>
					<td>-</td>
					<td>
					<?php echo((int)$row['mean_after_losing'] . " &plusmn" . (int)$row['standard_deviation_after_losing']); ?>
					</td>
				</tr>
				<tr>
					<td class="strong">Set Score:</td>
					<td><?php echo ($row['winner_score']); ?></td>
					<td>-</td>
					<td><?php echo ($row['loser_score']); ?></td>
				</tr>
		</table>
	</div>
			
			
			<?php
			}
		}
		else
		{
			//doubles
			$result = $contentManager->getEventMatches($eventID, false);
			while ($row = $result->fetch())
			{
	
	?>
	<div class="matches-table-container">
		<table class='matches-table'>
			<tr>
				<th> </th>
				<th>
					<?php echo("<a id='player-link' href='./team-profile.php?team-id=" . $row['winning_id'] . "'>" . $row['winning_name1'] . "</a>"); ?>
				</th>
				<th id="doubleDefeat">defeats</th>
				<th>
					<?php echo("<a id='player-link' href='./team-profile.php?team-id=" . $row['losing_id'] . "'>" . $row['losing_name1'] . "</a>"); ?>
				</th>
				<th> </th>
			</tr>
			<tr>

				<tr>
					<th> </th>
					<th>
						<?php echo("<a id='player-link' href='./team-profile.php?team-id=" . $row['winning_id'] . "'>" . $row['winning_name2'] . "</a>"); ?>
					</th>
					<th> </th>
					<th>
						<?php echo("<a id='player-link' href='./team-profile.php?team-id=" . $row['losing_id'] . "'>" . $row['losing_name2'] . "</a>"); ?>
					</th>
				</tr>
				<tr>

					<tr>
					<td class="strong">Previous Ranking:</td>
					<td>
					<?php echo((int)$row['mean_before_winning'] . " &plusmn" . (int)$row['standard_deviation_before_winning']); ?>
					</td>
					<td>-</td>
					<td>
					<?php echo((int)$row['mean_before_losing'] . " &plusmn" . (int)$row['standard_deviation_before_losing']); ?>
					</td>
				</tr>
				<tr>
					<td class="strong">Ranking Change:</td>
					<td>
					<?php
						$change = ((int)$row['mean_after_winning'] - (int)$row['mean_before_winning']);
						if (sign($change) < 0)
						{
							//negative change
							echo($change);
						}
						else
						{
							echo("+" . $change);
						}
					?>
					</td>
					<td>-</td>
					<td>
					<?php
						$change = (int)($row['mean_after_losing'] - $row['mean_before_losing']);
						if (sign($change) < 0)
						{
							//negative change
							echo($change);
						}
						else
						{
							echo("+" . $change);
						}
					?>
					</td>
				</tr>
				<tr>
					<td class="strong">New Ranking:</td>
					<td>
					<?php echo((int)$row['mean_after_winning'] . " &plusmn" . (int)$row['standard_deviation_after_winning']); ?>
					</td>
					<td>-</td>
					<td>
					<?php echo((int)$row['mean_after_losing'] . " &plusmn" . (int)$row['standard_deviation_after_losing']); ?>
					</td>
				</tr>
				<tr>
					<td class="strong">Set Score:</td>
					<td><?php echo ($row['winner_score']); ?></td>
					<td>-</td>
					<td><?php echo ($row['loser_score']); ?></td>
				</tr>
		</table>
	</div>
	
	<?php
}
}
?>

</article>

<?php
include( "./includes/footer.php" );
?>

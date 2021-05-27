<?php 
    $title = "Peterman Ratings | Club-profile";
	
    include("./includes/header.php");
    include("./includes/navigation.php");
    
    $rowsPerPage = 10;
    
    if (isset($_GET['id'])){
		$clubID = $_GET['id'];
	}
	else
	{
		//somehow the user has got to the event profile page without an event id
		//send them to the event search page
		redirect("./clubs.php");
	}


$clubInfo =  $contentManager->getClubInformation($clubID);

?>

<article id="club-profile-page-article">
  <div class="clubs-information-container">
		<h1><?php echo($clubInfo['club_name']) ?></h1>
		<h2><?php echo($clubInfo['sport_name']) ?></h2>
		<h2><?php echo($clubInfo['region']) ?></h2>
  </div>
  <div class="club-members-container">
      <h2>Club Members List</h2>
      <table class='club-members-table'>
	      <tr>
	      <th>Player</th>
	      <th>Age</th>
	      <th>Last Played</th>
	      </tr>
	      
	      <?php
	      
	      $result = $contentManager->getClubsPlayers($clubID);
			$totalRows = $result->rowCount();
			$pages = ceil($totalRows / $rowsPerPage);
			$count = 0;
			
			while ($row = $result->fetch())
			{
				$page = intdiv($count,$rowsPerPage);
				echo "<tr class='club-players-search-results club-players-search-results-page-" . $page . "'";
				if ($page > 0)
				{
					echo " hidden ";
				}
				echo ">";
				echo "<td><a href='./profile.php?profile-id=" . $row['player_id'] . "'>" . $row['player_name'] . "</a></td>";
				echo "<td>" . $row['player_age'] . "</td>";
				echo "<td>" . $row['last_played'] . "</td>";
				echo "</tr>";
				
				$count++;
			}
	      
	      ?>
        </table>
    
  </div>
    
  <div class="search-pagination-buttons">
		<?php
			if ($pages > 1)
			{
				echo "<span class='club-players-page-selector player-search-link player-search-link-active' id='0'>&lt;&lt;</span>";
				$i = 0;
				while (($i < $pages))
				{
					echo "<span class='club-players-page-selector club-page-selector-inner player-search-link player-search-link-active' id='" . $i . "'>". ($i + 1) ." </span>";
					$i++;
				}
				echo "<span class='club-players-page-selector player-search-link player-search-link-active' id='" . ($pages - 1) . "'>&gt;&gt;</span>";
			}
		?>
    </div>
    
    <div class="events-list-container">
    <h2>Events List</h2>
    <table class='events-list-table'>
	      <tr>
	      <th>Event Name</th>
	      <th>Date</th>
	      <th>Type</th>
          <th>Region</th>
	      </tr>
	      
	      
	      <?php
	      
	      $result = $contentManager->getClubEvents($clubID);
			$totalRows = $result->rowCount();
			$pages = ceil($totalRows / $rowsPerPage);
			$count = 0;
			
			while ($row = $result->fetch())
			{
				$page = intdiv($count,$rowsPerPage);
				echo "<tr class='club-events-search-results club-events-search-results-page-" . $page . "'";
				if ($page > 0)
				{
					echo " hidden ";
				}
				echo ">";
				echo "<td><a href='./event-profile.php?id=" . $row['event_id'] . "'>" . $row['name'] . "</a></td>";
				echo "<td>" . $row['date'] . "</td>";
				echo "<td>" . $row['type'] . "s</td>";
				echo "<td>" . $row['region'] . "</td>";
				echo "</tr>";
				
				$count++;
			}
	      
	      ?>
        
        </table>
    
  </div>
    
    <div class="search-pagination-buttons">
        <?php
			if ($pages > 1)
			{
				echo "<span class='club-events-page-selector player-search-link player-search-link-active' id='0'>&lt;&lt;</span>";
				$i = 0;
				while (($i < $pages))
				{
					echo "<span class='club-events-page-selector club-page-selector-inner player-search-link player-search-link-active' id='" . $i . "'>". ($i + 1) ." </span>";
					$i++;
				}
				echo "<span class='club-events-page-selector player-search-link player-search-link-active' id='" . ($pages - 1) . "'>&gt;&gt;</span>";
			}
		?>
    </div>


</article>

<?php
    include("./includes/footer.php");
?>

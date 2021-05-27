<?php 
    $title = "Peterman Ratings | Events";

    include("./includes/header.php");
    include("./includes/navigation.php");
    
    $rowsPerPage = 15;
    
    if (isset($_POST['search'])){
		$search = $_POST['search'];
	}
	else {
		$search = "";
	}
?>

<article id="event-page-article">

    <div class="event-search-filter-container">

        
        <div id="event-search-filter-line">
            <h1 id="event-search-filter-title">Search for a Event</h1>
        </div>
        
        <div class="search-box">
			<form method='post' action='./events.php'>
				<div class="search-field">
				<input type="txt" class="search-input" name="search" placeholder="Search event"
				<?php
						echo "value='" . $search . "' ";
				?>
				>
				<button class="search-button" type="submit">Search</button>
            </form>
            </div>
        </div>
    </div>
    <div class="search-result-container">
        <table class='search-result-table'>
	      <tr>
	      <th>Event</th>
	      <th>Club</th>
          <th>Date</th>
	      <th>Type</th>
	      <th>Region</th>
	      </tr>
	      
	      
	      
	      
	      <?php
			$result = $contentManager->searchEvents($search);
			$totalRows = $result->rowCount();
			$pages = ceil($totalRows / $rowsPerPage);
			$count = 0;
			
			while ($row = $result->fetch())
			{
				$page = intdiv($count,$rowsPerPage);
				echo "<tr class='event-search-results event-search-results-page-" . $page . "'";
				if ($page > 0)
				{
					echo " hidden ";
				}
				echo ">";
				echo "<td><a id='player-name-link' href='./event-profile.php?id=" . $row['event_id'] . "'>" . $row['eventName'] . "</a></td>";
				echo "<td>" . $row['clubName'] . "</td>";
				echo "<td>" . $row['date'] . "</td>";
				echo "<td>" . $row['type'] . "</td>";
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
				echo "<span class='event-page-selector player-search-link player-search-link-active' id='0'>&lt;&lt;</span>";
				$i = 0;
				while (($i < $pages))
				{
					echo "<span class='event-page-selector club-page-selector-inner player-search-link player-search-link-active' id='" . $i . "'>". ($i + 1) ." </span>";
					$i++;
				}
				echo "<span class='event-page-selector player-search-link player-search-link-active' id='" . ($pages - 1) . "'>&gt;&gt;</span>";
				
			}
		?>
    </div>

</article>
<?php
    include("./includes/footer.php");
?>

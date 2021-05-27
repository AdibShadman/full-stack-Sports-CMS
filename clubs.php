<?php 
    $title = "Peterman Ratings | Clubs";

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

<article id="club-page-article">

    <div class="club-search-filter-container">

        
        <div id="club-search-filter-line">
            <h1 id="club-search-filter-title">Search for a Club</h1>
        </div>
        
        <div class="search-box">
			<form method='post' action='./clubs.php'>
				<div class="search-field">
				<input type="txt" class="search-input" name="search" placeholder="Search club" 
				<?php
					echo "value='" . $search . "' ";
				?>
				>
				<button class="search-button" type="submit">Search</button>
				</div>
            </form>
        </div>
    </div>

    <div class="search-result-container">
        <table class='search-result-table'>
	      <tr> 
	      <th>Club</th>
	      <th>Sport</th>
	      <th>Region</th>
	      </tr>
          
          <?php
			$result = $contentManager->searchClubs($search);
			$totalRows = $result->rowCount();
			$pages = ceil($totalRows / $rowsPerPage);
			$count = 0;
			
			while ($row = $result->fetch())
			{
				$page = intdiv($count,$rowsPerPage);
				echo "<tr class='club-search-results club-search-results-page-" . $page . "'";
				if ($page > 0)
				{
					echo " hidden ";
				}
				echo ">";
				echo "<td><a id='player-name-link' href='./club-profile.php?id=" . $row['club_id'] . "'>" . $row['club'] . "</a></td>";
				echo "<td>" . $row['sport'] . "</td>";
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
				echo "<span class='club-page-selector player-search-link player-search-link-active' id='0'>&lt;&lt;</span>";
				$i = 0;
				while (($i < $pages))
				{
					echo "<span class='club-page-selector club-page-selector-inner player-search-link player-search-link-active' id='" . $i . "'>". ($i + 1) ." </span>";
					$i++;
				}
				echo "<span class='club-page-selector player-search-link player-search-link-active' id='" . ($pages - 1) . "'>&gt;&gt;</span>";
				
			}
		?>
    </div>

</article>


<?php
    include("./includes/footer.php");
?>

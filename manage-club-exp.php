<?php 
    $title = "Peterman Ratings | Manage Club Expiry";

    include("./includes/header.php");
    include("./includes/navigation.php");
    
    
    if($account->getAccessLevel() > 0)
	{
		redirect('./index.php');
	}
	
	if (isset($_POST['exp-date']))
	{
		//An exp date has been set for a club
		var_dump($_POST);
		
		$contentManager->updateClubEXP($_POST['club-id'],$_POST['exp-date']);
		
		$_SESSION['club-exp-name'] =  $_POST['club-name'];
		
		redirect('./manage-club-exp.php');
	}
    
    $rowsPerPage = 15;
    
    if (isset($_POST['search'])){
		$search = $_POST['search'];
	}
	else {
		$search = "";
	}
?>

<article id="manage-club-exp-page-article">

    <div class="club-search-filter-container">

        
        <div id="club-search-filter-line">
            <h1 id="club-search-filter-title">Search for a Club</h1>
        </div>
        
        <div class="search-box">
			<form method='post' action='./manage-club-exp.php'>
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
	      <th>Region</th>
	      <th>Expires</th>
	      </tr>
          
          <?php
			$result = $contentManager->searchClubsEXP($search);
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
				echo "<td>" . $row['region'] . "</td>";
				echo "<td>
				<form action='./manage-club-exp.php' method='post'>
				<input type='hidden' name='club-id' value='" . $row['club_id'] . "' />
				<input type='hidden' name='club-name' value='" . $row['club'] . "' />
				<input type='date' name='exp-date' value='" . $row['club_exp'] . "' />
				<button class='account-table-administrators-button'>Update</button>
				</form></td>";
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

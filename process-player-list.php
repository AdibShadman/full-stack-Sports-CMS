<?php

require("./includes/initialize.php");

$resultsPerPage = 15;
$currentPage = "";
$tableOutput = "";

if(isset($_POST["page"]))
{
	$currentPage = $_POST["page"];
}
else
{
	$currentPage = 1;
}

if(isset($_POST['favouritePost']))
{
	$getFavouritedPlayers = $contentManager->getBookmarkedPlayers();

	echo json_encode($getFavouritedPlayers);
}

if(isset($_POST["submitSearchFilter"]))
{
	$playerName = $_POST['playerName'];
	$playerAgeMin = $_POST['playerAgeMin'];
	$playerAgeMax = $_POST['playerAgeMax'];
	$lastPlayed = $_POST['lastPlayed'];
	$clubName = $_POST['clubName'];
	$countryName = $_POST['countryName'];
	$stateName = $_POST['stateName'];

	$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;

	$searchFilter = $contentManager->playerSearchFilter($playerName, $playerAgeMin, $playerAgeMax, $lastPlayed, $clubName, $countryName, $stateName, $resultsPageToStartFrom, $resultsPerPage);

	$totalPlayersResult = $contentManager->playerSearchFilterRowCount($playerName, $playerAgeMin, $playerAgeMax, $lastPlayed, $clubName, $countryName, $stateName);

	$tableOutput .= "
	  <table class='search-result-table'>
	    <tr>
	      <th>Player</th>
	      <th>Age</th>
	      <th>Last Played</th>
	      <th>Club</th>
	      <th>Region</th>
	    </tr>";

    if($totalPlayersResult != 0)
    {
    	while($row = $searchFilter->fetch())
    	{
    		$tableOutput .= "
    			<tr>
                 <td><a id='player-name-link' href='profile.php?profile-id=".$row["player_id"]."'>".$row["player_name"]."</a></td>
                 <td>".$row["player_age"]."</td>
                 <td>".$row["last_played"]."</td>
                 <td>".$row["club_name"]."</td>
                 <td>".$row["country_name"].", ".$row["state_name"]."</td>
               </tr>";
    	}
    }
    else
    {
        echo "<div class='no-search-result-message'>No player by the given search exists.</div>";
    }

	$tableOutput .= "
        </table>
    <div class='search-pagination-buttons-container'>
    <div class='search-pagination-buttons'>";

    $totalPages = ceil($totalPlayersResult / $resultsPerPage);

    if($totalPages == 0)
    {
        $totalPages = 1;
    }

    if($totalPages < 1)
    {
        $tableOutput .= "<span class='player-search-link player-search-link-active' id='0'><<</span>";
    }
    else
    {
        $tableOutput .= "<span class='player-search-link player-search-link-active' id='1'><<</span>";
    }

    $pageThreshold = $currentPage + 9;

    if($currentPage == 1)
    {
        for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
        {
            $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
        }
    }
    elseif($currentPage == 2)
    {
        for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
        {
            $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
        }
    }
    elseif($currentPage == 3)
        {
            for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
            {
                $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
            }
        }
        elseif($currentPage == 4)
        {
            for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
            {
                $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
            }
        }
    else
    {
        if($currentPage == $totalPages)
        {
            for($i = ($totalPages - 4); $i <= $totalPages; $i++)
            {
                $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
            }
        }
        elseif($currentPage == $totalPages - 1)
        {
            for($i = ($totalPages - 4); $i <= $totalPages; $i++)
            {
                $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
            }
        }
        elseif($currentPage == $totalPages - 2)
        {
            for($i = ($totalPages - 4); $i <= $totalPages; $i++)
            {
                $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
            }
        }
        else
        {
            for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
            {
                $tableOutput .= "<span class='player-search-link player-search-link-active' id='" . $i . "'>" . $i . " </span>";
            }
        }
    }

    $tableOutput .= "
        <span class='player-search-link player-search-link-active' id='" . $totalPages . "'>>></span>
    </div>
    </div>";
}


echo $tableOutput;

?>

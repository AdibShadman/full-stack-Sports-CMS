<?php

	include("./includes/initialize.php");

	if(!$account->isLoggedIn())
	{
		redirect("./index.php");
	}

	$resultsPerPage = 6;
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

	if(isset($_POST["eventID"]))
	{
		$clubID = "";
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";

		if($_POST["clubID"] == "")
		{
			$clubID = $account->getRegisteredClubID();
		}
		else
		{
			$clubID = $_POST["clubID"];
		}

		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$results = $contentManager->getEventsAttendedByClub($clubID, $resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table class='account-tables'>
			<tr>
				<th class='account-row-id'>ID</th>
				<th class='account-row-name'>Name</th>
				<th class='account-row-match-type'>Type</th>
				<th class='account-row-date'>Date</th>
				<th class='account-row-country'>Country</th>
				<th class='account-row-date'></th>				
			</tr>";

		while($row = $results->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['event_id'] . "</td>
					<td class='account-table-data-name'> " . $row['event_name'] . "</td>
					<td> " . $row['type'] . "</td>
					<td> " . $row['start_date'] . "</td>
					<td> " . $row['country_name'] . "</td>
					<td> <form action='./upload-event.php' method='post'><input type='hidden' name='editEventID' value='" . $row['event_id'] . "' /><button class='account-table-events-button'>Edit</button> </form></td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='pagination-buttons-container'>
			<div class='pagination-buttons'>";

		$totalAttendedEvents = $contentManager->getTotalNumberOfAttendedEvents($clubID, $searchTerm);
		$totalPages = ceil($totalAttendedEvents / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='recent-events-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='recent-events-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 4;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='recent-events-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='recent-events-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='recent-events-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='recent-events-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='recent-events-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='recent-events-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='recent-events-link' id=' " . $totalPages . "'>>></span></div></div>";
	}	
	elseif(isset($_POST["playersID"]))
	{
		$clubID = "";
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";

		if($_POST["clubID"] == "")
		{
			$clubID = $account->getRegisteredClubID();
		}
		else
		{
			$clubID = $_POST["clubID"];
		}
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$clubResults = $contentManager->getPlayersByClub($clubID, $resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table class='account-tables'>
			<tr>
				<th class='account-row-id'>ID</th>
				<th class='account-row-name'>Name</th>
				<th class='account-row-gender'>Gender</th>
				<th class='account-row-date'>DOB</th>
				<th class='account-row-rating'>Rating</th>
				<th class='account-row-date'></th>			
			</tr>";

		while($row = $clubResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['player_id'] . "</td>
					<td id='account-table-name-players' class='account-table-data-name'> " . $row['player_name'] . "</td>
					<td> " . $row['gender'] . "</td>
					<td> " . $row['date_of_birth'] . "</td>
					<td> " . $row['mean'] . ' ± ' . $row['standard_deviation'] . "</td>
					<td> <button class='account-edit-players-button'>Edit</button> <button class='account-remove-players-button'>Remove</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='pagination-buttons-container'>
			<div class='pagination-buttons'>";

		$totalClubMembers = $contentManager->getNumPlayersByClub($clubID, $searchTerm);
		$totalPages = ceil($totalClubMembers / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='club-players-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='club-players-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 4;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='club-players-link' id=' " . $totalPages . "'>>></span>";

		if($account->getAccessLevel() < 2 || $account->hasClubAssigned())
		{
			$tableOutput .= "<button type='button' id='account-add-player-button'>Add New Player</button>
							 <button type='button' id='account-add-existing-player-button'>Add Existing Player</button> </div></div>";
		}				
	}
	elseif(isset($_POST["directorID"]))
	{
		$resultsPerPage = 4;
		$clubID = "";

		if($_POST["clubID"] == "")
		{
			$clubID = $account->getRegisteredClubID();
		}
		else
		{
			$clubID = $_POST["clubID"];
		}

		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$directorResults = $contentManager->getClubDirectors($clubID, $resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table id='directors-table'>
			<tr class='account-table-headers'>
				<th class='account-row-id'>ID</th>
				<th class='account-row-name'>Name</th>
				<th class='account-row-email'>Email</th>
				<th class='account-row-date'></th>	
			</tr>";

		while($row = $directorResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['account_id'] . "</td>
					<td class='account-table-data-name'> " . $row['account_name'] . "</td>
					<td class='account-table-data-email'> " . $row['email'] . "</td>
					<td> <button class='account-table-directors-button'>Remove</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='pagination-buttons-container'>
			<div class='pagination-buttons'>";

		$totalDirectors = $contentManager->getNumClubDirectors($clubID, $searchTerm);
		$totalPages = ceil($totalDirectors / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='tournament-directors-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='tournament-directors-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 2;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='tournament-directors-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='tournament-directors-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='club-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='tournament-directors-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='tournament-directors-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='tournament-directors-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='tournament-directors-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='tournament-directors-link' id=' " . $totalPages . "'>>></span>";

		if($account->getAccessLevel() < 2 || $account->hasClubAssigned())
		{
			$tableOutput .= "<button type='button' id='account-add-director-button'>Add Director</button> </div></div>";
		}
	}
	elseif(isset($_POST["administrationID"]))
	{
		$resultsPerPage = 6;
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$administratorResults = $contentManager->getAdministrators($resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table class='account-tables'>
			<tr class='account-table-headers'>
				<th class='account-row-id'>ID</th>
				<th class='account-row-name'>Name</th>
				<th class='account-row-email'>Email</th>
				<th class='account-row-date'></th>	
			</tr>";

		while($row = $administratorResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['account_id'] . "</td>
					<td class='account-table-data-name'> " . $row['account_name'] . "</td>
					<td class='account-table-data-email'> " . $row['email'] . "</td>
					<td> <button class='account-table-administrators-button'>Demote</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='pagination-buttons-container'>
			<div class='pagination-buttons'>";

		$totalAdministrators = $contentManager->getNumAdministrators($searchTerm);
		$totalPages = ceil($totalAdministrators / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='administrators-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='administrators-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 2;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='administrators-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='administrators-link' id=' " . $totalPages . "'>>></span>
						<button type='button' id='account-add-administrator-button'>Add Administrator</button> </div></div>";
	}
	elseif(isset($_POST["inactiveID"]))
	{
		$resultsPerPage = 6;
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$inactiveResults = $contentManager->getInactiveAccounts($resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table class='account-tables' id='account-table-inactive'>
			<tr class='account-table-headers'>
				<th class='account-row-id'>ID</th>
				<th class='account-row-request'>Requests</th>
				<th class='account-row-request-buttons'></th>
				<th class='account-row-request-buttons'></th>	
			</tr>";

		while($row = $inactiveResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['account_id'] . "</td>
					<td class='account-table-data-requests'>" . $row['account_name'] . " (" . $row['email'] . ") requests their account to be activated.</td>
					<td> <button class='account-table-approve-request-button'>Approve</button> </td>
					<td> <button class='account-table-deny-request-button'>Deny</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='pagination-buttons-container'>
			<div class='pagination-buttons'>";

		$totalInactiveAccounts = $contentManager->getNumInactiveAccounts($searchTerm);
		$totalPages = ceil($totalInactiveAccounts / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='admin-requests-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='admin-requests-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 2;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='admin-requests-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='admin-requests-link' id=' " . $totalPages . "'>>></span></div></div>";
	}
	elseif(isset($_POST["administratorModal"]))
	{
		$resultsPerPage = 5;
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$administratorResults = $contentManager->getPotentialAdministrators($resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table class='account-modal-tables'>
			<tr>
				<th class='account-row-id'>ID</th>
				<th class='account-modal-row-name'>Name</th>
				<th class='account-modal--row-email'>Email</th>
				<th class='account-row-date'></th>			
			</tr>";

		while($row = $administratorResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['account_id'] . "</td>
					<td class='account-modal-table-data-name'> " . $row['account_name'] . "</td>
					<td class='account-modal-table-data-email'> " . $row['email'] . "</td>
					<td class='account-modal-data-buttons'> <button class='account-promote-administrator-button'>Promote</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='modal-pagination-buttons-container'>
			<div class='modal-pagination-buttons'>";

		$totalPotentialAdministrators = $contentManager->getNumPotentialAdministrators($searchTerm);
		$totalPages = ceil($totalPotentialAdministrators / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='promote-administrator-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='promote-administrator-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 4;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-administrator-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='promote-administrator-link' id=' " . $totalPages . "'>>></span></div></div>";

	}
	elseif(isset($_POST["directorModal"]))
	{
		$resultsPerPage = 5;
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$directorResults = $contentManager->getPotentialDirectors($resultsPageToStartFrom, $resultsPerPage, $searchTerm);

		$tableOutput .= "
			<table class='account-modal-tables'>
			<tr>
				<th class='account-row-id'>ID</th>
				<th class='account-modal-row-name'>Name</th>
				<th class='account-modal--row-email'>Email</th>
				<th class='account-row-date'></th>			
			</tr>";

		while($row = $directorResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['account_id'] . "</td>
					<td class='account-modal-table-data-name'> " . $row['account_name'] . "</td>
					<td class='account-modal-table-data-email'> " . $row['email'] . "</td>
					<td class='account-modal-data-buttons'> <button class='account-promote-director-button'>Promote</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='modal-pagination-buttons-container'>
			<div class='modal-pagination-buttons'>";

		$totalPotentialDirectors = $contentManager->getNumPotentialDirectors($searchTerm);
		$totalPages = ceil($totalPotentialDirectors / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='promote-director-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='promote-director-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 4;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='promote-director-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='promote-director-link' id=' " . $totalPages . "'>>></span></div></div>";

	}
	elseif(isset($_POST["existingPlayerID"]))
	{
		$resultsPerPage = 5;
		$resultsPageToStartFrom = ($currentPage - 1) * $resultsPerPage;
		$searchTerm = "";
		$clubID = $_POST["clubID"];
		
		if(isset($_POST["searchTerm"]))
		{
			$searchTerm = $_POST["searchTerm"];
		}

		$existingResults = $contentManager->getPotentialExistingPlayers($resultsPageToStartFrom, $resultsPerPage, $searchTerm, $clubID);

		$tableOutput .= "
			<table class='account-modal-tables'>
			<tr>
				<th class='account-row-id'>ID</th>
				<th class='account-modal-row-name'>Name</th>
				<th class='account-modal--row-email'>Email</th>
				<th class='account-row-date'></th>			
			</tr>";

		while($row = $existingResults->fetch())
		{
			$tableOutput .= "
				<tr>
					<td class='account-table-id'> " . $row['player_id'] . "</td>
					<td class='account-modal-table-data-name'> " . $row['player_name'] . "</td>
					<td class='account-modal-table-data-email'> " . $row['email'] . "</td>
					<td class='account-modal-data-buttons'> <button class='add-existing-player-table-button'>Add</button> </td>
				</tr>";
		}

		$tableOutput .= "
			</table>
			<div class='modal-pagination-buttons-container-existing-player'>
			<div class='modal-pagination-buttons'>";

		$totalPotentialExistingPlayers = $contentManager->getNumPotentialExistingPlayers($searchTerm, $clubID);
		$totalPages = ceil($totalPotentialExistingPlayers / $resultsPerPage);

		if($totalPages == 0)
		{
			$totalPages = 1;
		}

		if($totalPages < 1)
		{
			$tableOutput .= "<span class='existing-players-link' id='0'><<</span>";
		}
		else
		{
			$tableOutput .= "<span class='existing-players-link' id='1'><<</span>";
		}

		$pageThreshold = $currentPage + 4;

		if($currentPage == 1)
		{
			for($i = $currentPage; $i <= $pageThreshold AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 2)
		{
			for($i = ($currentPage - 1); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 3)
		{
			for($i = ($currentPage - 2); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		elseif($currentPage == 4)
		{
			for($i = ($currentPage - 3); $i <= ($pageThreshold - 1) AND $i <= $totalPages; $i++)
			{
				$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
			}
		}
		else
		{
			if($currentPage == $totalPages)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 1)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			elseif($currentPage == $totalPages - 2)
			{
				for($i = ($totalPages - 4); $i <= $totalPages; $i++)
				{
					$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
			else
			{
				for($i = ($currentPage - 2); $i <= ($pageThreshold - 2) AND $i < $totalPages; $i++)
				{
					$tableOutput .= "<span class='existing-players-link' id='" . $i . "'>" . $i . " </span>";
				}
			}
		}


		$tableOutput .= "<span class='existing-players-link' id=' " . $totalPages . "'>>></span></div></div>";
	}
	else
	{
		redirect("./index.php");
	}

	echo $tableOutput;
?>

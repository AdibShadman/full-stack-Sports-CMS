window.onload = retrieveRecentEventsForClub(1, "", "");
window.onload = retrieveClubPlayers(1, "", "");
window.onload = retrieveTournamentDirectors(1, "", "");
window.onload = retrieveAdministrators(1, "");
window.onload = retrieveInactiveAccounts(1, "");
window.onload = retrieveSearchedPlayers(1, "", "", "", "", "", "", "", "");
window.onload = showFavouritedPlayers();
window.onload = retrievePotentialAdministrators(1, "");
window.onload = retrievePotentialDirectors(1, "");
window.onload = uploadEventChangeStates($("#create-club-select-country"),$("#create-club-select-state"));

$(document).ready(function()
{
    setupInitialClubInfo();
});

/* EDIT ACCOUNT MODAL */


$(document).on('click', '#edit-account-details-button', function()
{
    showEditAccountModal();
});


/* ADD DIRECTOR MODAL */


function retrievePotentialDirectors(page, searchTerm)
{
	var directorModal = 1;

	$.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, directorModal: directorModal, searchTerm: searchTerm},
        success: function(data)
        {
            $("#director-table-modal-information").html(data);
        }
    });
}

$(document).on('click', '.promote-director-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#add-director-searchbar").val();

    if(page > 0)
    {
        retrievePotentialDirectors(page, searchValue);
    }
});

$(document).on('click', '#account-search-promote-director-button', function()
{
    var searchValue = $("#add-director-searchbar").val();
    retrievePotentialDirectors(1, searchValue);
});

$("#add-director-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-promote-director-button").click();
    }
});

$(document).on('click', '.account-promote-director-button', function()
{
	var accountID = $(this).closest('tr').find('.account-table-id').text();
	var clubID;

	if($("#admin-change-club").length > 0)
    {
        clubID = $("#admin-change-club").find(":selected").val();
    }
    else
    {
    	clubID = $("#account-hidden-club-id").text();
    }

    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "promoteDirector", accountID: accountID, clubID: clubID},
        success: function(data)
        {
            $("#account-search-promote-director-button").click();
            $("#account-search-directors-button").click();
        }
    });
});


$(document).on('click', '#account-add-director-button', function()
{
	showDirectorModal();
	$("#account-search-promote-director-button").click();
});

/* ADD ADMINISTRATOR MODAL */


function retrievePotentialAdministrators(page, searchTerm)
{
	var administratorModal = 1;

	$.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, administratorModal: administratorModal, searchTerm: searchTerm},
        success: function(data)
        {
            $("#administrator-table-modal-information").html(data);
        }
    });
}

$(document).on('click', '.promote-administrator-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#add-administrator-searchbar").val();

    if(page > 0)
    {
        retrievePotentialAdministrators(page, searchValue);
    }
});

$(document).on('click', '#account-search-promote-administrator-button', function()
{
    var searchValue = $("#add-administrator-searchbar").val();
    retrievePotentialAdministrators(1, searchValue);
});

$("#add-administrator-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-promote-administrator-button").click();
    }
});

$(document).on('click', '.account-promote-administrator-button', function()
{
	var accountID = $(this).closest('tr').find('.account-table-id').text();

    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "promoteAccount", accountID: accountID},
        success: function(data)
        {
            retrieveAdministrators(1, "");
            retrievePotentialAdministrators(1, "");
        }
    });
});


/* INACTIVE ACCOUNTS */


function retrieveInactiveAccounts(page, searchTerm)
{
    var inactiveID = 0;

    $.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, inactiveID: inactiveID, searchTerm: searchTerm},
        success: function(data)
        {
            $("#account-requests-information").html(data);
        }
    });
}


$(document).on('click', '.admin-requests-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#requests-searchbar").val();

    if(page > 0)
    {
        retrieveInactiveAccounts(page, searchValue);
    }
});

$(document).on('click', '#account-search-requests-button', function()
{
    var searchValue = $("#requests-searchbar").val();
    retrieveInactiveAccounts(1, searchValue);
});

$("#requests-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-requests-button").click();
    }
});

$(document).on('click', '.account-table-deny-request-button', function(event)
{
    var accountID = $(this).closest('tr').find('.account-table-id').text();
    denyRequest(accountID);

});

$(document).on('click', '.account-table-approve-request-button', function(event)
{
    var accountID = $(this).closest('tr').find('.account-table-id').text();
    approveRequest(accountID);
    retrieveInactiveAccounts(1, "");
});

function approveRequest(accountID)
{
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "activate-account", accountID: accountID},
        success: function(data)
        {
            retrieveInactiveAccounts(1, "");
            showNotificationModal("Account Activation", "The account has been activated successfully.");
        }
    });
}

function denyRequest(accountID)
{
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "remove-account", accountID: accountID},
        success: function(data)
        {
            retrieveInactiveAccounts(1, "");
            showNotificationModal("Account Removal", "The account has been removed successfully.");
        }
    });
}


/* RECENT CLUB EVENTS */


function retrieveRecentEventsForClub(page, searchTerm, clubID)
{
	var eventID = 0;

	$.ajax
	({
		url: "./account-pagination.php",
        type: "POST",
        data: {page: page, eventID: eventID, searchTerm: searchTerm, clubID: clubID},
        success: function(data)
        {
            $("#account-event-information").html(data);
        }
    });
}

$(document).on('click', '.recent-events-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#event-searchbar").val();
    var clubID = "";

    if($("#admin-change-club-events").length > 0)
    {
        var clubID = $("#admin-change-club-events").find(":selected").val();
    }

    if(page > 0)
    {
        retrieveRecentEventsForClub(page, searchValue, clubID);
    }
});

$(document).on('click', '#account-search-event-button', function()
{
    var searchValue = $("#event-searchbar").val();
    var clubID = "";

    if($("#admin-change-club-events").length > 0)
    {
        var clubID = $("#admin-change-club-events").find(":selected").val();
    }

    retrieveRecentEventsForClub(1, searchValue, clubID);
});

$("#event-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-event-button").click();
    }
});

$(document).on('click', '.account-table-events-button', function(event)
{
    var eventID = $(this).closest('tr').find('.account-table-id').text();
    sendEditEventID(eventID);

});

function sendEditEventID(eventID)
{
    var form;
    var inputElement;

    form = document.createElement('form');
    form.action = './upload-event.php';
    form.method = 'post';
    form.name = 'editEventForm';

    inputElement = document.createElement('input');
    inputElement.type = 'hidden';
    inputElement.name = 'editEventID';
    inputElement.value = 2;

    form.appendChild(inputElement);
    document.getElementById('account-edit-event-submission').appendChild(form);
    form.submit();
}

$(document).on('change', '#admin-change-club-events', function()
{
    var searchValue = $("#club-search-event-searchbar").val();
    var clubID = $("#admin-change-club-events").find(":selected").val();
    retrieveRecentEventsForClub(1, searchValue, clubID);
});


/* CLUB PLAYERS */


function retrieveClubPlayers(page, searchTerm, clubID)
{
    var playersID = 0;

    $.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, playersID: playersID, searchTerm: searchTerm, clubID: clubID},
        success: function(data)
        {
            $("#account-players-information").html(data);
        }
    });
}

$(document).on('click', '.club-players-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#club-players-searchbar").val();
    var clubID = "";

    if($("#admin-change-club-members").length > 0)
    {
        var clubID = $("#admin-change-club-members").find(":selected").val();
    }

    if(page > 0)
    {
        retrieveClubPlayers(page, searchValue, clubID);
    }
});

$(document).on('click', '#account-search-players-button', function()
{
    var searchValue = $("#club-players-searchbar").val();
    var clubID = "";

    if($("#admin-change-club-members").length > 0)
    {
        var clubID = $("#admin-change-club-members").find(":selected").val();
    }
    else
    {
        clubID = $("#account-hidden-club-id").text();
    }

    retrieveClubPlayers(1, searchValue, clubID);
});

$("#club-players-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-players-button").click();
    }
});

$(document).on('change', '#admin-change-club-members', function()
{
    var searchValue = $("#club-players-searchbar").val();
    var clubID = "";

    if($("#admin-change-club-members").length > 0)
    {
        var clubID = $("#admin-change-club-members").find(":selected").val();
    }
    else
    {
        clubID = $("#account-hidden-club-id").text();
    }

    retrieveClubPlayers(1, searchValue, clubID);
});

$(document).on('click', '.account-edit-players-button', function()
{
    var playerID = $(this).closest('tr').find('.account-table-id').text();
    showEditPlayersModal(playerID);
});

$(document).on('click', '#edit-player-button', function()
{
    var playerID = $("#hidden-edit-player-id").val();
    var givenName = $("#edit-given-name").val();
    var familyName = $("#edit-family-name").val();
    var gender = $("#player-gender").find(":selected").val();
    var dob = $("#event-date").val();
    var email = $("#edit-player-email").val();
    var country = $("#edit-player-country").find(":selected").val();
    var state = $("#edit-player-state").find(":selected").val();

    editPlayer(playerID, givenName, familyName, gender, dob, email, country, state);
});

function editPlayer(playerID, givenName, familyName, gender, dob, email, country, state)
{
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "editPlayer", playerID: playerID, givenName: givenName, familyName: familyName, gender: gender, dob: dob, email: email, country: country, state: state},
        success: function(data)
        {
            hideEditPlayersModal();
            $("#account-search-players-button").click();
        }
    });
}

$(document).on('click', '#account-add-player-button', function()
{
    showCreatePlayerModal();
    uploadEventChangeStates($("#player-create-select-country"),$("#player-create-select-state"));
});

$(document).on('click', '#account-add-existing-player-button', function()
{
    showAddExistingPlayerModal();
    retrieveExistingPlayers(1, "");

});

function retrieveExistingPlayers(page, searchTerm)
{
    var existingPlayerID = 0;
    var clubID = "";

    if($("#admin-change-club-members").length > 0)
    {
        var clubID = $("#admin-change-club-members").find(":selected").val();
    }
    else
    {
        clubID = $("#account-hidden-club-id").text();
    }

    $.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, existingPlayerID: existingPlayerID, searchTerm: searchTerm, clubID: clubID},
        success: function(data)
        {
            $("#existing-player-table-modal-information").empty();
            $("#existing-player-table-modal-information").html(data);
        }
    });
}

$(document).on('click', '.existing-players-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#add-existing-player-searchbar").val();

    if(page > 0)
    {
        retrieveExistingPlayers(page, searchValue);
    }
});

$(document).on('click', '#account-search-add-existing-player-button', function()
{
    var searchValue = $("#add-existing-player-searchbar").val();
    retrieveExistingPlayers(1, searchValue);
});

$("#add-existing-player-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-add-existing-player-button").click();
    }
});

$(document).on('click', '.account-remove-players-button', function()
{
    var playerID = $(this).closest('tr').find('.account-table-id').text();
    var clubID = "";

    if($("#admin-change-club-members").length > 0)
    {
        var clubID = $("#admin-change-club-members").find(":selected").val();
    }
    else
    {
        clubID = $("#account-hidden-club-id").text();
    }

    $.ajax
    ({
        url: "./remove-player-from-club.php",
        type: "POST",
        data: {playerID: playerID, clubID: clubID},
        success: function(data)
        {
            $("#account-search-players-button").click();
        }
    });
});


$(document).on('click', '.add-existing-player-table-button', function()
{
    var playerID = $(this).closest('tr').find('.account-table-id').text();
    var clubID = "";

    if($("#admin-change-club-members").length > 0)
    {
        var clubID = $("#admin-change-club-members").find(":selected").val();
    }
    else
    {
        clubID = $("#account-hidden-club-id").text();
    }

    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "add-existing-player", playerID: playerID, clubID: clubID},
        success: function(data)
        {
            retrieveClubPlayers(1, "", clubID);
            retrieveExistingPlayers(1, "");
        }
    });
});


/* TOURNAMENT DIRECTORS */


function retrieveTournamentDirectors(page, searchTerm, clubID)
{
    var directorID = 0;

    $.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, directorID: directorID, searchTerm: searchTerm, clubID: clubID},
        success: function(data)
        {
            $("#account-directors-information").html(data);
        }
    });
}

$(document).on('click', '.tournament-directors-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#directors-searchbar").val();
    var clubID = "";

    if($("#admin-change-club").length > 0)
    {
        clubID = $("#admin-change-club").find(":selected").val();
    }

    if(page > 0)
    {
        retrieveTournamentDirectors(page, searchValue, clubID);
    }
});

$(document).on('click', '#account-search-directors-button', function()
{
    var searchValue = $("#directors-searchbar").val();
    var clubID = "";

    if($("#admin-change-club").length > 0)
    {
        clubID = $("#admin-change-club").find(":selected").val();
    }

    retrieveTournamentDirectors(1, searchValue, clubID);
});

$("#directors-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-directors-button").click();
    }
});

$(document).on('click', '.account-table-directors-button', function(event)
{
    var accountID = $(this).closest('tr').find('.account-table-id').text();
    removeDirectorFromClub(accountID);
});

function removeDirectorFromClub(accountID)
{
   $.ajax
    ({
        url: "./remove-director.php",
        type: "POST",
        data: {accountID: accountID},
        success: function(data)
        {
            $("#account-search-directors-button").click();
            document.location.reload();
        }
    });
}

$(document).on('change', '#admin-change-club', function()
{
    var searchValue = $("#directors-searchbar").val();
    var clubID = $("#admin-change-club").find(":selected").val();

    retrieveTournamentDirectors(1, searchValue, clubID);
    retrieveClubInformation(clubID);
});

function retrieveClubInformation(clubID)
{
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "retrieveClubInformation", clubID: clubID},
        success: function(data)
        {
            $("#account-club-details").empty().html(data);
        }
    });
}

$("#edit-player-country").change(function(){
    uploadEventChangeStates($("#edit-player-country"),$("#edit-player-state"));
});


/* ADMINISTRATORS */


function retrieveAdministrators(page, searchTerm)
{
    var administrationID = 0;

    $.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        data: {page: page, administrationID: administrationID, searchTerm: searchTerm},
        success: function(data)
        {
            $("#account-administrator-information").html(data);
        }
    });
}

$(document).on('click', '.administrators-link', function()
{
    var page = $(this).attr("id");
    var searchValue = $("#directors-searchbar").val();

    if(page > 0)
    {
        retrieveAdministrators(page, searchValue);
    }
});

$(document).on('click', '#account-search-administrators-button', function()
{
    var searchValue = $("#administrators-searchbar").val();
    retrieveAdministrators(1, searchValue);
});

$("#administrators-searchbar").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#account-search-administrators-button").click();
    }
});

$(document).on('click', '.account-table-administrators-button', function(event)
{
    var accountID = $(this).closest('tr').find('.account-table-id').text();
    demoteAdministrator(accountID);
});

function demoteAdministrator(accountID)
{
   $.ajax
    ({
        url: "./demote-administrator.php",
        type: "POST",
        data: {accountID: accountID},
        success: function(data)
        {
            document.location.reload();
        }
    });
}

$(document).on('click', '#account-add-administrator-button', function(event)
{
    showAdministratorModal();
    $("#account-search-promote-administrator-button").click();
});


/* SEARCH PLAYERS */


function retrieveSearchedPlayers(page, playerName, playerAgeMin, playerAgeMax, lastPlayed, clubName, countryName, stateName, submitSearchFilter)
{
    if(countryName === "Select Country")
    {
        countryName = "";
    }
    if(playerAgeMin == "" && playerAgeMax == "")
    {
        playerAgeMin = 0;
        playerAgeMax = 100;
    }

    $.ajax
    ({
        url: "./process-player-list.php",
        type: "POST",
        data: {page: page, playerName: playerName, playerAgeMin: playerAgeMin, playerAgeMax: playerAgeMax, lastPlayed: lastPlayed, clubName: clubName, countryName: countryName, stateName: stateName, submitSearchFilter: submitSearchFilter},
        success: function(data)
        {
            $(".player-search-result-container").html(data);
        }
    });
}

function showFavouritedPlayers()
{
  var favouritePost = 1;

  $.ajax
  ({
      url: "./process-player-list.php",
      type: "POST",
      data: {favouritePost: favouritePost},
      success: function(data)
      {
        var favouriteCheckbox = $("#toggle-favourite-checkbox");
        favouriteCheckbox.click(function(){
          if(this.checked)
          {
            var jsonData = JSON.parse(data);

            var newHTML = "";

            newHTML += "<table class='search-result-table'>";
          	newHTML += "<tr>";
          	newHTML += "<th>Player</th>";
          	newHTML += "<th>Age</th>";
          	newHTML += "<th>Last Played</th>";
          	newHTML += "<th>Club</th>";
            newHTML += "<th>Region</th>";
          	newHTML += "</tr>";

            $.each(jsonData, function(key, value)
            {
              var dateFormat = { day: 'numeric', month: 'long', year: 'numeric' };
              var lastPlayedDate = new Date(value.last_played);
              var lastPlayed = lastPlayedDate.toLocaleDateString("en-AU", dateFormat);

              var dateOfBirth = new Date(value.date_of_birth);
              var currentDate = new Date();
              var playerAge = Math.floor((currentDate - dateOfBirth) / (365.25 * 24 * 60 * 60 * 1000));

              newHTML += "<tr>";
              newHTML += "<td><a id='player-name-link' href='profile.php?profile-id=" + value.player_id + "'>" + value.family_name + " " + value.given_name + "</a></td>";
              newHTML += "<td>" + playerAge + "</td>";
              newHTML += "<td>" + lastPlayed + "</td>";
              newHTML += "<td>" + value.club_name + "</td>";
              newHTML += "<td>" + value.country_name + ", " + value.state_name + "</td>";
              newHTML += "</tr>";
            });

            $(".player-search-result-container").html(newHTML);
          }
          else
          {
            $("#submit-search-filter").click();
          }
        });
      }
  });
}

$(document).on('click', '.player-search-link', function()
{
    var page = $(this).attr("id");
    var playerName = $("#player-name-filter").val();
    var lastPlayed = $("#player-recent-match-filter").val();
    var countryName = $("#player-country-filter>option:selected").text();
    var clubName = $("#player-club-filter").val();
    var stateName = $("#player-state-filter").val();
    var submitSearchFilter = $("#submit-search-filter").val();
    var playerAgeMin = 0;
    var playerAgeMax = 120;

    if(Number.isInteger($("#player-age-min-filter").val()))
    {
        playerAgeMin = $("#player-age-min-filter").val();
    }

    if(Number.isInteger($("#player-age-max-filter").val()))
    {
        playerAgeMax = $("#player-age-max-filter").val();
    }

    if(page > 0)
    {
        retrieveSearchedPlayers(page, playerName, playerAgeMin, playerAgeMax, lastPlayed, clubName, countryName, stateName, submitSearchFilter);
    }
});

$(document).on('click', '#submit-search-filter', function()
{
    var playerName = $("#player-name-filter").val();
    var lastPlayed = $("#player-recent-match-filter").val();
    var countryName = $("#player-country-filter>option:selected").text();
    var clubName = $("#player-club-filter").val();
    var stateName = $("#player-state-filter").val();
    var submitSearchFilter = $("#submit-search-filter").val();
    var playerAgeMin = 0;
    var playerAgeMax = 120;

    if(!isNaN($("#player-age-min-filter").val()))
    {
        playerAgeMin = $("#player-age-min-filter").val();
    }

    if(!isNaN($("#player-age-max-filter").val()))
    {
        playerAgeMax = $("#player-age-max-filter").val();
    }

    retrieveSearchedPlayers(1, playerName, playerAgeMin, playerAgeMax, lastPlayed, clubName, countryName, stateName, submitSearchFilter);
});

//on load of player search page check to see if a search needs to be conducted
//based on post variable from home page.
$( function() {
    if (typeof $_POST["home-player-search"] !== 'undefined') {
        retrieveSearchedPlayers(1,$_POST["home-player-search"],'','','','','','','');
        $("#player-name-filter").val($_POST["home-player-search"]);
    }
});

$(".player-search-filter-container").keyup(function(event)
{
    if (event.keyCode === 13)
    {
        $("#submit-search-filter").click();
    }
});


/* State Changes */


$("#create-club-select-country").on('change', function(){
    uploadEventChangeStates($("#create-club-select-country"),$("#create-club-select-state"));
});

$(document).on('change', '#edit-player-country', function(){
	uploadEventChangeStates($("#edit-player-country"),$("#edit-player-state"));
});

$(document).on('change', '#player-create-select-country', function(){
    uploadEventChangeStates($("#player-create-select-country"),$("#player-create-select-state"));
});


/* Setup Administration Selected Clubs */

function setupInitialClubInfo()
{
    if($("#admin-change-club").length > 0)
    {
        $("#admin-change-club").val(1).change();
        $("#admin-change-club-members").val(1).change();
        $("#admin-change-club-events").val(1).change();
    }

}

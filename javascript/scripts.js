var slideIndex = 0;

window.onload = function()
{
    retrieveRecentEventsForClub(1);
    document.getElementById("input-confirm-password").onchange = passwordMatches;
    document.getElementById("input-email").onchange = isEmailTaken;
    document.getElementById("player-tab").click();
    rotateSlideshow();
    document.getElementById("reset-input-confirm-password").onchange = resetPasswordMatches;
}

function retrieveRecentEventsForClub(page)
{
    var eventID = eventID;

    $.ajax
    ({
        url: "./account-pagination.php",
        type: "POST",
        dataType: "text",
        data: {page: page, eventID: eventID},
        success: function(data)
        {
            $("recent-events-table").html(data);
        }
    });
}

function passwordMatches()
{
    var password = document.getElementById("input-password").value;
    var confirmPassword = document.getElementById("input-confirm-password").value;

    if(password && confirmPassword != null)
    {
    	if(confirmPassword != password)
    	{
        	document.getElementById("input-confirm-password").setCustomValidity("Passwords do not match");
    	}
   		else
    	{
    		document.getElementById("input-confirm-password").setCustomValidity("");
    	}
    }
}

function resetPasswordMatches()
{
    var password = document.getElementById("reset-input-password").value;
    var confirmPassword = document.getElementById("reset-input-confirm-password").value;

    if(password && confirmPassword != null)
    {
        if(confirmPassword != password)
        {
            document.getElementById("reset-input-confirm-password").setCustomValidity("Passwords do not match");
        }
        else
        {
            document.getElementById("reset-input-confirm-password").setCustomValidity("");
        }
    }
}

function isEmailTaken()
{
    var email = $("#input-email").val();

	$.ajax
	({
		url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data: {email: email, ajaxMethod: "is-email-taken"},
        success: function(data)
        {
            if(data == "true")
            {
            	document.getElementById("input-email").setCustomValidity("An account with this email already exists");
            }
            else
            {
                document.getElementById("input-email").setCustomValidity("");
            }
        }
    });
}


function showRegisterModal()
{
	document.querySelector(".register-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hideRegisterModal()
{
	document.querySelector(".register-modal-background").style.display = "none";
}

function showCreatePlayerModal()
{
    document.querySelector(".create-player-modal-background").style.display = "flex";

    if($("#admin-change-club").length > 0)
    {
        $('input[name="hidden-club-ID"]').val($("#admin-change-club-members").find(":selected").val());
    }
    else
    {
        $('input[name="hidden-club-ID"]').val($("#account-hidden-club-id").text());
    }

    hideDropdownMenu();
}

function showEditAccountModal()
{
    document.querySelector(".edit-account-modal-background").style.display = "flex";
    hideDropdownMenu();

    var givenName = $("#account-given-name-value").text();
    var familyName = $("#account-family-name-value").text();
    var email = $("#account-email-value").text();

    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "editAccountModal", givenName: givenName, familyName: familyName, email: email},
        success: function(data)
        {
            $(".edit-account-modal-field-wrapper").html(data);
        }
    });
}

function hideEditAccountModal()
{
    document.querySelector(".edit-account-modal-background").style.display = "none";
}

function hideCreatePlayerModal()
{
    document.querySelector(".create-player-modal-background").style.display = "none";
}

function showPasswordModal()
{
    document.querySelector(".password-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hidePasswordModal()
{
    document.querySelector(".password-modal-background").style.display = "none";
}

function showResetModal()
{
    document.querySelector(".reset-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hideResetModal()
{
    document.querySelector(".reset-modal-background").style.display = "none";
}

function showAdministratorModal()
{
    document.querySelector(".administrator-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hideAdministratorModal()
{
    document.querySelector(".administrator-modal-background").style.display = "none";
}

function showAddExistingPlayerModal()
{
    document.querySelector(".existing-player-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hideAddExistingPlayerModal()
{
    document.querySelector(".existing-player-modal-background").style.display = "none";
}

function showCreateClubModal()
{
    document.querySelector(".create-club-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hideCreateClubModal()
{
    document.querySelector(".create-club-modal-background").style.display = "none";
}

function showDirectorModal()
{
    document.querySelector(".director-modal-background").style.display = "flex";
    hideDropdownMenu();
}

function hideDirectorModal()
{
    document.querySelector(".director-modal-background").style.display = "none";
}

function showEditPlayersModal(playerID)
{
    document.querySelector(".edit-player-modal-background").style.display = "flex";
    hideDropdownMenu();
    $("#hidden-edit-player-id").val(playerID);

    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        data: {ajaxMethod: "editPlayerModal", playerID: playerID},
        success: function(data)
        {
            $(".edit-player-modal-field-wrapper").html(data);
            uploadEventChangeStates($("#edit-player-country"),$("#edit-player-state"));
        }
    });
}

function hideEditPlayersModal()
{
    document.querySelector(".edit-player-modal-background").style.display = "none";
}

function showDropdownMenu()
{
    document.querySelector(".dropdown-menu").style.display = "inline-block";
    document.querySelector(".nav-sign-in-button").style.backgroundColor = "var(--secondary-color)";
}

function hideDropdownMenu()
{
    document.querySelector(".dropdown-menu").style.display = "none";
    document.querySelector(".nav-sign-in-button").style.backgroundColor = "var(--primary-color)";
}

function showNotificationModal(header, message)
{
    document.querySelector("#notification-header-text").innerHTML=header;
    document.querySelector("#notification-modal-text").innerHTML=message;

    document.querySelector(".notification-modal-background").style.display = "flex";
}

function hideNotificationModal()
{
    document.querySelector(".notification-modal-background").style.display = "none";
}

function toggleDropdownMenu()
{
    if($(".dropdown-menu").css("display") === "none")
    {
        showDropdownMenu();
    }
    else
    {
        hideDropdownMenu();
    }
}

function rotateSlideshow()
{
    var slideshow = document.getElementsByClassName("slideshow-image");

    for(var currentSlide = 0; currentSlide < slideshow.length; currentSlide++)
    {
        slideshow[currentSlide].style.opacity = "0.0";
    }

    slideIndex++;

    if(slideIndex > slideshow.length)
    {
        slideIndex = 1;
    }

    slideshow[slideIndex - 1].style.opacity = "1.0";

    setTimeout(rotateSlideshow, 6500);
}

function switchTab(tab, content)
{
    var tabSelections = document.getElementsByClassName("tab-selection");
    var tabContent = document.getElementsByClassName("tab-content");

    for (var currentTab = 0; currentTab < tabContent.length; currentTab++)
    {
      tabContent[currentTab].style.display = "none";
    }

    for (currentTab = 0; currentTab < tabSelections.length; currentTab++)
    {
      tabSelections[currentTab].style.backgroundColor = "";
    }

    selectedContent = document.getElementById(content);
    selectedContent.style.display = "block";
}

function resetPassword()
{
    var emailSentText = document.getElementById("email-sent");
    var emailField = $("#password-input-email").val();

    if(emailField != "")
    {
        emailSentText.style.visibility = "visible";

        $.ajax
        ({
            url: "./forgotPassword.php",
            type: "POST",
            dataType: "text",
            data: { resetPassword: emailField },
            success: function(data)
            { }
        });
    }
}


/**
 * -------------------------------------------------------------*
 * 		Begin Match Upload Section								*
 * 																*
 * -------------------------------------------------------------*
 */

function showUploadMatchRows() {

    //check sport and event type selected
    if ($("#sport-type").val() == null) {
        //no sport selected
        showNotificationModal('Error', 'Please select a sport before clicking.');
    } else {
        if ($("#event-type").val() == null) {
            //no event type selected
            showNotificationModal('Error','Please select the match type (Singles or Doubles) before clicking.');
        } else {

            var matchInputNumber = document.getElementById("match-field-input").value;

            if (matchInputNumber == "") {
                showNotificationModal('Error',"Please type a number (greater than 1) before clicking");
            } else {

                if (matchInputNumber < 1 || matchInputNumber > 100) {
                    showNotificationModal('Error',"Match input number cannot be less than 1 or more than 100. If your event has more than 100 matches split it across multiple uploads.");
                } else {

                    var matchRows = document.getElementById("match-field-input").value;

                    var table = document.getElementById("match-input-table");

                    if (table.rows.length !== 0) {


                                for (var deleteCycle = table.rows.length - 1; deleteCycle >= 0; deleteCycle--) {
                                    table.deleteRow(deleteCycle);
                                }

                    }

                    var a = document.getElementById("event-type").value;
                    var dbl;
                    if (a == 'Double') {
                        x = 1;
                        dbl = 1;
                    } else {
                        x = 0;
                    }
                    for (var insertCycle = 0; insertCycle < matchRows; insertCycle++) {
                        addEventRow(dbl);

                    }

                    var num = document.getElementById("match-field-input").value;
                    sessionStorage.setItem("lastnumber", num);
                    setupMatchAutoComplete();
                    setupMatchErrorChecking();
                    setupAdvancedSearchLinks();

                    // display submit match button

                    document.getElementById('submit_event').style.display = 'block';

                    /*var addButton = document.createElement("BUTTON");
                      addButton.innerHTML = "Add More Rows";
                       addButton.setAttribute('class','add-button');
                         document.body.appendChild(addButton);*/


                    if (matchInputNumber != 0) {
                        document.getElementById("match-final-submit").style.display = "block";
                    }
                }
            }
        }
    }
    return false;
}

function addEventRow(dbl) {
    var table = document.getElementById("match-input-table");


    var row = table.insertRow(0);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);


    var newlabel0 = document.createElement("Label");
    newlabel0.setAttribute('class', 'match-detail-heading break');
    newlabel0.innerHTML = "<b> Match details</b>";
    cell1.appendChild(newlabel0);


    var insertCell1 = document.createElement("input");
    insertCell1.setAttribute('type', 'text');
    insertCell1.setAttribute('class', 'match-field-input winner-loser-field winner-field break');
    insertCell1.setAttribute('name', 'winner-name[]');
    insertCell1.onkeyup = "checkForm()";
    insertCell1.placeholder = "Winning Player";
    cell1.appendChild(insertCell1);

    //adds a hidden cell to contain ids of winners
    var hiddenInput1 = document.createElement("input");
    hiddenInput1.setAttribute('type', 'hidden');
    hiddenInput1.setAttribute('class', 'winner-id-field');
    hiddenInput1.setAttribute('name', 'winner-id[]');
    cell1.appendChild(hiddenInput1);

     var insertCell2 = document.createElement("input");
                        insertCell2.setAttribute('type', 'number');
                        insertCell2.setAttribute('class', 'winner-set-score');
                        insertCell2.setAttribute('name', 'winner-set-score[]');
                        insertCell2.setAttribute('required', 'true');
                        insertCell2.onkeyup = "checkForm()";
                        insertCell2.placeholder = "Set Score";
                        cell2.appendChild(insertCell2);

    var newlabel = document.createElement("label");
    newlabel.setAttribute('class', 'ad-search break');
    cell1.appendChild(newlabel);
    var advancedSearchLink = document.createElement("a");
    advancedSearchLink.setAttribute('class', 'ad-search-link');
    advancedSearchLink.innerHTML = "Advanced Search";
    advancedSearchLink.setAttribute('href','#');
    newlabel.appendChild(advancedSearchLink);

    if (dbl == '1') {

        var insertCell11 = document.createElement("input");
        insertCell11.setAttribute('type', 'text');
        insertCell11.setAttribute('class', 'match-field-input winner-loser-field winner-field break');
        insertCell11.setAttribute('name', 'winner-name[]');
        insertCell11.onkeyup = "checkForm()";
        insertCell11.placeholder = "Winning Player";
        cell1.appendChild(insertCell11);

        //adds a hidden cell to contain ids of winners
        var hiddenInput1 = document.createElement("input");
        hiddenInput1.setAttribute('type', 'hidden');
        hiddenInput1.setAttribute('class', 'winner-id-field');
        hiddenInput1.setAttribute('name', 'winner-id[]');
        cell1.appendChild(hiddenInput1);

        var newlabel = document.createElement("label");
        newlabel.setAttribute('class', 'ad-search break');
        cell1.appendChild(newlabel);
        var advancedSearchLink = document.createElement("a");
        advancedSearchLink.setAttribute('class', 'ad-search-link');
        advancedSearchLink.innerHTML = "Advanced Search";
        advancedSearchLink.setAttribute('href','#');
        newlabel.appendChild(advancedSearchLink);
    }

    var newlabel2 = document.createElement("Label");
    newlabel2.setAttribute('class', 'break add-player-link add-match');
    //newlabel2.setAttribute('onclick', 'showAddPlayerModal()');
    var str3 = "Can't find a player? Add them ";
    var str2 = "here";
    var result2 = str2.link("#");
    newlabel2.innerHTML = str3;// + result2;
    cell1.appendChild(newlabel2);
    var newPlayerLink = document.createElement("a");
    newPlayerLink.setAttribute('href','#');
    newPlayerLink.setAttribute('onclick', 'showAddPlayerModal()');
    newPlayerLink.innerHTML = "here";
    newlabel2.appendChild(newPlayerLink);



    /* var insertCell2 = document.createElement("button");
    insertCell2.innerHTML = "Search";
    insertCell2.setAttribute('class', 'search-button');
    cell2.appendChild(insertCell2); */

    var insertCell3 = document.createElement("input");
    insertCell3.setAttribute('type', 'text');
    insertCell3.setAttribute('class', 'match-field-input winner-loser-field loser-field break');
    insertCell3.setAttribute('name', 'loser-name[]');
    insertCell3.placeholder = "Losing Player";
    insertCell3.onkeyup = "checkForm()";
    cell3.appendChild(insertCell3);

    //adds a hidden cell to contain ids of losers
    var hiddenInput2 = document.createElement("input");
    hiddenInput2.setAttribute('type', 'hidden');
    hiddenInput2.setAttribute('class', 'loser-id-field');
    hiddenInput2.setAttribute('name', 'loser-id[]');
    cell3.appendChild(hiddenInput2);

    var newlabel = document.createElement("label");
    newlabel.setAttribute('class', 'ad-search break');
    cell3.appendChild(newlabel);
    var advancedSearchLink = document.createElement("a");
    advancedSearchLink.setAttribute('class', 'ad-search-link');
    advancedSearchLink.innerHTML = "Advanced Search";
    advancedSearchLink.setAttribute('href','#');
    newlabel.appendChild(advancedSearchLink);

    if (dbl == '1') {

        var insertCell3 = document.createElement("input");
        insertCell3.setAttribute('type', 'text');
        insertCell3.setAttribute('class', 'match-field-input winner-loser-field loser-field break');
        insertCell3.setAttribute('name', 'loser-name[]');
        insertCell3.placeholder = "Losing Player";
        insertCell3.onkeyup = "checkForm()";
        cell3.appendChild(insertCell3);

        //adds a hidden cell to contain ids of losers
        var hiddenInput2 = document.createElement("input");
        hiddenInput2.setAttribute('type', 'hidden');
        hiddenInput2.setAttribute('class', 'loser-id-field');
        hiddenInput2.setAttribute('name', 'loser-id[]');
        cell3.appendChild(hiddenInput2);

        var newlabel = document.createElement("label");
        newlabel.setAttribute('class', 'ad-search break');
        cell3.appendChild(newlabel);
        var advancedSearchLink = document.createElement("a");
        advancedSearchLink.setAttribute('class', 'ad-search-link');
        advancedSearchLink.innerHTML = "Advanced Search";
        advancedSearchLink.setAttribute('href','#');
        newlabel.appendChild(advancedSearchLink);
    }

    var insertCell4 = document.createElement("input");
                        insertCell4.setAttribute('type', 'number');
                        insertCell4.setAttribute('class', 'loser-set-score');
                        insertCell4.setAttribute('name', 'loser-set-score[]');
                        insertCell4.setAttribute('required', 'true');
                        insertCell4.onkeyup = "checkForm()";
                        insertCell4.placeholder = "Set Score";
                        cell4.appendChild(insertCell4);
    var insertCell5 = document.createElement("button");
    insertCell5.innerHTML = "Delete";
    insertCell5.setAttribute('class', 'delete-button');
    insertCell5.setAttribute('type', 'button');

    insertCell5.onclick = function() {
        deleteRow(this);
    };
    cell5.appendChild(insertCell5);

}

function deleteRow(selectedRow) {
    var findRow = selectedRow.parentNode.parentNode.rowIndex;
    document.getElementById("match-input-table").deleteRow(findRow);

}

function addMoreRows() {


    var a = document.getElementById("event-type").value;
    var dbl;
    if (a == 'Double') {

        dbl = 1;
    }

    addEventRow(dbl);

    setupMatchAutoComplete();
    setupMatchErrorChecking();
    setupAdvancedSearchLinks();


}
// show add player modal

function showAddPlayerModal() {
    document.querySelector(".add-player-border").style.display = "flex";

}

function hideAddPlayerModal() {
    document.querySelector(".add-player-border").style.display = "none";
}


function addPlayer() {
    //$('#add-player-button').click(function (){
    var playerGivenName = $("#player-given-name").val();
    var playerFamilyName = $("#player-family-name").val();
    var playerGenderID = $("#player-gender-ID").val();
    var playerBirthDate = $("#player-birth-date").val();
    var playerEmail = $("#player-email").val();
    var playerClubID = $("#player-club-ID").val();

    $.ajax({
        url: "./ajax.php",
        type: 'post',
        datatype: "text",
        data: {
            playerGivenName: playerGivenName,
            playerFamilyName: playerFamilyName,
            playerGenderID: playerGenderID,
            playerBirthDate: playerBirthDate,
            playerEmail: playerEmail,
            playerClubID: playerClubID,
            ajaxMethod: "add-player-manager"
        },
        success: function(data) {
            hideAddPlayerModal();

        }

    });

    //});
}

//variables used for advanced search
var advancedSeachPlayerNameInput; //will store the input of the name field
var advancedSeachPlayerIDInput; //will store the input of the (hidden) id field


function setupAdvancedSearchLinks() {
    $('.ad-search-link').click(function() {
        advancedSeachPlayerIDInput = $(this).parent().prev();
        advancedSeachPlayerNameInput = $(this).parent().prev().prev();
        showAdvancedSearchModal();
    });
}

//show advance search player modal-background

function showAdvancedSearchModal() {
    //clear any previous entry
    $("#input-player-name").val("");


    document.querySelector(".player-advanced-search-border").style.display = "flex";

    setupMatchAutoCompleteAdvancedSearch();
    setupMatchErrorCheckingAdvancedSearch();
}

function hideAdvancedSearchModal() {
    document.querySelector(".player-advanced-search-border").style.display = "none";
}

function setEditEvent(eventID)
{
	//run ajax
    $.ajax({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data: { eventID: eventID, ajaxMethod: "get-event-info"},
        success: function(data) {
            //parse the returned data
            var jsonData = JSON.parse(data);
            $('#event-name').val(jsonData.name);
            $('#event-date').val(jsonData.date);
            $('#event-type').val(jsonData.type);
            $('#match-field-input').val(jsonData.number_matches);
            $('#country-id').val(jsonData.country_id);
            $('#home-state').val(jsonData.state_id);
            showUploadMatchRows();
            uploadEventChangeStates($("#country-id"), $("#state-name"));

            if (jsonData.type == "Single")
            {
				var single = 1;
			}
			else
			{
				var single = 0;
			}

			$.ajax({
				url: "./ajax.php",
				type: "POST",
				dataType: "text",
				data: { eventID: eventID, singles: single, ajaxMethod: "get-event-matches"},
				success: function(data) {
					//parse the returned data
					var jsonData = JSON.parse(data);

					$.each(jsonData, function(index, value) {
						if (single == 1)
						{
							$('.winner-field').eq(index).val(value.winning_name);
							$('.winner-id-field').eq(index).val(value.winning_id);
							$('.loser-field').eq(index).val(value.losing_name);
							$('.loser-id-field').eq(index).val(value.losing_id);

							$('.winner-set-score').eq(index).val(value.winner_score);
							$('.loser-set-score').eq(index).val(value.loser_score);
						}
						else
						{
							//doubles
							var field1 = index * 2;
							var field2 = (index * 2) + 1;

							$('.winner-field').eq(field1).val(value.winning_name1);
							$('.winner-id-field').eq(field1).val(value.winning_id1);
							$('.loser-field').eq(field1).val(value.losing_name1);
							$('.loser-id-field').eq(field1).val(value.losing_id1);

							$('.winner-field').eq(field2).val(value.winning_name2);
							$('.winner-id-field').eq(field2).val(value.winning_id2);
							$('.loser-field').eq(field2).val(value.losing_name2);
							$('.loser-id-field').eq(field2).val(value.losing_id2);

							$('.winner-set-score').eq(index).val(value.winner_score);
							$('.loser-set-score').eq(index).val(value.loser_score);
						}
					});
				}
			});
        }
    });

}

/**
 * on page load funcion.
 * A number of items need to be setup on page load.
 * They are described in line.
 */
$(function() {
    $("#match-number-submit").click(function() { showUploadMatchRows(); });

    uploadEventChangeStates($("#country-id"), $("#state-name")); //gets states based on country
    setupMatchAutoComplete(); //gets players based on state

    //set the max event date to today
    let now = new Date();
    var nowString = now.toISOString().substring(0, 10);
    $("#event-date").attr({
        "max": nowString
    });

    if ( $('#edit-event-id').val() > 0 )
    {
		setEditEvent($('#edit-event-id').val());
		setupMatchAutoComplete(); //gets players based on state
	}
});

/**
 * ajax query for event upload page to fill in state box based upon user
 * selection from country box.
 *
 * Relies on getStatesByCountry.php for data.
 *
 * Triggers by change in country-id and on page load
 */


//event listener for change of country
$("#country-id").change(function() {
    uploadEventChangeStates($("#country-id"), $("#state-name"));
});

function uploadEventChangeStates(countryCombo, stateCombo) {
    var country = countryCombo.val();

    //clear the options
    stateCombo.empty();

    //run ajax
    $.ajax({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data: { countryID: country, ajaxMethod: "get-states-by-country-ID"},
        success: function(data) {
            //parse the returned data
            var jsonData = JSON.parse(data);
            //add a new option to state-name for each returned state.
            $.each(jsonData, function(index, value) {
                if (value["state_id"] == $('#home-state').val())
                {
                    stateCombo.append($("<option>", {
                        value: value["state_id"],
                        text: value["name"],
                        selected: true
                        }));
                }
                else
                {
                    stateCombo.append($("<option>", {
                        value: value["state_id"],
                        text: value["name"]}));
                }


            });
        }
    });
}


$("#player-country-filter").change(function() {
    playerSearchChangeStates($(this), $("#player-state-filter"));
});

function playerSearchChangeStates(countryName, stateName) {
    var country = countryName.val();

    stateName.html("<option selected value>Select State</option>");

    $.ajax({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data: { countryID: country, ajaxMethod: "get-states-by-country-ID" },
        success: function(data) {
            //parse the returned data
            var jsonData = JSON.parse(data);

            //add a new option to state-name for each returned state.
            $.each(jsonData, function(index, value) {
                stateName.append($("<option>", {
                    text: value["name"]
                }));
            });
        }
    });
}

/**
 *
 * sets up auto complete for winner/loser boxes. Gets a list of players
 * based upon 'state' selected'.
 *
 * Triggered by change in state-name, on page load and when number of
 * matches changes.
 */
$("#state-name").change(setupMatchAutoComplete);

function setupMatchAutoComplete() {
    var state = $("#state-name").val(); //note that this will need to change to state not country

    $(".winner-loser-field").autocomplete({
        source: function(request, response) {
            // Fetch data
            $.ajax({
                url: "./ajax.php",
                type: 'POST',
                dataType: "json",
                data: {
                    name: request.term,
                    state: state,
                    ajaxMethod: "get-player-by-state"
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            //the next elemtent in line will be the hidden cell to contain id
            //fill this with the id.
            //name cell will be automatically filled in

            $(this).next().val(ui.item.id);

            //when an item is selected it is assumed that no error exists, remove the error class
            $(this).removeClass("upload-page-error-on-submit");
        }
    });
}

function setInitialRating(playerID) {
    if (playerID != "") {
        var setRating = 1;
        var sportID = $("#sport-type").val();

        $.ajax({
            url: "./ajax.php",
            type: 'POST',
            datatype: "text",
            data: {
                playerID: playerID,
                sportID: sportID,
                setRating: setRating,
                ajaxMethod: "initial-rating-Manager"
            },
            success: function(data) {
                if (!parseInt(data)) {
					//player as no rating for this sport set.
                    showInitialRatingModal(playerID, sportID);
                }
            }

        });
    }
}


/**
 * Sets hidden id field for winners/losers to "" on a user key press,
 * removes any validitiy set when a change is made to winner/losers.
 *
 * This function needs to be executed every time there is a change in the
 * number of matches.
 */
function setupMatchErrorChecking() {
    $(".winner-loser-field").keyup(function(e) {
        //user has used keyboard to change winner/loser field
        //The winner/loser hidden field needs to be made blank
        $(this).next().val("");
    });

    $(".winner-loser-field").change(function(e) {
        var playerID = $(this).next().val();
        setInitialRating(playerID);

        $(".winner-loser-field").each(function() {
            this.setCustomValidity('');
        });
    });
}

function favouriteButtonAnimation() {
    $("#favourite-icon").click(function(e) {
        if ($(this).attr('src') === "resources/images/favourite-icon-24.png") {
            $(this).attr('src', 'resources/images/favourite-icon-filled-24.png');
        } else {
            $(this).attr('src', 'resources/images/favourite-icon-24.png');
        }
    });

    $("#favourite-icon").on("mouseenter" , function(e) {
            $(this).animate( { width: "28px", height: "28px" }, 200 );
    }).on("mouseleave", function(e) {
            $(this).animate( { width: "24px", height: "24px" }, 200 );
    });
}

/**
 * Form validity checking.
 *
 * Most validity checking is done with HTML5. However, we also need to
 * check validity of winners/losers. This is done by making use of the
 * above funciton setupMatchErrorChecking, then checks to ensure a player
 * has been selected rather than just typed in and that winner != loser.
 *
 * If there is an error the submit of form is stopped and a HTML5 validity
 * error message is shown to the user.
 */
$("#event-upload-form").submit(function() {

    var rtn = true;

    //first check the date is not in the future


    var winnerID;

    $(".winner-loser-field").each(function() {

        if ($(this).is(".winner-field")) {
            //save the winner field for comparrison when we get to loser field
            winnerID = $(this).next().val();
        }

        //check if id is set, if id is not set then user has not selected a player and has just entered the information by hand, possibly causing errors.
        if ($(this).next().val() == "") {
            //val not set
            this.setCustomValidity('You must select the player from the list.');
            if (rtn == true) {
                //first error reported so show the error
                this.reportValidity();
            }
            rtn = false;
        } else {
            if (!($(this).is(".winner-field"))) {
                //check if winner id = loser id
                if (winnerID == $(this).next().val()) {
                    this.setCustomValidity('Winner and loser can not be the same player');
                    if (rtn == true) {
                        //first error reported so show the error.
                        this.reportValidity();
                    }
                    rtn = false;
                } else {
                    //no error for this item
                    this.setCustomValidity('');
                }
            } else {
                //no error for this item
                this.setCustomValidity('');
            }
        }



    });

    return rtn;

});


var x = 0;

function changeValue() {
    var matchRows = document.getElementById("match-field-input").value;

    var table = document.getElementById("match-input-table");

    if (table.rows.length !== 0) {

        modalSelection();

    } else {
        return;
    }

}

function modalSelection(){
    var ab = document.getElementById("event-type").value;

                    if (ab == 'Double') {
                        x = 1;

                    } else {
                        x = 0;
                    }

    if (x == 1) {
        document.getElementById("event-type-notification-modal-text").innerHTML = "Are you sure you wish to change match type? You will lose any un-submitted singles events on this page.";
        document.querySelector(".event-type-notification-modal-background").style.display = "flex";
    }
    else {
        document.getElementById("event-type-notification-modal-text").innerHTML = "Are you sure you wish to change match type? You will lose any un-submitted doubles event on this page.";
        document.querySelector(".event-type-notification-modal-background").style.display = "flex";

}
}

function changeType(){

    showUploadMatchRows();
    document.querySelector(".event-type-notification-modal-background").style.display = "none";
}
function hideTypeModal() {
    document.querySelector(".event-type-notification-modal-background").style.display = "none";
     var ind = document.getElementById("event-type").selectedIndex
     if(ind == "1"){
         document.getElementById("event-type").selectedIndex = "2";
     }
     else if(ind == "2"){
         document.getElementById("event-type").selectedIndex = "1";
     }

}

/**
 *----------------------------
 * Harinder work of change Number of matches event
 *------------------------------
 */



function changeMatchNumber() {
    var matchRows = document.getElementById("match-field-input").value;

    var table = document.getElementById("match-input-table");

    if (table.rows.length !== 0) {

            var val = document.getElementById("match-field-input").value;
             if(val == ""){
                 return;
             }
             else {
          modalChangeNumber();
             }

    }
    else {
        return;
    }

}


function modalChangeNumber(){

        document.getElementById("change-match-number-notification-modal-text").innerHTML = "Are you sure you wish to change match Number? You will lose any un-submitted events on this page.";
        document.querySelector(".change-match-number-notification-modal-background").style.display = "flex";

}

function changeNumber(){

    showUploadMatchRows();
    document.querySelector(".change-match-number-notification-modal-background").style.display = "none";
}

function hideNumberModal() {
    document.querySelector(".change-match-number-notification-modal-background").style.display = "none";
        document.getElementById("match-field-input").value = sessionStorage.getItem("lastnumber");
}

/*for popover*/

$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
});

/**
 * ---------------------------------------------- *
 *  Begin bookmark section                        *
 * ---------------------------------------------- *
 */

 function createBookmark()
 {
    //initial values
	 var getVariableName = 'profile-id';
	 var cookieName = 'bookmarked_players';

     //get the player id from url
	 var params = (new URL(document.location)).searchParams;
	 var playerID = params.get(getVariableName);

	 var bookmarked;

     bookmarked = Cookies.getJSON(cookieName);

	 if (! (bookmarked) )
	 {
		 //cookie does not already exist so the list will be empty
		bookmarked = [];
	 }
    if ( (bookmarked.indexOf(playerID)) == -1 )
    {
       //player not in bookmark list so add it
       bookmarked.push(playerID);
    }
    else
    {
       //player in the list remove them
       bookmarked.splice(bookmarked.indexOf(playerID),1);
    }

    //now bookmarked has been updated lets save it to the cookie.
    Cookies.set(cookieName, bookmarked, {expires: 1825});
 }

//listener for when bookmark button pressed
$(".favourite-label").click(createBookmark);

$(document).ready(function(){
   favouriteButtonAnimation();
});

 /**
  *------------------------------------------------*
  *Begin profile section
  *
  *------------------------------------------------*
  */

//global values required
var getVariableName = 'profile-id';
var eventHistoryRowCount = 0;

function updateProfileSport()
{
     //get the player id from url
	 var params = (new URL(document.location)).searchParams;
	 var playerID = params.get(getVariableName);

     //get sport ID
     newSportID = $("#profile-select-sport").val();
     newSportName = $("#profile-select-sport option:selected").text();

     $(".profile-sport-name").html(newSportName);
     $(".mean-value").html("Loading");
     $(".sd-value").html("Loading");

     //run ajax to update sd and mean
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data:
        {
            playerID: playerID,
            sportID: newSportID,
            ajaxMethod: "get-player-rating"
        },
        success: function(data)
        {

            //parse the returned data
            var jsonData = JSON.parse(data);

            $(".mean-value").html(parseInt(jsonData.mean));
            $(".sd-value").html("&plusmn; " + parseInt(jsonData.sd));
        }
    });

    addEventHistory(true);
}

function isEmpty(obj) {
    for(var key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }
    return true;
}

function updatePlayerTeams()
{
  var playerTeamsRowCount = 0;

  var params = (new URL(document.location)).searchParams;
  var playerID = params.get(getVariableName);
  var sportID = $("#profile-select-sport").val();

  $.ajax
  ({
      url: "./ajax.php",
      type: "POST",
      dataType: "text",
      data:
      {
          playerID: playerID,
          sportID: sportID,
          ajaxMethod: "update-player-teams"
      },
      success: function(data)
      {
          var jsonData = JSON.parse(data);

          var newHTML = "";

          $.each(jsonData, function(key, value)
          {
            if ((playerTeamsRowCount % 2) == 0)
            {
                // 'even' row
                newHTML = newHTML + "<tr class='even-row'>";
            }
            else
            {
                newHTML = newHTML + "<tr class='odd-row'>";
            }

            newHTML = newHTML + "<td><a href='team-profile.php?team-id=" + value.teamID + "'>" + value.player1 + ", &nbsp" + value.player2 + "</a></td>";

            playerTeamsRowCount++;
          });

          $("#team-table-link").html(newHTML);
      }
  });
}

 //listener for change of sport on profile page
 $("#profile-select-sport").change(updateProfileSport);

 //display teams on page load for default selected sport
 $(function(){
    $("#profile-select-sport").ready(updatePlayerTeams);
 });

 //listener for change of teams on profile page
 $("#profile-select-sport").change(updatePlayerTeams);

 function addEventHistory(changeSport)
 {
    //get the player id from url
	 var params = (new URL(document.location)).searchParams;
	 var playerID = params.get(getVariableName);

     //get sport ID
     sportID = $("#profile-select-sport").val();

    if (changeSport)
    {
        //set count to zero and reset the table
        eventHistoryRowCount = 0;
        $("#player-history-table-body").html(""); //possibly this should report loading
    }

    //run ajax to recent event histories
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data:
        {
            playerID: playerID,
            sportID: sportID,
            limitOffset: eventHistoryRowCount,
            ajaxMethod: "player-event-history"
        },
        success: function(data)
        {
            //parse the returned data
            var jsonData = JSON.parse(data);

            var currentHTML = $("#player-history-table-body").html();

            for (var i=0; i<jsonData.length; i++)
            {
                var event = jsonData[i][0];

                if ((eventHistoryRowCount % 2) == 0)
                {
                    // 'even' row
                    currentHTML = currentHTML + "<tr class='even-row'>";
                }
                else
                {
                    currentHTML = currentHTML + "<tr class='odd-row'>";
                }

                currentHTML = currentHTML + "<td><a href='./event-profile.php?id=" + event.event_id + "' >" + event.event_name + "</a></td>";

                if(event.SDBefore >= 0 && event.SDBefore <= 50)
                {
                    currentHTML = currentHTML + "<td>" + event.meanBefore + "<span class='sd-value-green'>&plusmn" + event.SDBefore + "</span></td>";
                }

                if(event.SDBefore > 50 && event.SDBefore < 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanBefore + "<span class='sd-value-orange'>&plusmn" + event.SDBefore + "</span></td>";
                }

                if(event.SDBefore > 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanBefore + "<span class='sd-value-red'>&plusmn" + event.SDBefore + "</span></td>";
                }

                var pointChange = event.meanAfter - event.meanBefore;

                currentHTML = currentHTML + "<td>" + (pointChange<0?"":"+") + pointChange + "</td>";

                if(event.SDAfter >= 0 && event.SDAfter <= 50)
                {
                    currentHTML = currentHTML + "<td>" + event.meanAfter + "<span class='sd-value-green'>&plusmn" + event.SDAfter + "</span></td>";
                }

                if(event.SDAfter > 50 && event.SDAfter < 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanAfter + "<span class='sd-value-orange'>&plusmn" + event.SDAfter + "</span></td>";
                }

                if(event.SDAfter > 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanAfter + "<span class='sd-value-red'>&plusmn" + event.SDAfter + "</span></td>";
                }

                currentHTML = currentHTML + "</td>";


                eventHistoryRowCount++;
            }

            $("#player-history-table-body").html(currentHTML);
        }
    });
 }

 function updateTeamSport()
{
     //get the team id from url
     var params = (new URL(document.location)).searchParams;
     var teamID = params.get('team-id');

     //get sport ID
     newSportID = $("#team-select-sport").val();
     newSportName = $("#team-select-sport option:selected").text();

     $(".team-profile-sport-name").html(newSportName);
     $(".mean-value").html("Loading");
     $(".sd-value").html("Loading");

     //run ajax to update sd and mean
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data:
        {
            teamID: teamID,
            sportID: newSportID,
            ajaxMethod: "get-team-rating"
        },
        success: function(data)
        {

            //parse the returned data
            var jsonData = JSON.parse(data);

            $(".mean-value").html(parseInt(jsonData.mean));
            $(".sd-value").html("&plusmn; " + parseInt(jsonData.sd));
        }
    });

    addTeamEventHistory(true);
}

 //listener for change of sport on team profile page
 $("#team-select-sport").change(updateTeamSport);

 function addTeamEventHistory(changeSport)
 {
    //get the team id from url
    var params = (new URL(document.location)).searchParams;
    var teamID = params.get('team-id');

    //get sport ID
    sportID = $("#team-select-sport").val();

    if (changeSport)
    {
        //set count to zero and reset the table
        eventHistoryRowCount = 0;
        $("#team-history-table-body").html(""); //possibly this should report loading
    }

    //run ajax to recent event histories
    $.ajax
    ({
        url: "./ajax.php",
        type: "POST",
        dataType: "text",
        data:
        {
            teamID: teamID,
            sportID: sportID,
            limitOffset: eventHistoryRowCount,
            ajaxMethod: "team-event-history"
        },
        success: function(data)
        {
            //parse the returned data
            var jsonData = JSON.parse(data);

            var currentHTML = $("#team-history-table-body").html();

            for (var i=0; i<jsonData.length; i++)
            {
                var event = jsonData[i][0];

                if ((eventHistoryRowCount % 2) == 0)
                {
                    currentHTML = currentHTML + "<tr class='even-row'>";
                }
                else
                {
                    currentHTML = currentHTML + "<tr class='odd-row'>";
                }

                currentHTML = currentHTML + "<td><a href='./event-profile.php?id=" + event.event_id + "' >" + event.event_name + "</a></td>";

                if(event.SDBefore >= 0 && event.SDBefore <= 50)
                {
                    currentHTML = currentHTML + "<td>" + event.meanBefore + "<span class='sd-value-green'>&plusmn" + event.SDBefore + "</span></td>";
                }

                if(event.SDBefore > 50 && event.SDBefore < 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanBefore + "<span class='sd-value-orange'>&plusmn" + event.SDBefore + "</span></td>";
                }

                if(event.SDBefore > 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanBefore + "<span class='sd-value-red'>&plusmn" + event.SDBefore + "</span></td>";
                }

                var pointChange = event.meanAfter - event.meanBefore;

                currentHTML = currentHTML + "<td>" + (pointChange<0?"":"+") + pointChange + "</td>";

                if(event.SDAfter >= 0 && event.SDAfter <= 50)
                {
                    currentHTML = currentHTML + "<td>" + event.meanAfter + "<span class='sd-value-green'>&plusmn" + event.SDAfter + "</span></td>";
                }

                if(event.SDAfter > 50 && event.SDAfter < 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanAfter + "<span class='sd-value-orange'>&plusmn" + event.SDAfter + "</span></td>";
                }

                if(event.SDAfter > 100)
                {
                    currentHTML = currentHTML + "<td>" + event.meanAfter + "<span class='sd-value-red'>&plusmn" + event.SDAfter + "</span></td>";
                }

                currentHTML = currentHTML + "</td>";


                eventHistoryRowCount++;
            }

            $("#team-history-table-body").html(currentHTML);
        }
    });
 }

 $( function(){
    $(".profile-sport-name").html($("#profile-select-sport option:selected").text());
    addEventHistory(true);
 });

 $("#player-history-view-more").click(function(){
    addEventHistory(false);
 });

 $( function(){
    $(".team-profile-sport-name").html($("#team-select-sport option:selected").text());
    addTeamEventHistory(true);
 });

 $("#team-history-view-more").click(function(){
    addTeamEventHistory(false);
 });

/*
 * -------------------------------------------------------------*
 * 		Begin Add Player Section								*
 * 																*
 * -------------------------------------------------------------*
 */

function showAddPlayerModal()
{
	document.querySelector(".add-player-border").style.display = "flex";

}
function hideAddPlayerModal()
{
  document.querySelector(".add-player-border").style.display = "none";
}
function addPlayer()
{
  //$('#add-player-button').click(function (){
    var playerGivenName = $("#player-given-name").val();
    var playerFamilyName = $("#player-family-name").val();
    var playerGenderID = $("#player-gender-ID").val();
    var playerBirthDate = $("#player-birth-date").val();
    var playerEmail = $("#player-email").val();
    var playerClubID = $("#player-club-ID").val();

    $.ajax({
      url: "./ajax.php",
      type:'post',
      datatype: "text",
      data :{
        playerGivenName: playerGivenName,
        playerFamilyName: playerFamilyName,
        playerGenderID: playerGenderID,
        playerBirthDate: playerBirthDate,
        playerEmail: playerEmail,
        playerClubID: playerClubID,
        ajaxMethod: "add-player-manager"
      },
      success: function(data)
      {
        hideAddPlayerModal();

      }

    });

  //});
}

//sets up state/country listener
$("#player-country-id").change(function(){
    uploadEventChangeStates($("#player-country-id"),$("#player-state-ID"));
    });

//on page load
$( function() {
    uploadEventChangeStates($("#player-country-id"),$("#player-state-ID"));	//gets states based on country
});

/**
 * -------------------------------------------------------------*
 * 		Begin Advanced Player Section								*
 * 																*
 * -------------------------------------------------------------*
 */

/**
 * -------------------------------------------------------------*
 * 		Begin Initial Rating Section								*
 * 																*
 * -------------------------------------------------------------*
 */

function prefillTextbox()
{
    $("#player-initial-rating").change(function(){
  if ($(this).val() == 250)
  {
    $("#initial-mean-ID").val('250');
    $("#initial-sd-ID").val('100');
  }
  else if ($(this).val() == 500)
  {
    $("#initial-mean-ID").val('500');
    $("#initial-sd-ID").val('150');
  }
  else
  {
    $("#initial-mean-ID").val('1000');
    $("#initial-sd-ID").val('250');
  }
    });
}

function showInitialRatingModal(playerID, sportID)
{
  document.querySelector(".initial-rating-border").style.display = "flex";
  $("#hidden-sport-ID").val(sportID);
  $("#hidden-player-ID").val(playerID);
}

function hideInitialRatingModal()
{
  document.querySelector(".initial-rating-border").style.display="none";
  $("#hidden-sport-ID").val("");
  $("#hidden-player-ID").val("");
}

function addRating()
{
  var playerID = $("#hidden-player-ID").val();
  var sportID = $("#hidden-sport-ID").val();
  var meanID = $("#initial-mean-ID").val();
  var sdID = $("#initial-sd-ID").val();

  $.ajax({
            url: "./ajax.php",
            type: 'POST',
            datatype: "text",
            data :{
              meanID: meanID,
              sdID: sdID,
              playerID: playerID,
              sportID: sportID,
              ajaxMethod: "initial-rating-Manager"
              },
            success: function(data)
            {
              hideInitialRatingModal();
            }

          });
}

function  setupMatchAutoCompleteAdvancedSearch()
{
  $(".advanced-player-name").autocomplete({
    source:
        function( request, response )
        {
            // Fetch data
            $.ajax({
                url: "./ajax.php",
                type: 'POST',
                dataType: "json",
                data:
                {
                    name: request.term,
                    ajaxMethod: "get-all-player"
                },
                success: function( data )
                {
                    response( data );
                }
            });
        },
        select: function(event,ui)
        {
            //player has been chosen from advanced search list. fill in the box on main page.

            advancedSeachPlayerIDInput.val(ui.item.id);
            advancedSeachPlayerNameInput.val(ui.item.label);

            hideAdvancedSearchModal();

            //when an item is selected it is assumed that no error exists, remove the validity message
            var playerID = ui.item.id;
            setInitialRating(playerID);

             $( ".winner-loser-field").each(function ()
             {
                this.setCustomValidity('');
             });

        }

  });
}

/**
 *-----------------------------------------
 * Club Search Page --------------
 * ---------------------
 */

$(".club-page-selector").click(function(event){
        var newPage = event.target.id;
        $(".club-search-results").hide();
        var selector = ".club-search-results-page-" + newPage;
        $(selector).show();
    });

/**
 *--------------------------
 *event search page
 *--------------------------
 */

$(".event-page-selector").click(function(event){
        var newPage = event.target.id;
        $(".event-search-results").hide();
        var selector = ".event-search-results-page-" + newPage;
        $(selector).show();
    });

/**
 *----------------------------
 *Club profile page
 *------------------------------
 */

$(".club-players-page-selector").click(function(event){
        var newPage = event.target.id;
        $(".club-players-search-results").hide();
        var selector = ".club-players-search-results-page-" + newPage;
        $(selector).show();
    });

$(".club-events-page-selector").click(function(event){
        var newPage = event.target.id;
        $(".club-events-search-results").hide();
        var selector = ".club-events-search-results-page-" + newPage;
        $(selector).show();
    });

<?php
    $title = "Peterman Ratings | Players";

    include("./includes/header.php");
    include("./includes/navigation.php");
?>

<script type="text/javascript">
	var $_POST = <?php echo json_encode($_POST); ?>;
</script>

<article id="player-page-article">

    <div class="player-search-filter-container">


        <div id="player-search-filter-line">
            <h1 id="player-search-filter-title">Search for a Player</h1>
        </div>

          <div class="top-row-filter-inputs">

                <input type="text" id="player-name-filter" placeholder="Enter Player Name">

                <input type="text" id="player-age-min-filter" placeholder="Min Age" pattern="[0-9]{1,3}">

                <span id="player-age-filter-dash">-</span>

                <input type="text" id="player-age-max-filter" placeholder="Max Age" pattern="[0-9]{1,3}">

                <select id="player-country-filter">
                    <option selected>Select Country</option>
                      <?php

                        $countries = $contentManager->getAllCountries()->fetchAll();

                        foreach($countries as $country)
                        {
                            echo "<option value='".$country['country_id']."'>".$country['name']."</option>";
                        }

                      ?>
                </select>

            </div>

            <div class="middle-row-filter-inputs">

                <input type="text" id="player-club-filter" placeholder="Enter Club Name">

                <input type="text" id="player-recent-match-filter" placeholder="Last Played" onfocus="(this.type='date')" onblur="(this.type='text')">

                <select id="player-state-filter">
                </select>

            </div>

            <div class="bottom-row-filter-inputs">

                <div class="favourite-checkbox-border">
                  <input id="toggle-favourite-checkbox" type="checkbox" value="Favourited Players">
                  <label id="favourite-checkbox-label" data-text="Show Favourited Players" for="toggle-favourite-checkbox"></label>
                </div>

                <button id="submit-search-filter">Search</button>

            </div>

    </div>

    <div class="player-search-result-container">
    </div>

</article>

<?php
    include("./includes/footer.php");
?>

<script src=./javascript/pagination.js></script>

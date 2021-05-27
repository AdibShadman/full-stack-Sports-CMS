<div class="create-club-modal-background">
  <div class="create-club-modal-content">

  	<div class="create-club-modal-header">
  	  <div class="create-club-modal-exit-button" onclick="hideCreateClubModal()">+</div>
  	</div>

  	<div class="create-club-modal-fields">
  	  <h2>Create Club</h2>
  	  <hr/>
      <div class="create-club-modal-field-wrapper">
        <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">

        <div class="create-club-input-group-double">
          <input type="text" id="input-club-name" name="club-name" placeholder="Name" pattern="[a-zA-Z\s]{1,90}" required title="Club name must be within 1-90 characters">
          <select name="select-sport" id="create-club-select-sport">
            <?php
              $sports = $contentManager->getAllSports();

              while ($sport = $sports->fetch())
              {
                  echo "<option value=\"".$sport["sport_id"]."\">".$sport["name"]."</option>";
              }
            ?>
          </select>
        </div>

        <div class="create-club-input-group-double">
          <select name="select-country" id="create-club-select-country">
          <?php
              $countries = $contentManager->getAllCountries();

              while ($country = $countries->fetch())
              {
                echo "<option value=\"".$country["country_id"]."\">".$country["name"]."</option>";
              }
          ?>
          </select>
          <select name="state-name" id="create-club-select-state"></select>
        </div>

        <button type="submit" name="create-club" id="create-club-button">Create Club</button>
      </form>
      </div>
  	</div>
  </div>
</div>

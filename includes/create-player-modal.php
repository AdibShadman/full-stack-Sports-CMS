<div class="create-player-modal-background">
  <div class="create-player-modal-content">

  	<div class="create-player-modal-header">
  	  <div class="create-player-modal-exit-button" onclick="hideCreatePlayerModal()">+</div>
  	</div>

  	<div class="create-player-modal-fields">
  	  <h2>Add Player</h2>
  	  <hr/>
      <div class="create-player-modal-field-wrapper">
        <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">

        <div class="create-player-input-group-double">
          <input type="text" id="input-create-player-given-name" name="player-given-name" placeholder="Given Name" pattern="[a-zA-Z\s]{1,45}" required title="Player given name must be within 1-45 characters">
          <input type="text" id="input-create-player-family-name" name="player-family-name" placeholder="Family Name" pattern="[a-zA-Z\s]{1,45}" required title="Player family name must be within 1-45 characters">
        </div>
        <input type="hidden" id="create-player-hidden-club-id" name="hidden-club-ID">
        <div class="register-input-group-double">

          <select id="create-player-gender" name="create-player-gender">';
            <option value="M" selected>Male</option>
            <option value="F">Female</option>';
          </select>

          <input class="create-player-date" class="event-field-date" name="event-date" id="event-date" placeholder="Date of Birth" onfocus="(this.type='date')" onblur="(this.type='text')"> 
        </div>

        <input type="email" id="create-player-email" name="email" placeholder="Email" pattern="{7,75}" required title="Email must not exceed 75 characters"> 

        <div class="create-player-input-group-double">
          <select name="select-country" id="player-create-select-country">
          <?php
              $countries = $contentManager->getAllCountries();

              while ($country = $countries->fetch())
              {
                echo "<option value=\"".$country["country_id"]."\">".$country["name"]."</option>";
              }
          ?>
          </select>
          <select name="state-name" id="player-create-select-state"></select>
        </div>

        <button type="submit" name="create-player" id="create-player-button">Confirm</button>
      </form>
      </div>
  	</div>
  </div>
</div>

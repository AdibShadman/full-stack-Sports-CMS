<?php
  $title = "Peterman Ratings | Team Profile";

  include("./includes/header.php");
  include("./includes/navigation.php");

  $teamID = $_GET['team-id'];

  $teamSports = $contentManager->getTeamSports($teamID);
  $firstSport = $teamSports->fetch();
  $teamRating = $contentManager->getTeamRating($teamID, $firstSport['sport_id']);

  $teamPlayers = $contentManager->getTeamPlayersBySport($teamID, $firstSport['sport_id']); 

  $playerNames = $contentManager->getTeamPlayerNames($teamPlayers['player_one_id'], $teamPlayers['player_two_id']);

  $playerOneInfo = $contentManager->getSpecificPlayerInformation($teamPlayers['player_one_id']);
  $playerTwoInfo = $contentManager->getSpecificPlayerInformation($teamPlayers['player_two_id']);

  $today = new Datetime(date("Y-m-d"));
  $playerOneDob = new DateTime($playerOneInfo["date_of_birth"]);
  $playerOneAge = $today->diff($playerOneDob)->y;
  $playerTwoDob = new DateTime($playerTwoInfo["date_of_birth"]);
  $playerTwoAge = $today->diff($playerTwoDob)->y;
?>

<article id="team-profile-page-article">

  <div class="player-details-border">

    <h1>
      <?php
        echo $playerNames['player_one'].", ".$playerNames['player_two'];
      ?>
    </h1>

    <h2 class="team-profile-sport-name"></h2>

    <div class="select-sport-border">
      <b>Select Sport</b>
      <span class="select-sport-menu">
      <select id="team-select-sport">
        <?php
          $teamSports = $contentManager->getTeamSports($teamID);

          while($sports = $teamSports->fetch())
          {
              echo "<option value=\"".$sports["sport_id"]."\">".$sports["name"]."</option>";
          }
        ?>
      </select>
    </span>
    </div>

    <ul class="player-bio-list">

      <li id="player-bio-row">
        <span id="player-bio-row-heading"><b>Player 1</b></span>
        <span id="player-bio-row-value">
          <a id="player-name-link" href="profile.php?profile-id=<?php echo $teamPlayers['player_one_id']; ?>">
            <?php
              echo $playerNames['player_one'];
            ?>
          </a>
        </span>
      </li>
      <li id="player-bio-row">
        <span id="player-bio-row-heading"><b>Gender</b></span>
        <span id="player-bio-row-value"> <?php echo $playerOneInfo["gender"]; ?> </span>
      </li>
      <li id="player-bio-row">
        <span id="player-bio-row-heading"><b>Age</b></span>
        <span id="player-bio-row-value"> <?php echo $playerOneAge; ?> </span>
      </li>

      <li id="player-bio-row">
        <span id="player-bio-row-heading"><b>Player 2</b></span>
        <span id="player-bio-row-value">
          <a id="player-name-link" href="profile.php?profile-id=<?php echo $teamPlayers['player_two_id']; ?>">
            <?php
              echo $playerNames['player_two'];
            ?>
          </a>
        </span>
      </li>
      <li id="player-bio-row">
        <span id="player-bio-row-heading"><b>Gender</b></span>
        <span id="player-bio-row-value"> <?php echo $playerTwoInfo["gender"]; ?> </span>
      </li>
      <li id="player-bio-row">
        <span id="player-bio-row-heading"><b>Age</b></span>
        <span id="player-bio-row-value"> <?php echo $playerTwoAge; ?> </span>
      </li>

    </ul>

    <div class="team-rating-border">
      <div class="team-mean-border">
        <p class="mean-value">
          <?php
            echo (int)$teamRating['mean'];
          ?>
        </p>
        <p>Mean</p>
      </div>

      <div class="team-sd-border">
        <?php
          if($teamRating['standard_deviation'] >= 0 && $teamRating['standard_deviation'] <= 50)
          {
        ?>
          <p class="sd-value sd-value-green">
            &plusmn
              <?php
                echo (int)$teamRating['standard_deviation'];
              ?>
          </p>
        <?php
          }

          if($teamRating['standard_deviation'] > 50 && $teamRating['standard_deviation'] < 100)
          {
        ?>
          <p class="sd-value sd-value-orange">
            &plusmn
              <?php
                echo (int)$teamRating['standard_deviation'];
              ?>
          </p>
        <?php
          }

          if($teamRating['standard_deviation'] > 100)
          {
        ?>
          <p class="sd-value sd-value-red">
            &plusmn
            <?php
              echo (int)$teamRating['standard_deviation'];
            ?>
          </p>
        <?php
          }
        ?>
        <p class="sd-name">Standard Deviation</p>
      </div>
    </div>
  </div>

	<div class="team-history-border">

    <h1>Team History</h1>

    <h2 class="team-profile-sport-name"></h2>

    <table class="team-history-table">
		  <tr class="odd-row">
  			<th>Event</th>
  			<th>Initial Rating</th>
  			<th>Point Change</th>
  			<th>Final Rating</th>
		  </tr>

		  <tbody id="team-history-table-body">
		  </tbody>
    </table>

    <p id="team-history-view-more">
      View More
    </p>

  </div>

</article>

<?php
  include("./includes/footer.php");
?>

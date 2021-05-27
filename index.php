<?php 
    $title = "Peterman Ratings | Home";

    include("./includes/header.php");
    include("./includes/navigation.php");

?>

<article>

  <div class="slideshow-container">
    <div class="slideshow-image">
      <img src="./editable/images/home-slideshow-1.png">
    </div>

    <div class="slideshow-image">
      <img src="./editable/images/home-slideshow-2.png">
    </div>

    <div class="slideshow-image">
      <img src="./editable/images/home-slideshow-3.png">
    </div>
  </div>

  <div class="search-container">

    <div class="search-tab-container">
      <button class="tab-selection" onclick="switchTab(this, 'player-content')" id="player-tab">Players</button>
      <button class="tab-selection" onclick="switchTab(this, 'event-content')" id="event-tab">Events</button>
      <button class="tab-selection" onclick="switchTab(this, 'club-content')" id="club-tab">Clubs</button>
    </div>

    <div id="player-content" class="tab-content">
      <div class="search-box">
        <div class="search-field">
			<form action="./players.php" method="post">
			  <input type="txt" class="search-input" name="home-player-search" placeholder="Search for Players">
			  <button class="search-button" type="submit">Search</button>
			</form>
        </div>
      </div>
    </div>

    <div id="event-content" class="tab-content">
      <div class="search-box">
        <div class="search-field">
			<form action="./events.php" method="post">
			  <input type="txt" class="search-input" name="search" placeholder="Search for Events">
			  <button class="search-button" onclick="" type="submit">Search</button>
          </form>
        </div>
      </div>
    </div>

    <div id="club-content" class="tab-content">
      <div class="search-box">
        <div class="search-field">
			<form action="./clubs.php" method="post">
			  <input type="txt" class="search-input" name="search" placeholder="Search for Clubs">
			  <button class="search-button" onclick="" type="submit">Search</button>
          </form>
        </div>
      </div>
    </div>  
  </div>

</article>

<?php
    include("./includes/footer.php");
?>

<header>    
  <nav>

    <div class="nav-menu-logo">
      <a href="./index.php">
        <img src="./resources/images/logo.png" alt="">
      </a>
    </div>

    <div class="nav-menu-links">
      <a href="./index.php">home</a>
      <a href="./players.php">players</a>
      <a href="./events.php">events</a>
      <a href="./clubs.php">clubs</a>
      <a href="#" class="nav-sign-in-button" onclick="toggleDropdownMenu()"> 
        <?php 
          if(!$account->isLoggedIn())
          { 
            echo "Sign In"; 
          }
          else
          { 
            echo "Administration"; 
          }
        ?>
      </a>

      <?php
        if($account->isLoggedIn())
        {
          include("nav-menu-signed-in.php");
        }
        else
        {
          include("nav-menu-signed-out.php");
        }
      ?>

   </nav>
</header>

   

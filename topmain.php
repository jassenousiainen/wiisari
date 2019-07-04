<?php

// display the topbar //

echo "<header>";

  echo '<nav>
  <a class="logolink" href="/index.php">
    <h2 class="logotext">WIISARI</h2>
  </a>';

  if (isset($_SESSION['logged_in_user'])) {
    if ($_SERVER['REQUEST_URI'] != '/timeclock.php') {
      echo "<a href='/timeclock.php' style='margin-left:20px;'><i class='fas fa-clock'></i> Kellotusasema</a>";
    }
    echo '</nav>';

    echo " <div class='loggedBar'>";
    echo $_SESSION['logged_in_user']->displayName .": ";
    echo "<a href='/mypage.php' id='my'><i class='fas fa-user'></i> Oma sivu</a>";
    echo "<a href='/logout.php' id='logout'><i class='fas fa-sign-out-alt'></i> Kirjaudu Ulos</a>";
  } else {
    echo '</nav>';
    echo " <div class='loggedBar'>";
    echo '<a href="/loginpage.php"><i class="fas fa-sign-in-alt"></i> Oma sivu</a>';
  }
echo "</div>";

echo "</header>";
echo '<section class="top-skew-bg blue">
<div class="elipsed-border">
</div>
</section>';
?>

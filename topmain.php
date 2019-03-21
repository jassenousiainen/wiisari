<?php

// display the topbar //

echo "<header>";

  echo '<nav>
  <a class="logolink" href="/timeclock.php">
    <h2 class="logotext">WIISARI</h2>
  </a>';

  if (isset($_SESSION['logged_in_user'])) {
    if ($_SERVER['REQUEST_URI'] != '/timeclock.php') {
      echo "<a href='/index.php' style='margin-left:20px;'><i class='fas fa-clock'></i> Etusivu</a>";
    }
    if ($_SESSION['logged_in_user']->admin == 1) {
      echo "<a href='/admin/index.php'><i class='fas fa-toolbox'></i> Hallinta</a>";
    }
    if ($_SESSION['logged_in_user']->time_admin == 1) {
      echo "<a href='/time_editor.php'><i class='fas fa-toolbox'></i> Kellotuseditori</a>";
    }
    if ($_SESSION['logged_in_user']->reports == 1) {
      echo "<a href='/reports/total_hours.php'><i class='fas fa-toolbox'></i> Raportit</a>";
    }
    echo '</nav>';

    echo " <div class='loggedBar'>";
    echo $_SESSION['logged_in_user']->displayname .": ";
    echo "<a href='/mypage.php'><i class='fas fa-user'></i> Oma sivu</a>";
    echo "<a href='/logout.php'><i class='fas fa-sign-out-alt'></i> Kirjaudu Ulos</a>";
  } else {
    echo '</nav>';
    echo " <div class='loggedBar'>";
    echo '<a href="/loginpage.php"><i class="fas fa-sign-in-alt"></i> Oma sivu</a>';
  }
echo "</div>";

echo "</header>";
echo '<section class="mypageHead"></section>';
?>

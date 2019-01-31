<?php

// display the topbar //

echo "<header>";

  echo '<nav>
  <a class="logolink" href="/timeclock.php">
    <h2 class="logotext">WIISARI</h2>
  </a>';

  if (isset($_SESSION['logged_in_user'])) {
    echo "<a href='/index.php' style='margin-left:20px;'><i class='fas fa-clock'></i> Etusivu</a>";
    echo "<a href='/mypage.php'><i class='fas fa-user'></i> Oma sivu</a>";
    echo "<a href='/reports/personalreport.php'><i class='fas fa-user'></i> Omat tunnit</a>";
    if ($_SESSION['logged_in_user']->admin == 1) {
      echo "<a href='/admin/index.php'><i class='fas fa-toolbox'></i> Hallinta</a>";
    }
    if ($_SESSION['logged_in_user']->reports == 1) {
      echo "<a href='/reports/index.php'><i class='fas fa-calendar-alt'></i> Raportit</a>";
    }
    echo '</nav>';

    echo " <div class='loggedBar'>";
    echo $_SESSION['logged_in_user']->displayname.": <a href='/logout.php'>Kirjaudu Ulos <i class='fas fa-sign-out-alt'></i></a>";
  } else {
    echo '</nav>';
    echo " <div class='loggedBar'>";
    echo '<a href="/loginpage.php"><i class="fas fa-sign-in-alt"></i> Kirjaudu</a>';
  }
echo "</div>";

echo "</header>";
?>

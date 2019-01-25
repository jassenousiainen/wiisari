<?php

echo '
<div class="ylalaatikko">
  <a class="otsikkolinkki" href="/timeclock.php">
    <h2 class="ylaotsikko">WIISARI</h2>
  </a>
</div>
    ';
//<img src="/images/icons/clock_title.png" style="float: left;height: 40px;margin: 12px 10px 0 10px;">

// display the topbar //

echo "<header>";


  echo "<a href='/index.php' style='margin-left:20px;'><i class='fas fa-clock'></i> Etusivu</a>";

  if (isset($_SESSION['logged_in'])) {
    echo "<a href='/reports/personalreport.php'><i class='fas fa-user'></i> Omat tunnit</a>";
  }
  if (isset($_SESSION['valid_user'])) {
    echo "<a href='/admin/index.php'><i class='fas fa-toolbox'></i> Hallinta</a>";
  }
  if (isset($_SESSION['valid_reports_user'])) {
    echo "<a href='/reports/index.php'><i class='fas fa-calendar-alt'></i> Raportit</a>";
  }


if (isset($_SESSION['logged_in'])) {
  $logged_in_user = $_SESSION['logged_in'];
  $login_in_displayname = tc_select_value("displayname", "employees", "empfullname = ?", $logged_in_user);

  echo " <div class='loggedBar' style='background: orange; border-radius: 14px; padding-left: 15px;'>";
  echo "<span>$login_in_displayname: </span><a href='/logout.php'>Kirjaudu Ulos <i class='fas fa-sign-out-alt'></i></a>";
} else {
  echo " <div class='loggedBar'>";
  echo '<a href="/loginpage.php"><i class="fas fa-sign-in-alt"></i> Kirjaudu</a>';
}
echo "</div>";

echo "</header>";
?>

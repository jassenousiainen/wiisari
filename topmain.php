<?php

echo '
<div class="ylalaatikko">
  <a class="otsikkolinkki" href="/timeclock.php">
    <img src="/images/icons/clock_title.png" style="float: left;height: 40px;margin: 12px 10px 0 10px;">
    <h2 class="ylaotsikko">Kellokalle</h2>
  </a>
</div>
    ';


// display the topbar //

echo "<header>";

echo "
  <a href='/index.php'><i class='fas fa-clock'></i> Etusivu</a>
  <a href='/login.php'><i class='fas fa-toolbox'></i> Hallinta</a>
  <a href='/login_reports.php'><i class='fas fa-calendar-alt'></i> Raportit</a>
  <a href='/reports/personalreport.php'><i class='fas fa-user'></i> Omat tunnit</a>
";

echo " <div class='loggedBar'>
  <div class='loggedBarUnskew'>";
if (isset($_SESSION['valid_user'])) {
    $logged_in_user = $_SESSION['valid_user'];
    echo "    <span>$logged_in_user: </span>";
} else if (isset($_SESSION['time_admin_valid_user'])) {
    $logged_in_user = $_SESSION['time_admin_valid_user'];
    echo "    <span>$logged_in_user: </span>";
} else if (isset($_SESSION['valid_reports_user'])) {
    $logged_in_user = $_SESSION['valid_reports_user'];
    echo "    <span>$logged_in_user: </span>";
};

if ((isset($_SESSION['valid_user'])) || (isset($_SESSION['valid_reports_user'])) || (isset($_SESSION['time_admin_valid_user']))) {
  echo "<a href='/logout.php'>Kirjaudu Ulos</a>";
}
echo "</div></div>";

echo "</header>";
?>

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

echo "
  <a href='/index.php' style='margin-left:20px;'><i class='fas fa-clock'></i> Etusivu</a>
  <a href='/reports/personalreport.php'><i class='fas fa-user'></i> Omat tunnit</a>
";
/*<a href='/login.php'><i class='fas fa-toolbox'></i> Hallinta</a>
<a href='/login_reports.php'><i class='fas fa-calendar-alt'></i> Raportit</a>*/

echo " <div class='loggedBar'>";

if (isset($_SESSION['logged_in'])) {
  $logged_in_user = $_SESSION['logged_in'];
  echo "<span>$logged_in_user: </span><a href='/logout.php'>Kirjaudu Ulos</a>";
} else {
  echo '<a href="/adminlogin.php"><i class="fas fa-sign-in-alt"></i> Kirjaudu</a>';
}
echo "</div>";

echo "</header>";
?>

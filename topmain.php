<?php

// displays both the topmenu and sidemenu (sidemenu is only visible in other pages than punchstation)

echo "<header>";

if ($_SERVER['PHP_SELF'] == "/timeclock.php") {
  echo '<nav>
  <a class="logolink" href="/index.php">
    <h2 class="logotext">WIISARI</h2>
  </a>';

  if (isset($_SESSION['logged_in_user'])) {
    echo '</nav>';
    echo "<div class='loggedBar'>";
    echo $_SESSION['logged_in_user']->displayName .": ";
    echo "<a href='/mypage.php' id='my'><i class='fas fa-user'></i> Oma sivu</a>";
    echo "<a href='/logout.php' id='logout'><i class='fas fa-sign-out-alt'></i> Kirjaudu Ulos</a>";
  } else {
    echo '</nav>';
    echo " <div class='loggedBar'>";
    echo '<a href="/loginpage.php"><i class="fas fa-sign-in-alt"></i> Oma sivu</a>';
  }
  echo "</div>";

} else {

echo '
<nav class="topmenu">
  <a class="logolink" href="/index.php">
    <h2 class="logotext">WIISARI</h2>
  </a>
  <div class="loggedBar">';
    echo '
    <a id="profile" title="'.$_SESSION['logged_in_user']->displayName.'"><i class="fas fa-user-circle"></i></a>
    <div id="profileBox">
      <svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="svg-triangle" width="100" height="30">
        <path d="M 50,5 95,97.5 5,97.5 z"/>
      </svg>
      <div class="profileContainer">
        <h3 class="profileName">'.$_SESSION['logged_in_user']->displayName.'</h3>
        <p class="profileLevel">Oikeustaso: '.$_SESSION['logged_in_user']->level.'</p>
        <a class="btn" href="/mypage.php"><i class="fas fa-home"></i> Oma sivu</a>
        <a class="btn" style="background-color: var(--orange)" href="#"><i class="fas fa-user"></i> Omat tiedot</a>
        <a class="btn del" href="/logout.php"><i class="fas fa-sign-in-alt"></i> Kirjaudu ulos</a>
      </div>
    </div>
    <div>
</nav>
';

echo '
<nav class="sidemenu">
  <a class="expand fas fa-bars" title="laajenna"></a>
  <ul>';
    if ($_SERVER['PHP_SELF'] == "/mypage.php") { echo '<li class="current">'; }
    else { echo '<li>'; }
    echo '
      <a href="/mypage.php" title="Oma sivu">
        <i class="fas fa-home"></i>
        <p class="caption">Oma sivu</p>
      </a>
    </li>';
    if ($_SESSION['logged_in_user']->level > 0) {
      if (dirname($_SERVER['PHP_SELF']) == "/employees") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/employees/employees.php" title="Henkilöstö">
          <i class="fas fa-id-card"></i>
          <p class="caption">Henkilöstö</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level >= 3) {
      if (dirname($_SERVER['PHP_SELF']) == "/offices") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/offices/offices.php" title="Toimistot">
          <i class="fas fa-building"></i>
          <p class="caption">Toimistot</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level >= 3) {
      if (dirname($_SERVER['PHP_SELF']) == "/groups") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/groups/groups.php" title="Ryhmät">
          <i class="fas fa-users"></i>
          <p class="caption">Ryhmät</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level > 0) {
      if (dirname($_SERVER['PHP_SELF']) == "/reports") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/reports/total_hours.php" title="Työtunnit">
          <i class="fas fa-hourglass-half"></i>
          <p class="caption">Työtunnit</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level > 0) {
      if ($_SERVER['PHP_SELF'] == "/barcode-generator/barcodefetch.php") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/barcode-generator/barcodefetch.php" title="Viivakoodien tulostin">
          <i class="fas fa-barcode"></i>
          <p class="caption">Viivakoodien tulostin</p>
        </a>
      </li>';
    }
    echo '
    <li>
      <a href="/timeclock.php" title="Kellotusasema">
        <i class="fas fa-desktop"></i>
        <p class="caption">Kellotusasema</p>
      </a>
    </li>
  </ul>
  
</nav>';
}
echo "</header>";
/*
echo '<section class="top-skew-bg blue">
<div class="elipsed-border">
</div>
</section>';*/
?>

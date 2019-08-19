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
  <div class="loggedBar">
    <a id="profileIcon" title="'.$_SESSION['logged_in_user']->displayName.'">
      <i class="fas fa-user-circle"></i>
      <p class="profileName">'.$_SESSION['logged_in_user']->displayName.'</p>
    </a>
    <div id="profileBox">
      <div class="profileContainer">
        <a href="/mypage.php"><i class="fas fa-home"></i> Oma sivu</a>
        <a href="#"><i class="fas fa-user"></i> Omat tiedot</a>
        <a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Kirjaudu ulos</a>
      </div>
    </div>
    <div>
</nav>
';

echo '
<nav class="sidemenu">
  <h2 class="logotext">WIISARI</h2>
  <ul>';
    if ($_SERVER['PHP_SELF'] == "/mypage.php") { echo '<li class="current">'; }
    else { echo '<li>'; }
    echo '
      <a href="/mypage.php" title="Oma sivu">
        <i class="fas fa-home" style="color: var(--purple-light);"></i>
        <p class="caption">Oma sivu</p>
      </a>
    </li>';
    if ($_SESSION['logged_in_user']->level > 0) {
      if (dirname($_SERVER['PHP_SELF']) == "/employees") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/employees/employees.php" title="Henkilöstö">
          <i class="fas fa-id-card" style="color: var(--teal);"></i>
          <p class="caption">Henkilöstö</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level >= 3) {
      if (dirname($_SERVER['PHP_SELF']) == "/offices") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/offices/offices.php" title="Toimistot">
          <i class="fas fa-building" style="color: var(--lightgreen);"></i>
          <p class="caption">Toimistot</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level >= 3) {
      if (dirname($_SERVER['PHP_SELF']) == "/groups") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/groups/groups.php" title="Ryhmät">
          <i class="fas fa-users" style="color: #ffd600;"></i>
          <p class="caption">Ryhmät</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level > 0) {
      if (dirname($_SERVER['PHP_SELF']) == "/reports") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/reports/total_hours.php" title="Työtunnit">
          <i class="fas fa-hourglass-half" style="color: #f5365c;"></i>
          <p class="caption">Työtunnit</p>
        </a>
      </li>';
    }
    if ($_SESSION['logged_in_user']->level > 0) {
      if ($_SERVER['PHP_SELF'] == "/barcode-generator/barcodefetch.php") { echo '<li class="current">'; }
      else { echo '<li>'; }
      echo '
        <a href="/barcode-generator/barcodefetch.php" title="Viivakoodien tulostin">
          <i class="fas fa-barcode" style="color: #fb6340;"></i>
          <p class="caption">Viivakoodien tulostin</p>
        </a>
      </li>';
    }
    echo '
    <hr class="menu-separator">
    <li>
      <a href="/timeclock.php" title="Kellotusasema">
        <i class="fas fa-desktop" style="color: rgba(0,0,0,.5);"></i>
        <p class="caption">Kellotusasema</p>
      </a>
    </li>
  </ul>
  
</nav>';
}
echo "</header>";

echo '<section class="top-skew-bg purple">
<div class="elipsed-border">
</div>
</section>';
?>

<?php
require '../common.php';
session_start();
include '../header.php';
include 'topmain.php';

echo "<title>Henkilökohtaiset työtunnit</title>\n";

// User can't access the page unless they are logged in
if (!isset($_SESSION['logged_in_user'])) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


  $fullname = $_SESSION['logged_in_user']->userID;
  $displayname = $_SESSION['logged_in_user']->displayName;
  $timetoday = $_SESSION['logged_in_user']->getCurrentWorkTime();
  $weektime = $_SESSION['logged_in_user']->getWeekWorkTime();
  $monthtime = $_SESSION['logged_in_user']->getMonthWorkTime();

  $timeNow = time();

echo ' <section class="container">
        <div class="middleContent">
          <div class="box">
            <h2> '.$displayname.' - työtunnit ('. date('Y', $timeNow).')</h2>
            <div class="section">
              <table>';

if ( $timetoday > 0 ) {
  echo '      <tr>
                <td>Viimeisestä sisäänkirjauksesta:</td>
                <td><b>'.convertToHours($timetoday).'</b></td>
              </tr>
              <tr><td>&nbsp;</td></tr>';
}

echo '        <tr>
                <td>Työaikasi tällä viikolla (vko '.ltrim(date('W', $timeNow), 0).'): </td>
                <td><b>' .convertToHours($weektime[ltrim(date('W', $timeNow), 0)]). '</b></td>
              </tr>';

if ( ltrim(date('W', $timeNow), 0) > 1 ) {
  echo '      <tr>
                <td>Työaikasi viime viikolla (vko '.(ltrim(date('W', $timeNow), 0)-1).'): </td>
                <td>' .convertToHours($weektime[ltrim(date('W', $timeNow)-1, 0)]). ' </td>
              </tr>';
}

echo '        <tr><td>&nbsp;</td></tr>';

  if ($monthtime[12] > 0) echo '<tr> <td>Joulukuu:</td> <td>' .convertToHours((int)$monthtime[12]). '</td> </tr>';
  if ($monthtime[11] > 0) echo '<tr> <td>Marraskuu:</td> <td>' .convertToHours((int)$monthtime[11]). '</div><br>';
  if ($monthtime[10] > 0) echo '<tr> <td>Lokakuu:</td> <td>' .convertToHours((int)$monthtime[10]). '</td> </tr>';
  if ($monthtime[9] > 0) echo '<tr> <td>Syyskuu:</td> <td>' .convertToHours((int)$monthtime[9]). '</td> </tr>';
  if ($monthtime[8] > 0) echo '<tr> <td>Elokuu:</td> <td>' .convertToHours((int)$monthtime[8]). '</td> </tr>';
  if ($monthtime[7] > 0) echo '<tr> <td>Heinäkuu:</td> <td>' .convertToHours((int)$monthtime[7]). '</td> </tr>';
  if ($monthtime[6] > 0) echo '<tr> <td>Kesäkuu:</td> <td>' .convertToHours((int)$monthtime[6]). '</td> </tr>';
  if ($monthtime[5] > 0) echo '<tr> <td>Toukokuu:</td> <td>' .convertToHours((int)$monthtime[5]). '</td> </tr>';
  if ($monthtime[4] > 0) echo '<tr> <td>Huhtikuu:</td> <td>' .convertToHours((int)$monthtime[4]). '</td> </tr>';
  if ($monthtime[3] > 0) echo '<tr> <td>Maaliskuu:</td> <td>' .convertToHours((int)$monthtime[3]). '</td> </tr>';
  if ($monthtime[2] > 0) echo '<tr> <td>Helmikuu:</td> <td>' .convertToHours((int)$monthtime[2]). '</td> </tr>';
  if ($monthtime[1] > 0) echo '<tr> <td>Tammikuu:</td> <td>' .convertToHours((int)$monthtime[1]). '</td> </tr>';

  echo '</table></div></div></div></section>';

?>

<?php
require '../common.php';
session_start();
include 'header_post_reports.php';
include 'topmain.php';

echo "<title>Henkilökohtaiset työtunnit</title>\n";

// User can't access the page unless they are logged in
if (!isset($_SESSION['logged_in_user'])) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


  echo "<title>Omat Tunnit</title>\n";

  $timeNow = time();

  $fullname = $_SESSION['logged_in_user']->username;
  $displayname = $_SESSION['logged_in_user']->displayname;

  $monthtime = array_fill(1, 12, 0);
  $weektime = array_fill(1, 52, 0);

  $infoQuery = tc_query(<<<QUERY
SELECT *
FROM info
WHERE fullname = '$fullname' AND `inout` = 'out'
ORDER BY timestamp DESC
QUERY
);

  while ( $tempOut = mysqli_fetch_array($infoQuery) ) {   // Käydään läpi työntekijän kaikki kirjaukset
    if ( date('Y', $tempOut[3]) == date('Y', $timeNow) ) { // Lasketaan vain tämän vuoden kirjaukset
      $tempstamp = $tempOut[3];
      $month = date('n', $tempOut[3]); // 1-12
      $week = ltrim(date('W', $tempOut[3]), 0); // 1-52 (huomaa ltrimin käyttö aloittavien nollien poistamiseksi)

      $nextInfoQuery = tc_query( "SELECT * FROM info WHERE fullname = '$fullname' AND timestamp < '$tempstamp' ORDER BY timestamp DESC"); // Haetaan seuraava kirjaus (eli sisäänkirjaus)
      $tempIn = mysqli_fetch_row($nextInfoQuery);

      $time = (int)$tempOut[3] - (int)$tempIn[3]; // Lasketaan uloskirjauksen ja sisäänkirjauksen erotus
      if (is_numeric($time)) {
        $monthtime[$month] += $time;
        $weektime[$week] += $time;
      }

    } else {
      break;
    }
  }

  $timetoday = 0;
  if ( $_SESSION['logged_in_user']->inout_status == "in" ) {
    $timetoday = $timeNow - mysqli_fetch_row(tc_query( "SELECT timestamp FROM info WHERE fullname = '$fullname' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
  }

  echo '<div class="ownReportsBox" style="width:500px;">
          <h2> '.$displayname.' - työtunnit </h2>
          <center><p style="color: grey; margin: 0;"> Vuosi '. date('Y', $timeNow).'</p></center>';
          if ( $timetoday > 0 ) {
            echo'<p>Viimeisestä sisäänkirjauksesta: <b>'.convertToHours($timetoday).'</b></p>';
          }

  echo    '<p> Työaikasi tällä viikolla (vko '.ltrim(date('W', $timeNow), 0).'): <b>' .convertToHours($weektime[ltrim(date('W', $timeNow), 0)]). '</b> <br>';

  if ( ltrim(date('W', $timeNow), 0) > 1 ) {
    echo    'Työaikasi viime viikolla (vko '.(ltrim(date('W', $timeNow), 0)-1).'): ' .convertToHours($weektime[ltrim(date('W', $timeNow)-1, 0)]). ' </p> <br>';
  }

  if ($monthtime[12] > 0) echo 'Joulukuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[12]). '</div><br>';
  if ($monthtime[11] > 0) echo 'Marraskuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[11]). '</div><br>';
  if ($monthtime[10] > 0) echo 'Lokakuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[10]). '</div><br>';
  if ($monthtime[9] > 0) echo 'Syyskuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[9]). '</div><br>';
  if ($monthtime[8] > 0) echo 'Elokuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[8]). '</div><br>';
  if ($monthtime[7] > 0) echo 'Heinäkuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[7]). '</div><br>';
  if ($monthtime[6] > 0) echo 'Kesäkuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[6]). '</div><br>';
  if ($monthtime[5] > 0) echo 'Toukokuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[5]). '</div><br>';
  if ($monthtime[4] > 0) echo 'Huhtikuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[4]). '</div><br>';
  if ($monthtime[3] > 0) echo 'Maaliskuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[3]). '</div><br>';
  if ($monthtime[2] > 0) echo 'Helmikuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[2]). '</div><br>';
  if ($monthtime[1] > 0) echo 'Tammikuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[1]). '</div><br>';

  echo '</div>';

?>

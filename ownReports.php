<?php
session_start();

require 'common.php';
include 'header.php';
include 'topmain.php';

echo "<title>Omat Tunnit</title>\n";


function convertToHours($tmstmp) {
  $hours = floor($tmstmp / 3600);
  $minutes = floor(($tmstmp / 60) % 60);
  $seconds = $tmstmp % 60;
  if ($tmstmp > 0) {
    return $hours > 0 ? "$hours tuntia, $minutes minuuttia" : ($minutes > 0 ? "$minutes minuuttia, $seconds sekuntia" : "$seconds sekuntia");
  } else {
    return " ";
  }
}

$timeNow = time();
$barcode = (yes_no_bool($barcode_clockin) ? strtoupper($_POST['left_barcode']) : "");
$fullname = tc_select_value("empfullname", "employees", "barcode = ?", $barcode);
$displayname = tc_select_value("displayname", "employees", "barcode = ?", $barcode);

if (!has_value($fullname)) {
  echo '<h2>Hups! En löytänyt sinua. Kokeile uudestaan.</h2>';
}

$monthtime = array_fill(1, 12, " ");
$weektime = array_fill(1, 52, " ");

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
    $week = date('W', $tempOut[3]); // 1-52

    $nextInfoQuery = tc_query( "SELECT * FROM info WHERE fullname = '$fullname' AND timestamp < '$tempstamp' ORDER BY timestamp DESC"); // Haetaan seuraava kirjaus (eli sisäänkirjaus)
    $tempIn = mysqli_fetch_row($nextInfoQuery);

    $time = (int)$tempOut[3] - (int)$tempIn[3]; // Lasketaan uloskirjauksen ja sisäänkirjauksen erotus
    $monthtime[$month] += $time;
    $weektime[$week] += $time;

  } else {
    break;
  }
}

echo '<div class="ownReportsBox">
        <h2> '.$displayname.' työtunnit </h2>
        <center><p style="color: grey; margin: 0;"> Vuosi '. date('Y', $timeNow).'</p></center>
        <p> Työaikasi tällä viikolla: <b>' .convertToHours($weektime[date('W', $timeNow)]). '</b> </p>';


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

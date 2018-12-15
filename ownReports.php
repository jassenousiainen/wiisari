<?php
session_start();

require 'common.php';
include 'header.php';
include 'topmain.php';

echo "<title>Omat Tunnit</title>\n";


function convertToHours($tmstp) {
    return gmdate('H:i', $tmstp);
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
QUERY
);

while ( $tempOut = mysqli_fetch_array($infoQuery) ) {   // Käydään läpi työntekijän kaikki kirjaukset
  if ( date('Y', $tempOut[3]) == date('Y', $timeNow) ) { // Lasketaan vain tämän vuoden kirjaukset
    $newid = $tempOut[0];
    $month = date('m', $tempOut[3]); // 1-12
    $week = date('W', $tempOut[3]); // 1-52
    $day = date('d', $tempOut[3]); // 1-31

    $nextInfoQuery = tc_query( "SELECT * FROM info WHERE fullname = '$fullname' AND newid < '$newid'"); // Haetaan seuraava kirjaus (eli sisäänkirjaus)
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
        <p> Työaikasi tällä viikolla: ' .convertToHours($weektime[date('W', $timeNow)]). ' </p>';


  echo 'Tammikuu:<b> ' .convertToHours((int)$monthtime[1]). '</b><br>';
  echo 'Helmikuu:<b> ' .convertToHours((int)$monthtime[2]). '</b><br>';
  echo 'Maaliskuu:<b> ' .convertToHours((int)$monthtime[3]). '</b><br>';
  echo 'Huhtikuu:<b> ' .convertToHours((int)$monthtime[4]). '</b><br>';
  echo 'Toukokuu:<b> ' .convertToHours((int)$monthtime[5]). '</b><br>';
  echo 'Kesäkuu:<b> ' .convertToHours((int)$monthtime[6]). '</b><br>';
  echo 'Heinäkuu:<b> ' .convertToHours((int)$monthtime[7]). '</b><br>';
  echo 'Elokuu:<b> ' .convertToHours((int)$monthtime[8]). '</b><br>';
  echo 'Syyskuu:<b> ' .convertToHours((int)$monthtime[9]). '</b><br>';
  echo 'Lokakuu:<b> ' .convertToHours((int)$monthtime[10]). '</b><br>';
  echo 'Marraskuu:<b> ' .convertToHours((int)$monthtime[11]). '</b><br>';
  echo 'Joulukuu:<b> ' .convertToHours((int)$monthtime[12]). '</b><br>';


echo '</div>';
?>

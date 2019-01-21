<?php

error_reporting(0);
ini_set('display_errors', 0);

require 'common.php';

echo "<head>
        <title>Sisään/Ulos</title>
        <meta http-equiv='Content-Type' content=t'ext/html; charset=UTF-8'/>
        <meta http-equiv='refresh' content='3; URL=timeclock.php'>
        <link rel='stylesheet' type='text/css' media='screen' href='css/default.css' />\n
      </head>";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];



// signin/signout data passed over from timeclock.php //
@$username = $_POST['username'];
@$notes = $_POST['notes'];

$fullname = tc_select_value("empfullname", "employees", "empfullname = ?", $username);
$displayname = tc_select_value("displayname", "employees", "empfullname = ?", $username);

if (!has_value($fullname)) {
  echo "<h3 style='color:red;'>Antamallasi käyttäjätunnuksella ei löytynyt ketään.</h3>";
  exit;
}


// Choose whether employee logs in or out based on previous inout log
$currently_inout = mysqli_fetch_row(tc_query( "SELECT `inout` FROM info WHERE fullname = '$fullname' ORDER BY timestamp DESC"))[0];

if (has_value($currently_inout)) {
  if ($currently_inout == 'in') { $inout = 'out'; }
  elseif ($currently_inout == 'out') { $inout = 'in'; }
  else {
    echo "<h3 style='color:red;'>Virhe! Jokin meni pieleen :(</h3>";
    exit;
  }
} else {
  $inout = 'out';
}


// Insert inout data to info -table (and employees -table)
$tz_stamp = time();
$clockin = array("fullname" => $fullname, "inout" => $inout, "timestamp" => $tz_stamp, "notes" => "$notes");
tc_insert_strings("info", $clockin);
tc_update_strings("employees", array("tstamp" => $tz_stamp), "empfullname = ?", $fullname);


// Format timestamp to readable form
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


$infoQuery = tc_query("SELECT * FROM info WHERE fullname = '$fullname' AND `inout` = 'out' ORDER BY timestamp DESC");
$nextInfoQuery = tc_query( "SELECT * FROM info WHERE fullname = '$fullname' AND `inout` = 'in' ORDER BY timestamp DESC");


echo "<div class='flexBox'>";

if ($inout == "out") {
  $tempOut = mysqli_fetch_array($infoQuery);
  $tempstamp = $tempOut[3];
  $tempIn = mysqli_fetch_array($nextInfoQuery);
  $time = (int)$tempOut[3] - (int)$tempIn[3];

  $inout = "<p class='logOutTime'>".convertToHours($time). "</p> <p class='kirjausUlos'>Ulos</p>";
  echo "<div class='borderBox borderOut'>";
}
else if ($inout == "in") {
  $inout = "<p class='kirjausSisaan'>Sisään</p>";
  echo "<div class='borderBox borderIn'>";
}


$logTime = new DateTime("@$tz_stamp");
$logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

echo "<div class='kirjausLaatikko'>";
echo "<h2 class='kirjausNimi'>$displayname</h2>";
echo '<br>';
echo '<p class="kirjausAika">Kello: <b>';
echo $logTime->format("H:i");
echo '</b></p>';
echo '<p class="kirjausPaiva">Päivä: <b>';
echo $logTime->format("d.m.Y");
echo '</b></p>';
echo '<br>';
echo $inout;
if ( $notes != '' ) {
  echo '<div class="inout_notes"><h3>Viesti:</h3><p>';
  echo htmlspecialchars($notes);
  echo '</p></div>';
}
echo '<p>Sivu siirtyy automaattisesti etusivulle</p>';
echo "</div></div></div>";

?>

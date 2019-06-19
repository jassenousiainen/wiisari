<?php
require 'common.php';
pdo_connect();  //Connect to database using PDO

echo "<head>
        <title>Sisään/Ulos</title>
        <meta http-equiv='Content-Type' content=t'ext/html; charset=UTF-8'/>";
        if (isset($_POST['mypage'])) { echo "<meta http-equiv='refresh' content='3; URL=mypage.php'>"; }
        else { echo "<meta http-equiv='refresh' content='3; URL=timeclock.php'>"; }
echo "  <link rel='stylesheet' type='text/css' media='screen' href='css/default.css' />\n
      </head>";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];


if (isset($_POST['notes'])) {
  $notes = htmlspecialchars($_POST['notes']);
} else {
  $notes = '';
}

// SQL-injection-proof query
$getuser_stmt = $pdo->prepare("SELECT userID, displayName, earliestStart, latestEnd FROM employees WHERE userID = ?");
$getuser_stmt->execute(array($_POST['userID']));
$row = $getuser_stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
   $userID = $row['userID'];
   $displayName = $row['displayName'];
   $earliestStart = $row['earliestStart'];
   $latestEnd = $row['latestEnd'];
}
else {
  echo "<h3 style='color:red;'>Antamallasi käyttäjätunnuksella ei löytynyt ketään.</h3>";
  exit;
}


$tz_stamp = time();
$tz_clock = new DateTime("@$tz_stamp");
$tz_clock->setTimeZone(new DateTimeZone('Europe/Helsinki'));


// Choose whether employee logs in or out based on previous inout log
$inoutData = mysqli_fetch_array(tc_query( "SELECT * FROM info WHERE userID = '$userID' ORDER BY timestamp DESC"));

if (!empty($inoutData)) {
  if ($inoutData['inout'] == 'in' && $inoutData['timestamp'] > $tz_stamp) { $inout = 'error'; }
  else if ($inoutData['inout'] == 'in') { $inout = 'out'; }
  else if ($inoutData['inout'] == 'out') { $inout = 'in'; }
  else {
    echo "<h3 style='color:red;'>Virhe! Jokin meni pieleen :(</h3>";
    exit;
  }
  $last_stamp = $inoutData['timestamp'];
  $last_clock = new DateTime("@$last_stamp");
  $last_clock->setTimeZone(new DateTimeZone('Europe/Helsinki'));
} else {
  $inout = 'in';
}


if ($earliestStart != null && $latestEnd != null) {
  if ($inout == 'in' && $tz_clock->format('H:i:s') < $earliestStart) {
    $inout = 'early';
    $notes = "Tuli liian aikaisin, todellinen tuloaika: " . $tz_clock->format('d.m.Y H:i');
    $tzDateStr = $tz_clock->format('Y-m-d')." ".$earliestStart;
    $tz_stamp = \DateTime::createFromFormat('Y-m-d H:i:s', $tzDateStr)->getTimestamp();
  } 
  else if ($inout == 'in' && $tz_clock->format('H:i:s') > $latestEnd) {
    $inout = 'afterhours';
  }
  else if ($inout == 'out') {
    if ($tz_clock->format('Y-m-d') != $last_clock->format('Y-m-d') || $tz_clock->format('H:i:s') > $latestEnd) {
      $inout = 'late';
      $notes = "Lähti liian myöhään, todellinen lähtöaika: " . $tz_clock->format('d.m.Y H:i');
      $tzDateStr = $last_clock->format('Y-m-d')." ".$latestEnd;
      $tz_stamp = \DateTime::createFromFormat('Y-m-d H:i:s', $tzDateStr)->getTimestamp();
    }
  }
}


// Insert data to the DB
if ($inout != 'afterhours' && $inout != 'error') {
  $clockin_stmt = $pdo->prepare("INSERT INTO `info` (`userID`, `inout`, `timestamp`, `notes`) VALUES (?,?,?,?)");

  if ($inout == 'early') { 
    $clockin_stmt->execute(array($userID, "in", $tz_stamp, $notes));
    tc_update_strings("employees", array("inoutStatus" => "in"), "userID = ?", $userID);
  }
  else if ($inout == 'late') { 
    $clockin_stmt->execute(array($userID, "out", $tz_stamp, $notes));
    tc_update_strings("employees", array("inoutStatus" => "out"), "userID = ?", $userID);
  }
  else { 
    $clockin_stmt->execute(array($userID, $inout, $tz_stamp, $notes));
    tc_update_strings("employees", array("inoutStatus" => $inout), "userID = ?", $userID);
  }

  
}


// The actual html that is shown to employee.
echo '<section class="top-skew-bg blue">
<div class="elipsed-border">
</div>
</section>';

echo "
<section class='container full-width'>";

if ($inout == "out") {
  // Lookup previous login, so we can count time between login and current logout
  $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE userID = '$userID' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
  $currentWorkTime = $tz_stamp - (int)$lastIn;
  $logblock = "<p class='logOutTime'>".convertToHours($currentWorkTime). "</p> <p class='kirjausUlos'>Ulos</p>";
}
else if ($inout == "in") {
  $logblock = "<p class='kirjausSisaan'>Sisään</p>";
}
else if ($inout == 'early') {
  $logblock = "<p class='logError'>Aikaisin</p>
            <br>
            <p class='kirjausSisaan'>Sisään</p>";
}
else if ($inout == 'afterhours') {
  $logblock = "<p class='logError'>Et voi tulla työpäivän jälkeen!</p>";
}
else if ($inout == 'late') {
  $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE userID = '$userID' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
  $currentWorkTime = $tz_stamp - (int)$lastIn;
  $logblock = "<p class='logError'>Myöhään</p>
              <p class='logOutTime'>".convertToHours($currentWorkTime). "</p> <p class='kirjausUlos'>Ulos</p>";
}
else if ($inout == 'error') {
  $logblock = "<p class='logError'>Virhe! Voit kellottautua ulos klo " . $last_clock->format('H:i') . " jälkeen</p>";
}


$logTime = new DateTime("@$tz_stamp");
$logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

echo "<div class='kirjausLaatikko'>";
echo "<h2 class='kirjausNimi'>$displayName</h2>";
echo '<br>';
echo '<p class="kirjausAika">Kello: <b>';
echo $logTime->format("H:i");
echo '</b></p>';
echo '<p class="kirjausPaiva">Päivä: <b>';
echo $logTime->format("d.m.Y");
echo '</b></p>';
echo '<br>';
echo $logblock;
if ( $notes != '' ) {
  echo '<div class="inout_notes"><h3>Viesti:</h3><p>';
  echo $notes;
  echo '</p></div>';
}
echo '<p>Sivu siirtyy automaattisesti etusivulle</p>';
echo "</div>
</section>";

?>

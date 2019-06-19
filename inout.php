<?php
require 'common.php';
pdo_connect();  //Connect to database using PDO

echo "<head>
        <title>Sisään/Ulos</title>
        <meta http-equiv='Content-Type' content=t'ext/html; charset=UTF-8'/>";
        if (isset($_POST['mypage'])) { echo "<meta http-equiv='refresh' content='2; URL=mypage.php'>"; }
        else { echo "<meta http-equiv='refresh' content='2; URL=timeclock.php'>"; }
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
$getuser_stmt = $pdo->prepare("SELECT userID, displayName FROM employees WHERE userID = ?");
$getuser_stmt->execute(array($_POST['userID']));
$row = $getuser_stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
   $userID = $row['userID'];
   $displayName = $row['displayName'];
}
else {
  echo "<h3 style='color:red;'>Antamallasi käyttäjätunnuksella ei löytynyt ketään.</h3>";
  exit;
}

// Choose whether employee logs in or out based on previous inout log
$currently_inout = mysqli_fetch_row(tc_query( "SELECT `inout` FROM info WHERE userID = '$userID' ORDER BY timestamp DESC"))[0];

if (has_value($currently_inout)) {
  if ($currently_inout == 'in') { $inout = 'out'; }
  elseif ($currently_inout == 'out') { $inout = 'in'; }
  else {
    echo "<h3 style='color:red;'>Virhe! Jokin meni pieleen :(</h3>";
    exit;
  }
} else {
  $inout = 'in';
}


// Insert inout data to info -table (and employees -table)
$tz_stamp = time();

$clockin_stmt = $pdo->prepare("INSERT INTO `info` (`userID`, `inout`, `timestamp`, `notes`) VALUES (?,?,?,?)");
$clockin_stmt->execute(array($userID, $inout, $tz_stamp, $notes));

tc_update_strings("employees", array("inoutStatus" => $inout), "userID = ?", $userID);



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

  $inout = "<p class='logOutTime'>".convertToHours($currentWorkTime). "</p> <p class='kirjausUlos'>Ulos</p>";
}
else if ($inout == "in") {
  $inout = "<p class='kirjausSisaan'>Sisään</p>";
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
echo $inout;
if ( $notes != '' ) {
  echo '<div class="inout_notes"><h3>Viesti:</h3><p>';
  echo $notes;
  echo '</p></div>';
}
echo '<p>Sivu siirtyy automaattisesti etusivulle</p>';
echo "</div>
</section>";

?>

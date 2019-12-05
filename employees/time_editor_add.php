<?php    /* This is companion for time_editor.php */
echo '<table>';

// Punch-in
if ($_POST['in_date'] != "") {
  $in_errors = array();
  $inDateStr = $_POST['in_date']." ".$_POST['in_time'];
  $notes = "";

  // Check input for errors
  if (empty($_POST['in_time'])) {
    array_push($in_errors, "Virhe! Täytit sisääntulopvm, mutta kellonaika oli tyhjä");
  } else {
    $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $inDateStr, new DateTimeZone($timezone))->getTimestamp();
    if ($timestamp > time()) {
      array_push($in_errors, "Virhe! Et voi lisätä kellotuksia tulevaisuuteen");
    }
  }

  // Print errors
  if (sizeof($in_errors) > 0) {
    foreach ($in_errors as &$in_error) {
      echo '<div class="box" style="background-color: var(--red); min-height: 50px; text-align: center; color: white; padding: 0;">';
      echo "<p>$in_error</p>";
      echo '</div>';
    }
  } else {  // Insert to database if there were no errors
    $clockin = array("userID" => $userID, "inout" => 'in', "timestamp" => $timestamp, "notes" => "$notes");
    $punchid = tc_insert_strings("info", $clockin);   // while inserting data to db get the autoincrement value to variable

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE punchID = '$punchid'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone($timezone));
    echo "<tr><td colspan='3' style='color: var(--gray3);'>Sisäänkellotus lisätty:</td></tr>
          <tr style='background-color: var(--light);'>
            <td><span class='inout' style='background-color:var(--lightgreen); text-align: center;'>$success[2]</span></td>
            <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
            <td style='text-align:center;'>".$logTime->format("H:i")."</td>
          </tr>";
  }
}

// Punch-out
if ($_POST['out_date'] != "") {
  $out_errors = array();
  $outDateStr = $_POST['out_date']." ".$_POST['out_time'];
  $notes = "";

  // Check input for errors
  if (empty($_POST['out_time'])) {
    array_push($out_errors, "Virhe! Täytit uloslähtöpvm, mutta kellonaika oli tyhjä");
  } else {
    $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $outDateStr, new DateTimeZone($timezone))->getTimestamp();
    if ($timestamp > time()) {
      array_push($out_errors, "Virhe! Et voi lisätä kellotuksia tulevaisuuteen");
    }
  }

  // Print errors
  if (sizeof($out_errors) > 0) {
    foreach ($out_errors as &$out_error) {
      echo '<div class="box" style="background-color: var(--red); min-height: 50px; text-align: center; color: white; padding: 0;">';
      echo "<p>$out_error</p>";
      echo '</div>';
    }
  } else {  // Insert to database if there were no errors
    $clockin = array("userID" => $userID, "inout" => 'out', "timestamp" => $timestamp, "notes" => "$notes");
    $punchid = tc_insert_strings("info", $clockin);   // while inserting data to db get the autoincrement value to variable

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE punchID = '$punchid'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone($timezone));
    echo "<tr><td colspan='3' style='color: var(--gray3);'>Uloskellotus lisätty:</td></tr>
          <tr style='background-color: var(--light);'>
            <td><span class='inout' style='background-color:var(--red); text-align: center;'>$success[2]</span></td>
            <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
            <td style='text-align:center;'>".$logTime->format("H:i")."</td>
          </tr>";
  }
}


echo '</table>';

// Update inout_status to match last log
$inout = mysqli_fetch_row(tc_query( "SELECT * FROM info WHERE userID = '$userID' ORDER BY timestamp DESC"));
if ($inout[2] == 'in' || $inout[2] == 'out') {
  tc_update_strings("employees", array("inoutStatus" => $inout[2]), "userID = ?", $userID);
} else {
  tc_update_strings("employees", array("inoutStatus" => 'out'), "userID = ?", $userID);
}
?>

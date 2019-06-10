<?php    /* This is companion for time_editor.php */
echo '<table>';
if ($_POST['in_date'] != "") {
  $inDateStr = $_POST['in_date']." ".$_POST['in_time'];
  $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $inDateStr)->getTimestamp();
  $notes = "";

  if ($timestamp > time()) {
    echo 'Virhe! Et voi lisätä kellotuksia tulevaisuuteen';
  } 
  else {
    $clockin = array("userID" => $userID, "inout" => 'in', "timestamp" => $timestamp, "notes" => "$notes");
    tc_insert_strings("info", $clockin);

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE timestamp = '$timestamp'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));
    echo "<tr style='background-color: var(--light);'>
            <td><span class='inout' style='background-color:var(--lightgreen); text-align: center;'>$success[2]</span></td>
            <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
            <td style='text-align:center;'>".$logTime->format("H:i")."</td>
          </tr>";
  }
}
if ($_POST['out_date'] != "") {
  $outDateStr = $_POST['out_date']." ".$_POST['out_time'];
  $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $outDateStr)->getTimestamp();
  $notes = "";

  if ($timestamp > time()) {
    echo 'Virhe! Et voi lisätä kellotuksia tulevaisuuteen';
  }
  else {
    $clockin = array("userID" => $userID, "inout" => 'out', "timestamp" => $timestamp, "notes" => "$notes");
    tc_insert_strings("info", $clockin);

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE timestamp = '$timestamp'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));
    echo "<tr style='background-color: var(--light);'>
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

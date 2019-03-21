<?php    /* This is companion for time_editor.php */
echo '<table>';
if ($_POST['in_date'] != "") {
  $inDateStr = $_POST['in_date']." ".$_POST['in_time'];
  $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $inDateStr)->getTimestamp();
  $notes = "";

  $clockin = array("fullname" => $empfullname, "inout" => 'in', "timestamp" => $timestamp, "notes" => "$notes");
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
if ($_POST['out_date'] != "") {
  $outDateStr = $_POST['out_date']." ".$_POST['out_time'];
  $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $outDateStr)->getTimestamp();
  $notes = "";

  $clockin = array("fullname" => $empfullname, "inout" => 'out', "timestamp" => $timestamp, "notes" => "$notes");
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
echo '</table>';

// Update inout_status to match last log
$inout = mysqli_fetch_row(tc_query( "SELECT * FROM info WHERE fullname = '$empfullname' ORDER BY timestamp DESC"));
if ($inout[2] == 'in' || $inout[2] == 'out') {
  tc_update_strings("employees", array("inout_status" => $inout[2]), "empfullname = ?", $empfullname);
  tc_update_strings("employees", array("tstamp" => $inout[3]), "empfullname = ?", $empfullname);
} else {
  tc_update_strings("employees", array("inout_status" => 'out'), "empfullname = ?", $empfullname);
  tc_update_strings("employees", array("tstamp" => null), "empfullname = ?", $empfullname);
}
?>

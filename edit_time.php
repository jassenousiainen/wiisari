<?php
include 'header.php';
session_start();
include 'topmain.php';

echo "<title>Kellotuseditori</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->time_admin == 0) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if ($request == 'GET') {
  echo "<script type='text/javascript' language='javascript'> window.location.href = '/time_editor.php';</script>";
  exit;
}

$empfullname;

echo '<section class="container">
        <div class="mainBox">
          <a class="btn back" href="/time_editor.php"> Takaisin</a>
          <div>';


if(!empty($_POST['deletelist'])) {
  $empfullname = $_POST['deletetime'];
  echo '<h2>Kellotuseditori - poista kirjauksia</h2>
          <div class="section">
            <table style="width:50%;">';

  foreach($_POST['deletelist'] as $del) {

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE newid = '$del'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

    tc_delete('info', 'newid = ?', $del);

    echo "<tr style='background-color: var(--light);'>
            <td><span class='inout' style='background-color:var(--blue); text-align: center;'>$success[2]</span></td>
            <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
            <td style='text-align:center;'>".$logTime->format("H:i")."</td>
          </tr>";
  }

  echo '</table>';
  echo '<p>Kirjaukset poistettiin onnistuneesti</p>
        </div>';
}


else if (isset($_POST['newtime'])) {
  $empfullname = $_POST['newtime'];
  $displayname = tc_select_value("displayname", "employees", "empfullname = ?", $empfullname);

  echo '<h2>Kellotuseditori - Lisää kirjauksia ('.$displayname.')</h2>
          <div class="section">
            <table style="width:50%;">';

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
  echo '<p>Kirjaukset lisätty onnistuneesti</p>';
  echo '</div>';
}


echo '</div></div></section>';


// Update inout_status to match last log
$inout = mysqli_fetch_row(tc_query( "SELECT `inout` FROM info WHERE fullname = '$empfullname' ORDER BY timestamp DESC"))[0];
tc_update_strings("employees", array("inout_status" => $inout), "empfullname = ?", $empfullname);

?>

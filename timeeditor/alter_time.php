<?php        /* This is companion for time_editor.php */

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";


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
        <div class="middleContent">';

/* ----- Punch deletion ----- */
if(isset($_POST['deletetime']) && !empty($_POST['deletelist'])) {
  $empfullname = $_POST['deletetime'];

  echo '  <form action="time_editor.php" method="post" style="margin:0;">
            <button class="btn back" type="submit" name="edittime" value="'.$empfullname.'"> Takaisin</button>
          </form>
          <div class="box">
            <h2>Kellotuseditori - poista kirjauksia</h2>
            <div class="section">
              <table style="width:50%;">';

  foreach($_POST['deletelist'] as $del) {

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE newid = '$del'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

    tc_delete('info', 'newid = ?', $del);

    echo "      <tr style='background-color: var(--light);'>
                  <td><span class='inout' style='background-color:var(--blue); text-align: center;'>$success[2]</span></td>
                  <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
                  <td style='text-align:center;'>".$logTime->format("H:i")."</td>
                </tr>";
  }

  echo '      </table>';
  echo '      <p>Kirjaukset poistettiin onnistuneesti</p>
            </div>
          </div>';
}

/* ----- Punch editing ----- */
else if (isset($_POST['altertime'])) {

  $punchid = $_POST['altertime'];
  $punch = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE newid = '$punchid'"));
  $empfullname = $punch[1];
  $logTime = new DateTime("@$punch[3]");
  $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

  echo '  <form action="time_editor.php" method="post" style="margin:0;">
            <button class="btn back" type="submit" name="edittime" value="'.$empfullname.'"> Takaisin</button>
          </form>';

  echo '  <div class="box">
            <h2>Kellotuseditori - muokkaa kirjausta</h2>
            <div class="section">
              <p>Käyttäjä: '.$empfullname.'</p>
              <p>KellotusID: '.$punch[0].'</p>
              <form action="alter_time.php" method="post">
              <input name="punchid" value="'.$punch[0].'" style="display:none;">
                <table style="max-width: 400px;">
                  <tr>
                    <td>'.$punch[2].'</td>
                    <td><i class="fas fa-long-arrow-alt-right"></i></td> 
                    <td><select name="inout">';
                  if ($punch[2] == "in") {
                    echo '<option selected="selected" value="in">in</option>
                    <option value="out">out</option>';
                  } else {
                    echo '<option selected="selected" value="out">out</option>
                    <option value="in">in</option>';
                  }
  echo '              </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.$logTime->format("d.m.Y").'</td>
                    <td><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td><input type="text" id="from" autocomplete="off" size="10" maxlength="10" name="date" value="'.$logTime->format("d.m.Y").'"></td>
                  </tr>
                  <tr>
                    <td>'.$logTime->format("H:i").'</td>
                    <td><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td><input type="time" name="time" value="'.$logTime->format("H:i").'"></td>
                  </tr>
                  <tr>
                    <td>"'.$punch[4].'"</td>
                    <td><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td><input type="text" name="notes" value="'.$punch[4].'"></td>
                  </tr>
                </table>
                <br>
                <button type="submit" class="btn">Lähetä</button>
              </form>
            </div>
          </div>';
}

/* ----- Punch updating ----- */
else if (isset($_POST['punchid'])) {
  $punchid = $_POST['punchid'];
  $oldpunch = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE newid = '$punchid'"));
  $empfullname = $oldpunch[1];
  $oldlogTime = new DateTime("@$oldpunch[3]");
  $oldlogTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

  $inout = $_POST['inout'];
  $inDateStr = $_POST['date']." ".$_POST['time'];
  $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $inDateStr)->getTimestamp();
  $notes = $_POST['notes'];

  echo '  <form action="time_editor.php" method="post" style="margin:0;">
            <button class="btn back" type="submit" name="edittime" value="'.$empfullname.'"> Takaisin</button>
          </form>';


  echo '  <div class="box">
            <h2>Kellotuseditori - muutoksia tehty</h2>
            <div class="section">
              <p>Käyttäjä: '.$empfullname.'</p>
              <p>KellotusID: '.$punchid.'</p>';

  if ($timestamp > time()) {
    echo '    <p>Virhe! Et voi sijoittaa kellotusta tulevaisuuteen</p>';
  } else {
    echo '    <p>Muutokset tehty onnistuneesti: </p>
              <table>
                <tr>
                  <td>'.$oldpunch[2].'</td>
                  <td><i class="fas fa-long-arrow-alt-right"></i></td>
                  <td>'.$inout.'</td>
                  <td colspan="2"></td>
                </tr>
                <tr>
                  <td>'.$oldlogTime->format("d.m.Y").'</td>
                  <td><i class="fas fa-long-arrow-alt-right"></i></td>
                  <td>'.$_POST['date'].'</td>
                </tr>
                <tr>
                  <td>'.$oldlogTime->format("H:i").'</td>
                  <td><i class="fas fa-long-arrow-alt-right"></i></td>
                  <td>'.$_POST['time'].'</td>
                </tr>
                <tr>
                  <td>"'.$oldpunch[4].'"</td>
                  <td><i class="fas fa-long-arrow-alt-right"></i></td>
                  <td>'.$notes.'</td>
                </tr>
              </table>';

    tc_update_strings(
      "info",
      array(
        "inout"     => $inout,
        "timestamp" => $timestamp,
        "notes"     => $notes
      ),
      "newid = ?", $punchid
    );

  }
  echo '    </div>
          </div>';
}

echo '  </div>
      </section>';


// Update tstamp and inout_status in table employees to match last punch
$inout = mysqli_fetch_row(tc_query( "SELECT * FROM info WHERE fullname = '$empfullname' ORDER BY timestamp DESC"));
if ($inout[2] == 'in' || $inout[2] == 'out') {
  tc_update_strings("employees", array("inout_status" => $inout[2]), "empfullname = ?", $empfullname);
  tc_update_strings("employees", array("tstamp" => $inout[3]), "empfullname = ?", $empfullname);
} else {
  tc_update_strings("employees", array("inout_status" => 'out'), "empfullname = ?", $empfullname);
  tc_update_strings("employees", array("tstamp" => null), "empfullname = ?", $empfullname);
}


?>
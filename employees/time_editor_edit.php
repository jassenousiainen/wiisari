<?php        /* This is companion for time_editor.php */

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";


echo "<title>Kellotuseditori</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];


if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


if ($request == 'GET') {
  echo "<script type='text/javascript' language='javascript'> window.location.href = '/time_editor.php';</script>";
  exit;
}


echo '<section class="container">
        <div class="middleContent">';

/* ----- Punch deletion ----- */
if(isset($_POST['deletetime']) && !empty($_POST['deletelist'])) {
  $userID = $_POST['deletetime'];
  $checkPermsID = $userID;


  require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";     // This blocks access to rest of the page if supervisor doesn't have access to this groups employee
  
  echo '  <form action="time_editor.php" method="post" style="margin:0;">
            <button class="btn back" type="submit" name="timeeditor" value="'.$userID.'">Takaisin</button>
          </form>
          <div class="box">
            <h2>Kellotuseditori - poista kirjauksia</h2>
            <div class="section">
              <table style="width:50%;">';

  foreach($_POST['deletelist'] as $del) {

    $success = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE punchID = '$del'"));
    $logTime = new DateTime("@$success[3]");
    $logTime->setTimeZone(new DateTimeZone($timezone));

    tc_delete('info', 'punchID = ?', $del);

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

/* ----- Punch edit form ----- */
else if (isset($_POST['altertime'])) {

  $punchID = $_POST['altertime'];
  $punch = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE punchID = '$punchID'"));
  $userID = $punch[1];
  $logTime = new DateTime("@$punch[3]");
  $logTime->setTimeZone(new DateTimeZone($timezone));

  $checkPermsID = $userID;
  require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";     // This blocks access to rest of the page if supervisor doesn't have access to this groups employee

  echo '  <form action="time_editor.php" method="post" style="margin:0;">
            <button class="btn back" type="submit" name="timeeditor" value="'.$userID.'">Takaisin</button>
          </form>';

  echo '  <div class="box">
            <h2>Kellotuseditori - muokkaa kirjausta</h2>
            <div class="section">
              <p>Käyttäjä: '.$userID.'</p>
              <p>KellotusID: '.$punch[0].'</p>
              <form action="'.$self.'" method="post">
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
  $punchID = $_POST['punchid'];
  $oldpunch = mysqli_fetch_row(tc_query("SELECT * FROM info WHERE punchID = '$punchID'"));
  $userID = $oldpunch[1];
  $oldlogTime = new DateTime("@$oldpunch[3]");
  $oldlogTime->setTimeZone(new DateTimeZone($timezone));

  $inout = $_POST['inout'];
  $inDateStr = $_POST['date']." ".$_POST['time'];
  $timestamp=\DateTime::createFromFormat('d.m.Y H:i', $inDateStr, new DateTimeZone($timezone))->getTimestamp();
  $notes = $_POST['notes'];

  echo '  <form action="time_editor.php" method="post" style="margin:0;">
            <button class="btn back" type="submit" name="timeeditor" value="'.$userID.'">Takaisin</button>
          </form>';


  echo '  <div class="box">
            <h2>Kellotuseditori - muutoksia tehty</h2>
            <div class="section">
              <p>Käyttäjä: '.$userID.'</p>
              <p>KellotusID: '.$punchID.'</p>';

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
      "punchID = ?", $punchID
    );

  }
  echo '    </div>
          </div>';
}

echo '  </div>
      </section>';


// Update tstamp and inout_status in table employees to match last punch
$inout = mysqli_fetch_row(tc_query( "SELECT * FROM info WHERE userID = '$userID' ORDER BY timestamp DESC"));
if ($inout[2] == 'in' || $inout[2] == 'out') {
  tc_update_strings("employees", array("inoutStatus" => $inout[2]), "userID = ?", $userID);
} else {
  tc_update_strings("employees", array("inoutStatus" => 'out'), "userID = ?", $userID);
}


?>
<?php
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
  $employee_query = tc_query("SELECT * FROM employees WHERE disabled = 0 ORDER BY displayname DESC");

echo '<section class="container">
        <div class="middleContent">
          <div class="box">
            <h2>Kellotuseditori - valitse työntekijä</h2>
            <div class="section">
            <form action="'.$self.'" method="post">
              <table>
              <thead>
                <tr>
                  <th data-placeholder="Hae nimellä">Nimi</th>
                  <th class="filter-select filter-exact" data-placeholder="Kaikki toimistot">Toimisto</th>
                  <th class="filter-select filter-exact" data-placeholder="Kaikki osastot">Osasto</th>
                  <th class="sorter-false filter-false">Valitse</th>
                </tr>
              </thead>
              <tfoot>
              <tr style="height: 20px;"></tr>
                <tr class="tablesorter-ignoreRow">
                  <th colspan="4" class="ts-pager form-horizontal">
                    <button type="button" class="btn first"><i class="fas fa-angle-double-left"></i></button>
                    <button type="button" class="btn prev"><i class="fas fa-angle-left"></i></button>
                    <span class="pagedisplay"></span>
                    <!-- this can be any element, including an input -->
                    <button type="button" class="btn next"><i class="fas fa-angle-right"></i></button>
                    <button type="button" class="btn last"><i class="fas fa-angle-double-right"></i></button>
                  </th>
                </tr>
                <tr class="tablesorter-ignoreRow">
                  <th colspan="4" class="ts-pager form-horizontal">
                    max rivit: <select class="pagesize browser-default" title="Select page size">
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option selected="selected" value="30">30</option>
                      <option value="40">40</option>
                      <option value="all">Kaikki rivit</option>
                    </select>
                    sivu: <select class="pagenum browser-default" title="Select page number"></select>
                  </th>
                </tr>
              </tfoot>
              <tbody>';

  while ( $employee = mysqli_fetch_array($employee_query) ) {

echo '          <tr>
                  <td>'.$employee[3].'</td>
                  <td>'.$employee[7].'</td>
                  <td>'.$employee[6].'</td>
                  <td style="text-align:center;"><button name="edittime" type="submit" class="btn" value="'.$employee[0].'">Valitse</button></td>
                </tr>';
  }
echo '          </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
      </section>';

echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
}

else if  ( isset($_POST['edittime']) ) {

  $empfullname = $_POST['edittime'];
  $user_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE empfullname = '$empfullname'"));

//  echo '<a class="btn back" href="/time_editor.php"> Takaisin</a>';
  echo '<section class="container">';
  echo '  <div class="leftContent">
          <div class="box">
            <h2>Lisää aika</h2>
            <div class="section">
              <form action="'.$self.'" method="post">
                <p>Täytä toinen tai molemmat:</p>
                <b>Sisään:</b>
                <br>
                <input type="text" id="from" autocomplete="off" size="10" maxlength="10" name="in_date" placeholder="pvm">
                <input name="in_time" type="time">
                <br><br>
                <b>Ulos:</b>
                <br>
                <input type="text" id="to" autocomplete="off" size="10" maxlength="10" name="out_date" placeholder="pvm">
                <input name="out_time" type="time">
                <br><br>
                <input type="text" name="emp" value="'.$empfullname.'" style="display:none;">
                <button type="submit" name="edittime" class="btn">Lisää</button>
              </form>
            </div>';
  if (isset($_POST['in_date']) || isset($_POST['out_date'])) {
    echo '  <div class="section">
              <p>Nämä lisättiin onnistuneesti:</p>';
    include 'addtime.php';
    echo '  </div>';
  }
  echo '  </div></div>';

  echo'    <div class="middleContent">
            <div class="box">
              <h2>Kellotuseditori - '.$user_data[3].'</h2>
              <div class="section">
              <form action="alter_time.php" method="post">
                Oransilla merkityt kirjaukset ilmaisevat virheestä.
                <button type="submit" name="deletetime" value="'.$empfullname.'" class="btn del" style="float:right; margin-bottom: 10px;">Poista valitut</button>
                <table>
                  <tr>
                    <th>Sisään/Ulos</th>
                    <th>Päivä</th>
                    <th>Kello</th>
                    <th colspan="3" >Viesti</th>
                    <th>Poista</th>
                    <th>Muokkaa</th>
                  </tr>';
  $max = 0;
  $prev = '';
  $inoutdata_query = tc_query("SELECT * FROM info WHERE fullname = '$empfullname' ORDER BY timestamp DESC");
  while ( $inout = mysqli_fetch_row($inoutdata_query) ) {

    $logTime = new DateTime("@$inout[3]");
    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

    if ( $prev == '' || ($prev == 'out' && $inout[2] == 'in') || ($prev == 'in' && $inout[2] == 'out')) {
      if ($inout[2] == 'in') {
        echo "<tr style='background-color: white;'>
                <td style='text-align:center;'><span class='inout' style='background-color:var(--lightgreen); border-radius: 0 0 10px 10px; margin-top: -6px;'>$inout[2]</span></td>";
      } else {
        echo "<tr style='background-color: var(--light);'>
                <td style='text-align:center;'><span class='inout' style='background-color:var(--red); border-radius: 10px 10px 0 0; margin-bottom: -6px;'>$inout[2]</span></td>"; }
    }
    else {
      echo "<tr style='background-color: var(--orange);'>
              <td style='text-align:center;'><span>$inout[2]</span></td>"; }

      echo "  <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
              <td style='text-align:center;'>".$logTime->format("H:i")."</td>
              <td colspan='3' style='word-wrap: break-word;'>$inout[4]</td>
              <td style='text-align:center;'>
                <label class='container'>
                  <input type='checkbox' name='deletelist[]' value='$inout[0]' class='check'>
                  <span class='checkmark'></span>
                </label>
              </td>
              <td style='text-align:center;'><button type='submit' name='edit' value='$inout[0]' class='btn'><i class='fas fa-pencil-alt'></i></button></td>
            </tr>";
    $prev = $inout[2];
    $max++;
    if ($max == 200) { break; }
  }
echo '          </table>
              </form>
              (näyttää viimeiset 200 kirjausta, mikäli vanhempia tarvitsee muokata tulee tämä tehdä phpMyAdminissa)
            </div>
          </div>
        </div>
      </section>';
}

?>

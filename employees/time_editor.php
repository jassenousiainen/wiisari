<?php
include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Kellotuseditori</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/employees/employees.php';</script>";
    exit;
}


if  ( isset($_POST['timeeditor']) ) {

  $userID = $_POST['timeeditor'];
  $user_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE userID = '$userID'"));

  require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";

  /* ----- Add/delete -punches ----- */
  echo '<section class="container">';
  echo '  <div class="leftContent" style="position: relative;">
            <form action="employeeinfo.php" method="post" style="margin: 0;">
              <button class="btn back" name="userID" value="'.$user_data[0].'">Takaisin</button>
            </form>
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
                <button type="submit" name="timeeditor" class="btn plus" value="'.$user_data[0].'">Lisää</button>
              </form>
            </div>';
  if (isset($_POST['in_date']) || isset($_POST['out_date'])) {
    echo '  <div class="section">
              <p>Nämä lisättiin onnistuneesti:</p>';
    include 'time_editor_add.php';
    echo '  </div>';
  }
  echo '  </div></div>';


  /* ----- List punches ----- */
  echo'    <div class="middleContent">
            <div class="box">
              <h2>Kellotuseditori - '.$user_data[1].'</h2>
              <div class="section">
              <form action="time_editor_edit.php" method="post">
                Oransilla merkityt kirjaukset ilmaisevat virheestä.
                <button type="submit" name="deletetime" value="'.$user_data[0].'" class="btn del trash" style="float:right; margin-bottom: 10px;">Poista valitut</button>
                <table class="sort-desc">
                  <thead>
                    <tr>
                      <th>Aikaleima</th>
                      <th class="filter-select filter-exact sorter-false">Sisään/Ulos</th>
                      <th class="sorter-false">Päivä</th>
                      <th class="sorter-false">Kello</th>
                      <th colspan="2" class="sorter-false" >Viesti</th>
                      <th class="sorter-false filter-false">Poista</th>
                      <th class="sorter-false filter-false">Muokkaa</th>
                    </tr>
                  </thead>
                  <tfoot>
                  <tr style="height: 20px;"></tr>
                    <tr class="tablesorter-ignoreRow">
                      <th colspan="8" class="ts-pager form-horizontal">
                        <button type="button" class="btn first"><i class="fas fa-angle-double-left"></i></button>
                        <button type="button" class="btn prev"><i class="fas fa-angle-left"></i></button>
                        <span class="pagedisplay"></span>
                        <button type="button" class="btn next"><i class="fas fa-angle-right"></i></button>
                        <button type="button" class="btn last"><i class="fas fa-angle-double-right"></i></button>
                      </th>
                    </tr>
                    <tr class="tablesorter-ignoreRow">
                      <th colspan="8" class="ts-pager form-horizontal">
                        max rivit: <select class="pagesize browser-default" title="Select page size">
                          <option value="50">50</option>
                          <option selected="selected" value="100">100</option>
                          <option value="200">200</option>
                          <option value="300">300</option>
                        </select>
                        sivu: <select class="pagenum browser-default" title="Select page number"></select>
                      </th>
                    </tr>
                  </tfoot>
                  <tbody>';
  $max = 0;
  $prev = '';
  $inoutdata_query = tc_query("SELECT * FROM info WHERE userID = '$userID' ORDER BY timestamp DESC");

  while ( $inout = mysqli_fetch_row($inoutdata_query) ) {

    $logTime = new DateTime("@$inout[3]");
    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

    echo "  <tr>
              <td style='text-align:center; color: grey;'>".$inout[3]."</td>";

    /* this part controls the in/out cells (basically just coloring)*/
    if ( $prev == '' || ($prev == 'out' && $inout[2] == 'in') || ($prev == 'in' && $inout[2] == 'out')) {
      if ($inout[2] == 'in') {
        echo "<td style='text-align:center;'><span class='inout' style='background-color:var(--lightgreen); border-radius: 0 0 10px 10px; margin-top: -4px;'>$inout[2]</span></td>";
      } else {
        echo "<td style='text-align:center;'><span class='inout' style='background-color:var(--red); border-radius: 10px 10px 0 0; margin-bottom: -7px;'>$inout[2]</span></td>"; }
    }
    else {  /* means that there is error (out isn't preceded by in) */
      echo "<td style='text-align:center;background-color: var(--orange);'><span>$inout[2] (virhe)</span></td>";
    }

      echo "  <td style='text-align:center;'>".$logTime->format("d.m.Y")."</td>
              <td style='text-align:center;'>".$logTime->format("H:i")."</td>
              <td colspan='2' style='word-wrap: break-word;'>$inout[4]</td>
              <td style='text-align:center;'>
                <label class='container'>
                  <input type='checkbox' name='deletelist[]' value='$inout[0]' class='check'>
                  <span class='checkmark'></span>
                </label>
              </td>
              <td style='text-align:center;'><button type='submit' name='altertime' value='$inout[0]' class='btn'><i class='fas fa-pencil-alt'></i></button></td>
            </tr>";
    $prev = $inout[2];
    $max++;
    if ($max == 500) { break; }
  }
echo '            </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
      </section>';

echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
}

?>

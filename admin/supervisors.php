<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Kellotuseditori</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->admin == 0) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if ($request == 'GET') {
    include 'header_get.php';

    $supervisors_query = tc_query("SELECT * FROM employees WHERE admin = 1 OR reports = 1 OR time_admin = 1 ORDER BY displayname ASC");


    echo '<section class="container">
        <div class="middleContent">
        <div class="box">
            <h2>Luo uusi valvoja</h2>
            <div class="section">
            <form name="form" action="supervisorcreate.php" method="post">
              <table>
              <tbody>
                <tr>
                    <td>Käyttäjätunnus:</td>
                    <td><input name="empfullname" type="text" required="true"></td>
                    <td style="color: grey; font-size: 12px;">Järjestelmän sisäiseen käyttöön</td>
                </tr>
                <tr>
                    <td>Nimi:</td>
                    <td><input name="displayname" type="text" required="true"></td>
                    <td style="color: grey; font-size: 12px;">Henkilön nimi</td>
                </tr>
                <tr>
                    <td>Salasana:</td>
                    <td><input name="password" type="text" required="true"></td>
                    <td style="color: grey; font-size: 12px;">Tällä valvoja kirjautuu järjestelmään</td>
                </tr>
                <tr>
                    <td>Viivakoodi:</td>
                    <td><input name="barcode" type="text" required="true"></td>
                    <td style="color: grey; font-size: 12px;">Tällä valvoja kellottaa itsensä töihin</td>
                </tr>
                <tr>
                    <td>Toimisto:</td>
                    <td><select name="office_name" onchange="group_names();" required="true"></select></td>
                    <td style="color: grey; font-size: 12px;">Toimisto, johon valvoja kuuluu</td>
                </tr>
                <tr>
                    <td>Ryhmä:</td>
                    <td><select name="group_name" required="true"></select></td>
                    <td style="color: grey; font-size: 12px;">Ryhmä, johon valvoja kuuluu</td>
                </tr>
                <tr>
                    <td><button name="create" type="submit" class="btn">Jatka</button></td>
                </tr>
              </tbody>';

echo '          </table>
              </form>
            </div>
          </div>

          <div class="box">
            <h2>Kaikki valvojat</h2>
            <div class="section">
            <form action="supervisoredit" method="post">
              <table class="sorted">
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

  while ( $supervisor = mysqli_fetch_array($supervisors_query) ) {

echo '          <tr>
                  <td>'.$supervisor[3].'</td>
                  <td>'.$supervisor[7].'</td>
                  <td>'.$supervisor[6].'</td>
                  <td style="text-align:center;"><button name="edittime" type="submit" class="btn" value="'.$supervisor[0].'">Valitse</button></td>
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


?>
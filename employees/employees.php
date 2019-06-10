<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Henkilöstöportaali</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if ($request == 'GET') {
  include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";
  
  $adminuserid = $_SESSION['logged_in_user']->userID;

  // Restricts which employees can be seen based on supervises table in the database
  if ($_SESSION['logged_in_user']->level >= 3) {
    $employee_query = tc_query("SELECT * FROM employees ORDER BY displayName ASC");
  } else {
    $employee_query = tc_query("SELECT * FROM employees 
                                WHERE groupID IN (
                                  SELECT groupID
                                  FROM groups NATURAL JOIN supervises
                                  WHERE userID = '$adminuserid' AND level = 0
                                  )
                                ORDER BY displayName ASC;");
  }


    echo '
      <section class="container">
        <div class="middleContent extrawide">
          <div class="box">
            <h2>Henkilöstö</h2>
            <div class="section">';
            if ($_SESSION['logged_in_user']->level >= 3) {
              echo '<a class="btn" href="employeecreate.php" style="margin-bottom: 20px;">Luo uusi <i class="fas fa-plus"></i></a>';
            } else {
              echo '<p>Huomaa, että ainoastaan admin voi luoda uusia käyttäjiä.<br> Näet alla ainoastaan omien ryhmiesi tason 0 henkilöt.</p><br>';
            }
    echo '  <form action="employeeinfo.php" method="post">
              <table class="sorted">
              <thead>
                <tr>
                  <th data-placeholder="Hae nimellä">Nimi</th>
                  <th data-placeholder="Hae käyttäjätunnuksella">Käyttäjätunnus</th>
                  <th class="filter-select filter-exact" data-placeholder="Kaikki">Toimisto</th>
                  <th class="filter-select filter-exact" data-placeholder="Kaikki">Ryhmä</th>
                  <th class="filter-select filter-exact sorter-false" data-placeholder="Kaikki">Käyttäjätaso</th>
                  <th class="filter-select filter-exact sorter-false" data-placeholder="Kaikki">Töissä</th>
                  <th class="sorter-false filter-false">Avaa</th>
                </tr>
              </thead>
              <tfoot>
              <tr style="height: 20px;"></tr>
                <tr class="tablesorter-ignoreRow">
                  <th colspan="7" class="ts-pager form-horizontal">
                    <button type="button" class="btn first"><i class="fas fa-angle-double-left"></i></button>
                    <button type="button" class="btn prev"><i class="fas fa-angle-left"></i></button>
                    <span class="pagedisplay"></span>
                    <button type="button" class="btn next"><i class="fas fa-angle-right"></i></button>
                    <button type="button" class="btn last"><i class="fas fa-angle-double-right"></i></button>
                  </th>
                </tr>
                <tr class="tablesorter-ignoreRow">
                  <th colspan="7" class="ts-pager form-horizontal">
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

    $employee_group = tc_select_value("groupName", "groups", "groupID = ?", $employee[2]);
    $employee_officeid = tc_select_value("officeID", "groups", "groupID = ?", $employee[2]);
    $employee_office = tc_select_value("officeName", "offices", "officeID = ?", $employee_officeid);

    $employee_level = "";
    if ($employee[3] == 0) {$employee_level = "Työntekijä (taso 0)";}
    if ($employee[3] == 1) {$employee_level = "Normaali valvoja (taso 1)";}
    if ($employee[3] == 2) {$employee_level = "Valvoja + editointi (taso 2)";}
    if ($employee[3] == 3) {$employee_level = "Admin (taso 3)";}

    echo '      <tr>
                  <td>'.$employee[1].'</td>
                  <td>'.$employee[0].'</td>
                  <td>'.$employee_office.'</td>
                  <td>'.$employee_group.'</td>
                  <td>'.$employee_level.'</td>
                  <td>'.$employee[5].'</td>
                  <td style="text-align:center;"><button name="userID" type="submit" class="btn" value="'.$employee[0].'"><i class="fas fa-user-cog"></i></button></td>
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
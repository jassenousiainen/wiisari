<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Työntekijät ja valvojat</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || !$_SESSION['logged_in_user']->isSuperior()) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if ($request == 'GET') {
  include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";
  
  $adminusername = $_SESSION['logged_in_user']->username;

  // Restricts which employees can be seen based on supervises table in the database
  if ($_SESSION['logged_in_user']->admin == 1) {
    $employee_query = tc_query("SELECT * FROM employees ORDER BY displayname ASC");
  } else {
    $employee_query = tc_query("SELECT * FROM employees 
                                WHERE groups IN (
                                  SELECT groupname
                                  FROM groups NATURAL JOIN supervises
                                  WHERE fullname = '$adminusername'
                                )
                                ORDER BY displayname ASC;");
  }


    echo '
      <section class="container">
        <div class="middleContent extrawide">
          <div class="box">
            <h2>Kaikki työntekijät</h2>
            <div class="section">';
            if ($_SESSION['logged_in_user']->admin == 1) {
              echo '<a class="btn" href="employeecreate.php" style="margin-bottom: 20px;">Luo uusi <i class="fas fa-plus"></i></a>';
            } else {
              echo '<p>Huomaa, että ainoastaan admin voi luoda uusia käyttäjiä</p><br>';
            }
    echo '  <form action="employeeinfo.php" method="post">
              <table class="sorted">
              <thead>
                <tr>
                  <th data-placeholder="Hae nimellä">Nimi</th>
                  <th data-placeholder="Hae käyttäjätunnuksella">Käyttäjätunnus</th>
                  <th class="filter-select filter-exact" data-placeholder="Kaikki">Toimisto</th>
                  <th class="filter-select filter-exact" data-placeholder="Kaikki">Ryhmä</th>
                  <th class="filter-select filter-exact sorter-false" data-placeholder="Kaikki">Oikeudet</th>
                  <th class="filter-select filter-exact sorter-false" data-placeholder="Kaikki">Töissä</th>
                  <th class="sorter-false filter-false">Avaa/Muokkaa</th>
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

    $rights = [];
    if ($employee[8] == 1) {array_push($rights, "admin");}
    if ($employee[9] == 1) {array_push($rights, "reports");}
    if ($employee[10] == 1) {array_push($rights, "editor");}

    echo '      <tr>
                  <td>'.$employee[3].'</td>
                  <td>'.$employee[0].'</td>
                  <td>'.$employee[7].'</td>
                  <td>'.$employee[6].'</td>';
    echo '        <td>';
    echo implode(", ", $rights);
    echo '        </td>
                  <td>'.$employee[12].'</td>';
    echo '        <td style="text-align:center;"><button name="empfullname" type="submit" class="btn" value="'.$employee[0].'"><i class="fas fa-user-edit"></i></button></td>
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
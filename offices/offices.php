<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Toimistot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 3) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if ($request == 'GET') {  
  $adminuserid = $_SESSION['logged_in_user']->userID;

  // Restricts which employees can be seen based on supervises table in the database
  if ($_SESSION['logged_in_user']->level >= 3) {
    $office_query = tc_query("SELECT * FROM offices ORDER BY officeName ASC");
  } else {
    
  }

  echo '
  <section class="container">
    <div class="middleContent">
      <div class="box">
        <h2>Toimistot</h2>
        <div class="section">';
        if ($_SESSION['logged_in_user']->level >= 3) {
          echo '<a class="btn plus" href="officecreate.php" style="margin-bottom: 20px;">Luo uusi</a>';
        } else {
          echo '<p>Huomaa, että ainoastaan admin voi luoda uusia käyttäjiä.<br> Näet alla ainoastaan omien ryhmiesi tason 0 henkilöt.</p><br>';
        }
    echo '  <form action="officeinfo.php" method="post">
              <table class="sorted">
              <thead>
                <tr>
                  <th data-placeholder="Hae nimellä">Nimi/avaa</th>
                  <th class="filter-false">Ryhmien määrä</th>
                  <th class="filter-false">Käyttäjien määrä</th>
                </tr>
              </thead>
              <tfoot>
              <tr style="height: 20px;"></tr>
                <tr class="tablesorter-ignoreRow">
                  <th colspan="3" class="ts-pager form-horizontal">
                    <button type="button" class="btn first"><i class="fas fa-angle-double-left"></i></button>
                    <button type="button" class="btn prev"><i class="fas fa-angle-left"></i></button>
                    <span class="pagedisplay"></span>
                    <button type="button" class="btn next"><i class="fas fa-angle-right"></i></button>
                    <button type="button" class="btn last"><i class="fas fa-angle-double-right"></i></button>
                  </th>
                </tr>
                <tr class="tablesorter-ignoreRow">
                  <th colspan="3" class="ts-pager form-horizontal">
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
  if($office_query != FALSE){
    while ( $office = mysqli_fetch_array($office_query) ) {
      $query = tc_query("SELECT COUNT(groupID) FROM groups WHERE officeId = ?",$office[0]);
      if($query != FALSE){
        $group_cnt = mysqli_fetch_row($query);
        if($group_cnt != NULL){
          $group_cnt = $group_cnt[0];
        }
      }
      $query = tc_query("SELECT COUNT(employees.userID) FROM employees,groups,offices WHERE employees.groupID = groups.groupID AND groups.officeID = offices.officeID AND offices.officeID = ?", $office[0]);
      if($query != FALSE){
        $user_cnt = mysqli_fetch_row($query);
        if($user_cnt != NULL){
          $user_cnt = $user_cnt[0];
        }
      }
      

      echo '      <tr>
                    <td><button name="officeID" type="submit" value="'.$office['officeID'].'" class="link">'.$office['officeName'].'</button></td>
                    <td>'.$group_cnt.'</td>
                    <td>'.$user_cnt.'</td>
                  </tr>';
    }
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
<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Ryhmät</title>\n";

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
    $group_query = tc_query("SELECT * FROM groups LEFT JOIN offices ON (groups.officeID = offices.officeID) ORDER BY groupName ASC");
  } else {
    
  }

  echo '
  <section class="container">
    <div class="middleContent">
      <div class="box">
        <h2>Ryhmät</h2>
        <div class="section">';
        if ($_SESSION['logged_in_user']->level >= 3) {
          echo '<a class="btn plus" href="groupcreate.php" style="margin-bottom: 20px;">Luo uusi</a>';
        } else {
          echo '<p>Huomaa, että ainoastaan admin voi luoda uusia käyttäjiä.<br> Näet alla ainoastaan omien ryhmiesi tason 0 henkilöt.</p><br>';
        }
    echo '  <form action="groupinfo.php" method="post">
              <table class="sorted">
              <thead>
                <tr>
                  <th data-placeholder="Hae nimellä">Nimi/avaa</th>
                  <th data-placeholder="Hae nimellä">Toimisto</th>
                  <th class="filter-false">Käyttäjien määrä</th>
                  <th class="filter-false">Valvojien määrä</th>
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
  if($group_query != FALSE){
    while ( $group = mysqli_fetch_array($group_query) ) {
      $gid = $group['groupID'];
      $user_count = mysqli_fetch_row(tc_query("SELECT COUNT(userID) FROM employees WHERE groupID = '$gid'"))[0];
      $supervisor_count = mysqli_fetch_row(tc_query("SELECT  COUNT(userID) FROM supervises WHERE groupID = '$gid'"))[0];
        
      echo '      <tr>
                    <td><button name="groupID" type="submit" value="'.$group['groupID'].'" class="link">'.$group['groupName'].'</button></td>
                    <td>'.$group['officeName'].'</td>
                    <td>'.$user_count.'</td>
                    <td>'.$supervisor_count.'</td>
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
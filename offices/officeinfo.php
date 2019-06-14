<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Toimiston tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if (isset($_POST['officeID'])) {
    $officeID = $_POST['officeID'];

    $officeData = mysqli_fetch_row(tc_query("SELECT * FROM offices WHERE officeID = '$officeID'"));
    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="offices.php"> Takaisin</a>';
    echo '
            <div class="box">
                <h2>Toimiston '.$officeData[1].' tiedot</h2>
                <div class="section">';
                if (isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level >= 3) {
                  echo '<form name="form" action="/offices/office-edit.php" method="post">
                      <input type="hidden" name="oldName" value="'.$officeData[1].'"></input>
                          <table>
                            <tbody>
                              <tr>
                                <td>Nimi:</td>
                                  <td><input name="officeName" type="text" required="true" value="'.$officeData[1].'"></td>
                                  <td style="color: grey; font-size: 13px;">Toimiston nimi</td>
                              </tr>
                            </tbody>
                          </table>';
                          echo'
                          <br><button name="officeID" type="submit" class="btn" value="'.$officeID.'">Muuta tietoja <i class="fas fa-paper-plane"></i></button>
                        </form>
                        </div>';
                  echo '<div class="section">
                          <table class="sorted">
                            <thead>
                              <tr>
                                <th data-placeholder="Hae nimellä">Nimi</th>
                                <th class="filter-false">Käyttäjän määrä</th>
                                <th class="sorter-false filter-false">Muokka</th>
                                <th class="sorter-false filter-false">Poista</th>
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
      $group_query = tc_query("SELECT * FROM groups WHERE officeID = ?",$officeData[0]);
      while ( $group = mysqli_fetch_array($group_query) ) {
        $user_cnt  = mysqli_num_rows(tc_query("SELECT 1 FROM employees WHERE groupID = ?", $officeData[0]));
        
      echo '                  <tr id="'.$group[0].'">
                                <td>'.$group[1].'</td>
                                <td>'.$user_cnt.'</td>
                                <td style="text-align:center;">
                                  <form id="edit" action="../groups/groupedit.php" method="post">
                                    <button name="groupID" type="submit" class="btn" value="'.$group[0].'" form="edit"><i class="fas fa-user-cog"></i></button>
                                  </form>
                                </td>
                                <td style="text-align:center;">
                                  <form id="delete" action="../groups/groupdel.php" method="post">
                                    <button name="groupID" type="submit" class="btn del" value="'.$group[0].'" form="delete"><i class="fas fa-trash-alt"></i></button>
                                  </form>
                                </td>                             
                              </tr>';
      }
      echo '                </tbody>
                          </table></div>';
      }
      if ($_SESSION['logged_in_user']->level >= 3) {
        echo '<div class="section">
                    <p><b>Poista Toimisto:</b></p>
                    <p>Toimisto poiston lisäksi kaikki muu tieto, kuten ryhmät poistetaan.</p>
                    <form action="'."officedelete.php".'" method="post">
                      <button name="officeID" type="submit" class="btn del" value="'.$officeID.'"><i class="fas fa-trash-alt"></i> Poista</button>
                    </form>
              </div>
              ';
    }
      echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';

}
?>

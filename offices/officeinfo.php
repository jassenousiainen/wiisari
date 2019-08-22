<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Toimiston tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 3) {
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
    if(isset($officeData)){
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
                          <br><button name="officeID" type="submit" class="btn send" value="'.$officeID.'">Muuta tietoja</button>
                        </form>
                        </div>';
                  echo '<div class="section">
                          <p><b>Tähän toimistoon kuuluvat ryhmät</b></p>
                          <form action="../groups/groupinfo.php" method="post">
                          <table class="sorted">
                            <thead>
                              <tr>
                                <th data-placeholder="Hae nimellä">Nimi/avaa</th>
                                <th class="filter-false">Käyttäjien määrä</th>
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
      }
      $group_query = tc_query("SELECT * FROM groups WHERE officeID = ?",$officeData[0]);
      while ( $group = mysqli_fetch_array($group_query) ) {
        $query = tc_query("SELECT COUNT(userID) FROM employees WHERE groupID = ?",$group[0]);
        if(!empty($query)){
          $user_cnt  = mysqli_fetch_row($query);
          if($user_cnt != NULL){
            $user_cnt = $user_cnt[0];
          }
        }
        
      echo '                  <tr id="'.$group[0].'">
                                <td><button name="groupID" type="submit" value="'.$group['groupID'].'" class="link">'.$group['groupName'].'</button></td>
                                <td>'.$user_cnt.'</td>                           
                              </tr>';
      }
      echo '                </tbody>
                          </table></form></div>';
      }
      if ($_SESSION['logged_in_user']->level >= 3) {
        echo '<div class="section">
                    <p><b>Poista Toimisto:</b></p>
                    ';?>
                    <form action="officedelete.php" method="post" onsubmit="return confirm('Oletko varma että haluat poista toimiston?');">
                    <?php echo'
                    <table>
                    <tbody>
                      <tr>
                        <td>Poistetaanko myös kaikki ryhmät?</td>
                        <td>
                          <label class="switch">
                            <input type="checkbox" name="groupDel">
                            Ei <span class="slider del"></span> Kyllä
                          <label>
                          <br>
                        </td>
                      </tr>
                      <tr>
                      <td>Poistetaanko myös kaikki käyttäjät?</td>
                      <td>
                        <label class="switch">
                          <input type="checkbox" name="userDel">
                          Ei <span class="slider del"></span> Kyllä
                        <label>
                        <br><br>
                      </td>
                    </tr>
                    </tbody>
                  </table>';
                  echo '<button name="officeID" type="submit" class="btn del trash" value="'.$officeID.'">Poista</button>

                </form>
              </div>
              ';
    }
      echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';

}
?>

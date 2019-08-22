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
if (isset($_POST['groupID'])) {
    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";

    $groupID = $_POST['groupID'];
    $query = tc_query("SELECT * FROM groups WHERE groupID = '$groupID'");
    if($query != FALSE){
        $groupData = mysqli_fetch_row($query);
    }
    $query = tc_query("SELECT * FROM groups NATURAL JOIN offices WHERE groupID = '$groupID'");
    if($query != FALSE){
        $officeData = mysqli_fetch_row($query);
    }

    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="groups.php"> Takaisin</a>';
        if(isset($groupData)){
    echo '
            <div class="box">
                <h2>Ryhmän '.$groupData[1].' tiedot</h2>
                <div class="section">';
                if (isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level >= 3) {
                  echo '<form name="form" action="/groups/group-edit.php" method="post">
                      <input type="hidden" name="oldName" value="'.$groupData[1].'"></input>
                      <input type="hidden" name="oldOffice" value="'.$officeData[3].'"></input>

                          <table>
                            <tbody>
                              <tr>
                                <td>Nimi:</td>
                                  <td><input name="groupName" type="text" required="true" value="'.$groupData[1].'"></td>
                                  <td style="color: grey; font-size: 13px;">Toimiston nimi</td>
                              </tr>
                              <tr>
                              <td>Toimisto:</td>
                              <td>
                                  <select name="office_name" onfocus="office_names();" required="true">
                                      <option selected>'.$officeData[3].'</option>
                                  </select>
                              </td>
                                <td style="color: grey; font-size: 13px;">Toimisto, johon ryhmä kuuluu</td>
                            </tr>
                            </tbody>
                          </table>';
                          echo'
                          <br><button name="groupID" type="submit" class="btn send" value="'.$groupID.'">Muuta tietoja</button>
                        </form>
                        </div>';
                }
        }
        $query = tc_query("SELECT employees.displayName, employees.userID FROM supervises, employees WHERE employees.userID = supervises.userID AND supervises.groupID = '$groupID'");
        

        echo '<div class="section">
                <p><b>Ryhmän valjojat:</b></p>
                <table class="valvojat">
                  <tbody>
                  <form action="../employees/employeeinfo.php" method="post">';
                  while ( $user = mysqli_fetch_array($query) ) {
                   echo '<tr>
                          <td>
                            <button name="userID" type="submit" value="'.$user['userID'].'" class="link">'.$user['displayName'].'</button>
                          </td>
                        </tr>';
                  }
                  echo '</form>
                  </tbody>
                </table>
              </div>';        
      if ($_SESSION['logged_in_user']->level >= 3) {
        echo '<div class="section">
                <p><b>Poista Ryhmä:</b></p>';?>
                <form action="groupdel.php" method="post" onsubmit="return confirm('Oletko varma että haluat poista Ryhmän?');">
                <?php echo'
                  <table>
                    <tr>
                      <td>Poistetaanko myös kaikki käyttäjät?</td>
                      <td>
                        <label class="switch">
                          <input type="checkbox" name="userDel">
                          Ei <span class="slider del"></span> Kyllä
                        <label>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <button name="groupID" type="submit" class="btn del trash" value="'.$groupID.'">Poista</button>   
                </form>
              </div>
              ';
    }
      echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';


}
?>

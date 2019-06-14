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
if (isset($_POST['groupID'])) {
    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";

    $groupID = $_POST['groupID'];

    $groupData = mysqli_fetch_row(tc_query("SELECT * FROM groups WHERE groupID = '$groupID'"));
    $officeData = mysqli_fetch_row(tc_query("SELECT * FROM groups NATURAL JOIN offices WHERE groupID = '$groupID'"));

    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="groups.php"> Takaisin</a>';
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
                          <br><button name="groupID" type="submit" class="btn" value="'.$groupID.'">Muuta tietoja <i class="fas fa-paper-plane"></i></button>
                        </form>
                        </div>';
      }
      if ($_SESSION['logged_in_user']->level >= 3) {
        echo '<div class="section">
                    <p><b>Poista Ryhmä:</b></p>
                    <form action="'."groupdel.php".'" method="post">
                      <button name="groupID" type="submit" class="btn del" value="'.$groupID.'"><i class="fas fa-trash-alt"></i> Poista</button>
                    </form>
              </div>
              ';
    }
      echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';


}
?>

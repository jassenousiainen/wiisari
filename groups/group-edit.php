<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Ryhmän tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 3) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}
$error = false;
if (!isset($_POST['groupName']) || $_POST['groupName'] == "" ) {
    $error = true; $nameID = "error";
}else {
    $groupName = $_POST['groupName'];
    $groupID = $_POST['groupID'];

    $officeName = $_POST['office_name'];
    $oldName = $_POST['oldName'];
    $oldOffice = $_POST['oldOffice'];

    $officeData = mysqli_fetch_row(tc_query("SELECT * FROM offices WHERE officeName = '$officeName' LIMIT 1"));
    $officeID = $officeData[0];
    if($groupName != $oldName || $officeName != $oldOffice){
      $groupNamecheck = mysqli_fetch_row(tc_query( "SELECT groupName FROM groups WHERE officeID = '$officeID' AND groupName = '$groupName'"));
      if (!empty($groupNamecheck)) {
        $error = true; $nameID = "error";
      }
    }
}
if ($error) {
  echo '<section class="container">
          <div class="middleContent">
            <div class="box">
              <h2>Muokka toimiston tietoja (muokkaamisesta tapahtui virhe!)</h2>
              <div class="section">
                <form name="form" action="'.$self.'" method="post">
                <input type="hidden" name="oldName" value="'.$oldName.'"></input>
                <input type="hidden" name="oldOffice" value="'.$oldOffice.'"></input>

                <input type="hidden" name="office_name" value="'.$officeName.'"></input>

                  <table>
                    <tbody>';
    if ($nameID == "error") {
      echo '          <tr>
                        <td class="errortext" colspan="3">Ryhmän nimi oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                      </tr>
                      <tr>
                        <td>Nimi:</td>
                        <td><input name="groupName" type="text" required="true"></td>
                        <td style="color: grey; font-size: 13px;">Toimiston nimi</td>
                      </tr>
                      <tr>
                        <td><button name="groupID" value="'.$groupID.'" type="submit" class="btn">Jatka</button></td>
                      </tr>
                    </tbody>
                  </table>
                </form>
              </div>
            </div>
          </div>
      </section>';
    }
  }else {
      if (isset($_POST['groupID'])) {

        $groupID = $_POST['groupID'];
        $groupName = $_POST['groupName'];
        echo '
        <section class="container">
            <div class="middleContent">
                <a class="btn back" href="groups.php"> Takaisin</a>
                <div class="box">
                    <h2>Toimiston tiedot on muokkattu</h2>
                    <div class="section">';
                      if($groupName != $oldName){
                        echo '<p>'.$_POST['oldName'].' uusi nimi: '.$groupName.'</p>';
                      }elseif($officeName != $oldOffice) {
                        echo '<p>'.$groupName.' uusi toimisto: '.$officeName.'</p>';
                      }else{
                        echo '<p>Mitään ei ole muutunut.</p>';
                      }
                    echo '</div>
                </div>
            </div>
        </section>';

        tc_update_strings("groups", array(
          'groupName' => $groupName,
          'officeID' => $officeID
        ), "groupID = ?", $groupID);
      }
}

?>            
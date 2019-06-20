<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Käyttäjän luonti</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$error = false;

// This shows the form for creating new user
if ( $request == "GET") {
    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";
    include "$_SERVER[DOCUMENT_ROOT]/scripts/barcode.php";

    echo '
      <section class="container">
        <div class="middleContent">
          <a class="btn back" href="employees.php">Takaisin</a>
          <div class="box">
            <h2>Luo uusi työntekijä/valvoja</h2>
            <div class="section">
              <form name="form" action="'.$self.'" method="post">
                <table>
                  <tbody>
                    <tr>
                        <td>Käyttäjätunnus:</td>
                        <td><input name="userID" id="userID" type="text" required="true">
                        <button type="button" class="btn" onclick="gen_barcode(userID);"><i class="fas fa-dice"></i></button></td>
                        <td style="color: grey; font-size: 13px;">Uniikki tunniste, jolla henkilö kirjautuu sisään tai kellottaa itsensä</td>
                    </tr>
                    <tr>
                        <td>Nimi:</td>
                        <td><input name="displayName" type="text" required="true"></td>
                        <td style="color: grey; font-size: 13px;">Henkilön nimi</td>
                    </tr>
                    <tr>
                        <td>Toimisto:</td>
                        <td><select name="office_name" onchange="group_names();" required="true"></select></td>
                        <td style="color: grey; font-size: 13px;">Toimisto, johon henkilö kuuluu</td>
                    </tr>
                    <tr>
                        <td>Ryhmä:</td>
                        <td><select name="group_name" required="true"></select></td>
                        <td style="color: grey; font-size: 13px;">Ryhmä, johon henkilö kuuluu</td>
                    </tr>
                    <tr>
                      <td><br></td>
                    </tr>
                    <tr>
                      <td>Tuntien lasku aikaisintaan</td>
                      <td><input name="earliest" type="time"></td>
                      <td style="color: grey; font-size: 13px;">Mikäli henkilö kellottaa itsensä sisään aikaisemmin, alkaa tuntien lasku vasta tästä kellonajasta. Voi olla tyhjä.</td>
                    </tr>
                    <tr>
                      <td>Tuntien lasku myöhäisimpään</td>
                      <td><input name="latest" type="time"></td>
                      <td style="color: grey; font-size: 13px;">Mikäli henkilö kellottaa itsensä ulos myöhemmin, päättyy tuntien lasku tähän kellonaikaan. Henkilö ei voi kellottaa itseänsä sisään tämän ajan jälkeen. Voi olla tyhjä.</td>
                    </tr>
                    <tr>
                      <td><br></td>
                    </tr>';
    if ($_SESSION['logged_in_user']->level >= 3) {
      echo '
                    <tr>
                      <td>(taso 0) Normaali työntekijä:</td>
                      <td>
                        <label class="container">
                          <input type="radio" name="level" value="0" class="check" checked>
                          <span class="checkmark"></span>
                        </label>
                      </td>
                      <td style="color: grey; font-size: 13px;">Voi kellottaa itsensä ja nähdä omat työtuntinsa</td>
                    </tr>
                    <tr>
                      <td>(taso 1) Normaali valvoja:</td>
                      <td>
                        <label class="container">
                          <input type="radio" name="level" value="1" class="check" id="reports">
                          <span class="checkmark"></span>
                        </label>
                      </td>
                      <td style="color: grey; font-size: 13px;">Normaali valvoja näkee valittujen ryhmien työtunnit</td>
                    </tr>
                    <tr>
                      <td>(taso 2) Valvoja + editori:</td>
                      <td>
                        <label class="container">
                          <input type="radio" name="level" value="2" class="check" id="editor">
                          <span class="checkmark"></span>
                        </label>
                      </td>
                      <td style="color: grey; font-size: 13px;">Editorioikeuksilla valvoja voi muokata valittujen ryhmien työntekijöiden tietoja sekä työaikoja</td>
                    </tr>
                    <tr>
                      <td>(taso 3) Admin:</td>
                      <td>
                        <label class="container">
                          <input type="radio" name="level" value="3" class="check" id="admin">
                          <span class="checkmark"></span>
                        </label>
                      </td>
                      <td style="color: grey; font-size: 13px;">Admininilla on valvontaoikeus kaikkiin ryhmiin. Admin voi myös luoda uusia työntekijöitä ja muokata kaikkien tietoja täysin.</td>
                    </tr>
                    <tr id="password">
                        <td>Salasana:</td>
                        <td><input name="password" type="text"></td>
                        <td style="color: grey; font-size: 13px;">Tällä valvoja kirjautuu järjestelmään</td>
                    </tr>';
    }
    echo '
                  </tbody>
                </table>';


      $groupquery = tc_query( "SELECT groupName, officeName, groupID FROM groups NATURAL JOIN offices ORDER BY groupName;" );

      echo '<div class="chooseGroups" style="display:none;">
              <p><b>Valitse valvottavat ryhmät:</b></p>';

      include "group_picker.php";

      echo '  </div>
                          <br><button name="create" type="submit" class="btn send">Luo käyttäjä</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>';

        echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
}

// This checks the input for errors and shows the form for choosing groups for supervision
// This block loops as-long-as input contains any errors
else if  ( isset($_POST['create']) ) {

  if ($_SESSION['logged_in_user']->level >= 3) {
    if (!isset($_POST['level'])) { $level = 0; }
    else { $level = $_POST['level']; }
  } else {
    $level = 0;
  }

    // Chekcs if given username already exists in database
    if (!isset($_POST['userID']) || $_POST['userID'] == "" ) {$error = true; $userID = "error";}
    else {
        $userID = $_POST['userID'];
        $userIDcheck = mysqli_fetch_row(tc_query( "SELECT userID FROM employees WHERE userID = '$userID'"));
        if (!empty($userIDcheck)) {$error = true; $userID = "error";}
    }

    if (!isset($_POST['displayName']) || $_POST['displayName'] == "") {$error = true; $displayName = "error";}
    else {$displayName = $_POST['displayName'];}

    if (!isset($_POST['password']) && $level > 0) {$error = true; $password = "error";}
    else if (empty($_POST['password']) && $level > 0) {$error = true; $password = "error";}
    else if ($level > 0) {$password = password_hash($_POST['password'].$salt, PASSWORD_DEFAULT);}
    else {$password = "";}

    if (!isset($_POST['group_name']) || $_POST['group_name'] == "") {$error = true; $groupID = "error";}
    else {$groupID = $_POST['group_name'];}

    if (!empty($_POST['grouplist'])) {
      $grouplist = $_POST['grouplist'];
    } else if (!empty($_POST['grouplist2'])) {
      $grouplist = explode(',', $_POST['grouplist2']);
    } else {
      $grouplist = array();
    }

    if (empty($_POST['earliest']) || empty($_POST['latest'])) {
      $earliestStart = null;
      $latestEnd = null;
    } else {
      $earliestStart = $_POST['earliest'];
      $latestEnd =$_POST['latest'];
    }


    // Displays the form again with input fields that contained errors
    // Notice that the fields that did not contain any errors are hidden but sent with post
    if ($error) {
        echo '<section class="container">
            <div class="middleContent">
            <div class="box">
                <h2>Luo uusi valvoja (luonnissa tapahtui virhe!)</h2>
                <div class="section">
                <form name="form" action="'.$self.'" method="post">
                <table>
                <tbody>';
        if ($userID == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Käyttäjätunnus oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Käyttäjätunnus:</td>
                        <td><input name="userID" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Järjestelmän sisäiseen käyttöön</td>
                    </tr>';
        } else {
            echo '<input name="userID" value="'.$userID.'" type="hidden">';
        }
        if ($displayName == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Nimi oli tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Nimi:</td>
                        <td><input name="displayName" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Henkilön nimi</td>
                    </tr>';
        } else {
            echo '<input name="displayName" value="'.$displayName.'" type="hidden">';
        }
        if ($password == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Salasana oli tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Salasana:</td>
                        <td><input name="password" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Tällä valvoja kirjautuu järjestelmään</td>
                    </tr>';
        } else {
            echo '<input name="password" value="'.$_POST['password'].'" type="hidden">';
        }
        if ($groupID == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Et valinnut toimistoa tai ryhmää, valitsethan uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Toimisto:</td>
                        <td><select name="office_name" onchange="group_names();" required="true"></select></td>
                        <td style="color: grey; font-size: 12px;">Toimisto, johon valvoja kuuluu</td>
                    </tr>';
        
            echo '  <tr>
                        <td>Ryhmä:</td>
                        <td><select name="group_name" required="true"></select></td>
                        <td style="color: grey; font-size: 12px;">Ryhmä, johon valvoja kuuluu</td>
                    </tr>';
        } else {
            echo '<input name="group_name" value="'.$groupID.'" type="hidden">';
        }
        echo '<input name="level" value="'.$level.'" type="hidden">';
        echo '<input name="grouplist2" value="'.implode(',', $grouplist).'" type="hidden">';
        echo '<input name="earliest" value="'.$earliestStart.'" type="hidden">';
        echo '<input name="latest" value="'.$latestEnd.'" type="hidden">';
        echo '      <tr>
                        <td><button name="create" type="submit" class="btn send">Jatka</button></td>
                    </tr>
                </tbody>
                </table>
            </form>
        </div>
        </div>
    </section>';
    }
    else {  // This part is run only if all inputs have been checked to be of correct form
      pdo_connect();
      
      $emp_stmt = $pdo->prepare("INSERT INTO employees VALUES (?,?,?,?,?,?,?,?)");
      $emp_stmt->execute(array($userID, $displayName, $groupID, $level, $password, "out", $earliestStart, $latestEnd));

      if (($level == 1 || $level == 2) && !empty($grouplist)) {
        foreach($grouplist as $grp) {
          $groupdata = array("userID" => $userID, "groupID" => $grp);
          tc_insert_strings("supervises", $groupdata);
        }
      }

      $newUserData = mysqli_fetch_array(tc_query("SELECT * FROM employees NATURAL JOIN groups NATURAL JOIN offices WHERE userID = '$userID'"));

      echo '
      <section class="container">
        <div class="middleContent">
          <a class="btn back" href="employees.php"> Takaisin</a>
          <div class="box">
            <h2>Uusi työntekijä/valvoja luotu</h2>
            <div class="section">
              <table>
                <tr>
                  <td>Käyttäjänimi:</td>
                  <td>'.$newUserData['userID'].'</td>
                </tr>
                <tr>
                  <td>Nimi:</td>
                  <td>'.$newUserData['displayName'].'</td>
                </tr>
                <tr>
                  <td>Toimisto:</td>
                  <td>'.$newUserData['officeName'].'</td>
                </tr>
                <tr>
                  <td>Ryhmä:</td>
                  <td>'.$newUserData['groupName'].'</td>
                </tr>
                <tr>
                  <td>Aikaisin aloitusaika:</td>
                  <td>'.$newUserData['earliestStart'].'</td>
                </tr>
                <tr>
                  <td>Myöhäisin lopetusaika:</td>
                  <td>'.$newUserData['latestEnd'].'</td>
                </tr>
                <tr>
                  <td>Taso:</td>
                  <td>'.$newUserData['level'].'</td>';
      if ($level > 0) {
        echo '  <tr>
                  <td>Salasana:</td>
                  <td>'.$_POST['password'].'</td>
                </tr>';
      }
      echo '  </table>
            </div>
          </div>
        </div>
      </section>';

    }
}

echo "<script type='text/javascript' language='javascript'>office_names();</script>";



?>
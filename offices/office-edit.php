<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Toimiston tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}
$error = false;
if (!isset($_POST['officeName']) || $_POST['officeName'] == "" ) {
    $error = true; $nameID = "error";
}else {
    $officeName = $_POST['officeName'];
    $officeID = $_POST['officeID'];
    $oldName = $_POST['oldName'];

    $officeNamecheck = mysqli_fetch_row(tc_query( "SELECT officeName FROM offices WHERE officeName = '$officeName'"));
    if (!empty($officeNamecheck)) {
      $error = true; $nameID = "error";
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
                  <table>
                    <tbody>';
    if ($nameID == "error") {
      echo '          <tr>
                        <td class="errortext" colspan="3">Toimiston nimi oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                      </tr>
                      <tr>
                        <td>Nimi:</td>
                        <td><input name="officeName" type="text" required="true"></td>
                        <td style="color: grey; font-size: 13px;">Toimiston nimi</td>
                      </tr>
                      <tr>
                        <td><button name="officeID" value="'.$officeID.'" type="submit" class="btn">Jatka</button></td>
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
      if (isset($_POST['officeID'])) {

        $officeID = $_POST['officeID'];
        $officeName = $_POST['officeName'];
        echo '
        <section class="container">
            <div class="middleContent">
                <a class="btn back" href="offices.php"> Takaisin</a>
                <div class="box">
                    <h2>Toimiston tiedot on muokkattu</h2>
                    <div class="section">
                        <p>'.$_POST['oldName'].' uusi nimi: '.$officeName.'</p>
                    </div>
                </div>
            </div>
        </section>';
        tc_update_strings("offices", array(
          'officeName' => $officeName,
        ), "officeID = ?", $officeID);
      }
}

?>            
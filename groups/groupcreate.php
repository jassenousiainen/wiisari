<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Ryhmän luonti</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 3) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$error = false;

// This shows the form for creating new user
if ( $request == "GET") {
  include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";
  echo '
  <section class="container">
    <div class="middleContent">
      <a class="btn back" href="groups.php"> Takaisin</a>
      <div class="box">
        <h2>Luo uusi ryhmä</h2>
        <div class="section">
          <form name="form" action="'.$self.'" method="post">
            <table>
              <tbody>
                <tr>
                  <td>Nimi:</td>
                  <td><input name="groupName" type="text" required="true"></td>
                  <td style="color: grey; font-size: 13px;">Ryhmän nimi</td>
                </tr>
                <tr>
                  <td>Toimisto:</td>
                  <td><select name="office_name" required="true"></select></td>
                  <td style="color: grey; font-size: 13px;">Toimisto, johon ryhmä kuuluu</td>
              </tr>
              </tbody>
            </table>';
            echo'<table id="myGroups"> 
            
            
            </table>';

            echo'
            <br><button name="create" type="submit" class="btn send">Luo Ryhmä</button>
          </form>
        </div>
      </div>
    </div>
  </section>';

}else if  ( isset($_POST['create']) ) {
  if (!isset($_POST['office_name']) || $_POST['office_name'] == "" ) {
     $error = true; $OnameID = "error";
  }else {
      $officeName = $_POST['office_name'];
      $officeID_query = mysqli_fetch_row(tc_query( "SELECT officeID FROM offices WHERE officeName = '$officeName' LIMIT 1"));
      $officeID = $officeID_query[0];
    }
  if (!isset($_POST['groupName']) || $_POST['groupName'] == "" ) {
     $error = true; $nameID = "error";
  }else {
      $groupName = $_POST['groupName'];
      $groupNamecheck = mysqli_fetch_row(tc_query( "SELECT groupName FROM groups WHERE officeID = '$officeID' AND groupName = '$groupName'"));
      if (!empty($groupNamecheck)) {$error = true; $nameID = "error";}
  }
  

  if ($error) {
    echo '<section class="container">
      <div class="middleContent">
        <div class="box">
          <h2>Luo ryhmä toimisto (luonnissa tapahtui virhe!)</h2>
          <div class="section">
            <form name="form" action="'.$self.'" method="post">
              <table>
                <tbody>';
    if ($nameID == "error") {
      echo '      <tr>
                    <td class="errortext" colspan="3">Ryhmän nimi oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                  </tr>
                  <tr>
                    <td>Nimi:</td>
                    <td><input name="groupName" type="text" required="true"></td>
                    <td style="color: grey; font-size: 13px;">Ryhmän nimi</td>
                  </tr>';
                  echo '      <input name="office_name" value="'.$officeName.'" type="hidden">';
    } else {


    }
    
    echo '        <tr>
                    <td><button name="create" type="submit" class="btn">Jatka</button></td>
                  </tr>
                </tbody>
              </table>
            </form>
          </div>
        </div>
      </section>';
  }else{
    tc_insert_strings("groups", array(
    'groupName'        => $groupName,
    'officeID'         => $officeID
    ));
    echo '
      <section class="container">
        <div class="middleContent">
          <a class="btn back" href="groups.php"> Takaisin</a>
          <div class="box">
            <h2>Uusi ryhmä luotu</h2>
            <div class="section">
              <p> Uusi ryhmä '.$groupName.' on luotu toimistoon '.$officeName.'.</p>
            </div>
          </div>
        </div>
      </section>';
  }
}
echo "<script type='text/javascript' language='javascript'>office_names();</script>";
?>
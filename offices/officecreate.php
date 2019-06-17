<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Toimiston luonti</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 3) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$error = false;

// This shows the form for creating new user
if ( $request == "GET") {
  include "$_SERVER[DOCUMENT_ROOT]/scripts/officesjs.php";
  echo '
  <section class="container">
    <div class="middleContent">
      <a class="btn back" href="offices.php"> Takaisin</a>
      <div class="box">
        <h2>Luo uusi toimisto</h2>
        <div class="section">
          <form name="form" action="'.$self.'" method="post">
            <table>
              <tbody>
                <tr>
                  <td>Nimi:</td>
                  <td><input name="officeName" type="text" required="true"></td>
                  <td style="color: grey; font-size: 13px;">Toimiston nimi</td>
                </tr>
                <tr>
                  <td>Lisää ryhmä</td>
                  <td>
                    <label class="container">
                      <input type="checkbox" name="groups" class="check" id="groups" onclick="addGroupInput()">
                      <span class="checkmark"></span>
                    </label>
                  </td>
                  <td style="color: grey; font-size: 13px;">Lisää ryhmä tähän toimistoon</td>
                </tr>
              </tbody>
            </table>';
            echo'<table id="myGroups"> 
            
            
            </table>';

            echo'
            <br><button name="create" type="submit" class="btn send">Luo Toimisto</button>
          </form>
        </div>
      </div>
    </div>
  </section>';

}else if  ( isset($_POST['create']) ) {
  if (!isset($_POST['input_group_name'])) { $officeGroups = 0; }
    else { $officeGroups = $_POST['input_group_name']; }
  if (!isset($_POST['officeName']) || $_POST['officeName'] == "" ) {$error = true; $nameID = "error";}
    else {
      $officeName = $_POST['officeName'];
      $officeNamecheck = mysqli_fetch_row(tc_query( "SELECT officeName FROM offices WHERE officeName = '$officeName'"));
      if (!empty($officeNamecheck)) {$error = true; $nameID = "error";}
    }

  if ($error) {
    echo '<section class="container">
      <div class="middleContent">
        <div class="box">
          <h2>Luo uusi toimisto (luonnissa tapahtui virhe!)</h2>
          <div class="section">
            <form name="form" action="'.$self.'" method="post">
              <table>
                <tbody>';
    if ($nameID == "error") {
      echo '      <tr>
                    <td class="errortext" colspan="3">Toimiston nimi oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                  </tr>
                  <tr>
                    <td>Nimi:</td>
                    <td><input name="officeName" type="text" required="true"></td>
                    <td style="color: grey; font-size: 13px;">Toimiston nimi</td>
                  </tr>';
    } else {
      echo '      <input name="officeName" value="'.$nameID.'" type="hidden">';
    }
    
      if (!empty($officeGroups)) {
        $i = 1;
        foreach($officeGroups as $group) {
          if(!empty($group)){
            echo '<input name="input_group_name['.$i.']" value="'.$group.'" type="hidden">';
            $i++;
          }
        }
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
    tc_insert_strings("offices", array(
    'officeName'        => $officeName,
    ));
    $newOfficeID = mysqli_fetch_array(tc_query("SELECT * FROM offices WHERE officeName = '$officeName'"));
    echo '
      <section class="container">
        <div class="middleContent">
          <a class="btn back" href="offices.php"> Takaisin</a>
          <div class="box">
            <h2>Uusi Toimisto/ryhmä luotu</h2>
            <div class="section">
              <table>
                <tr>
                  <td>Toimiston nimi:</td>
                  <td>'.$newOfficeID['officeName'].'</td>
                </tr>';
    $doneGroups = array();
    if (!empty($officeGroups)) {
      $i = 1;
      foreach($officeGroups as $group) {
        if(!empty($group) && !in_array($group, $doneGroups)){
          $groupdata = array("groupName" => $group, "officeID" => $newOfficeID['officeID']);
          tc_insert_strings("groups", $groupdata);
          array_push($doneGroups,$group);
          echo '<tr>
                  <td>Ryhmä '.$i.':</td>
                  <td>'.$group.'</td>
                </tr>';
        $i++;
        }
      }
    }
    echo '    </table>
            </div>
          </div>
        </div>
      </section>';
  }
}
?>
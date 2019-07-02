<?php
if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/employees/employees.php';</script>";
    exit;
}


$error = false;

if (isset($_POST['userID'])){
    $userID = $_POST['userID'];
}

$checkPermsID = $userID;
require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";

if ($_SESSION['logged_in_user']->level >= 2 && isset($_POST['newUserID'])){
    $newUserID = $_POST['newUserID'];
     // Checks if given (different) username already exists in database
    $userIDcheck = mysqli_fetch_row(tc_query( "SELECT userID FROM employees WHERE userID = '$newUserID' AND userID <> '$userID'"));
    if (!empty($userIDcheck)) {$error = true; $newUserID = "error";}
}

if (isset($_POST['level']) && $_SESSION['logged_in_user']->level >= 3) {$level = intval($_POST['level']);}
else {$level = 0;}

if (!isset($_POST['displayName']) || $_POST['displayName'] == "") {$error = true; $displayName = "error";}
else {$displayName = $_POST['displayName'];}

if ( isset($_POST['password']) && $_POST['password'] != "" && $level > 0) {
    $password = password_hash($_POST['password'].$salt, PASSWORD_DEFAULT);
}

if (!isset($_POST['group_name']) || $_POST['group_name'] == "") {$error = true; $groupID = "error";}
else {$groupID = $_POST['group_name'];}

if (isset($_POST['grouplist'])) {
    $grouplist = $_POST['grouplist'];
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


if ($error) {
    echo '<div class="box" style="background-color: var(--red); min-height: 50px; text-align: center; color: white; padding: 0;">';
    if ($displayName == "error") { echo '<p>Nimi oli tyhjä!</p>'; }
    if ($groupID == "error") { echo '<p>Et valinnut toimistoa ja/tai ryhmää!</p>'; }
    if ($newUserID == "error") { echo '<p>Käyttäjätunnus on varattu!</p>'; }
}
else {
    echo '<div class="box" style="background-color: var(--lightgreen); min-height: 50px; text-align: center; color: white; padding: 0;">';
    
    tc_update_strings("employees", array(
        'displayName'   => $displayName,
        'groupID'       => $groupID,
        'earliestStart' => $earliestStart,
        'latestEnd'     => $latestEnd
    ), "userID = ?", $userID);

    if ($_SESSION['logged_in_user']->level >= 3) {
        tc_update_strings("employees", array(
            'level'       => $level
        ), "userID = ?", $userID);

        if (isset($password)) {
            tc_update_strings("employees", array(
                'adminPassword' => $password
            ), "userID = ?", $userID);
        }

        if (($level == 1 || $level == 2)) {
            tc_delete("supervises", "userID = ?", $userID);
            foreach($grouplist as $grpid) {
              $groupdata = array("userID" => $userID, "groupID" => $grpid);
              tc_insert_strings("supervises", $groupdata);
            }
        }   
    }

    // updates the new userID to every table that has userIDs
    if ($_SESSION['logged_in_user']->level >= 2 && isset($newUserID) && ($newUserID != $userID) ) {
        tc_update_strings("employees", array(
            'userID' => $newUserID
        ), "userID = ?", $userID);
        tc_update_strings("info", array(
            'userID' => $newUserID
        ), "userID = ?", $userID);
        tc_update_strings("supervises", array(
            'userID' => $newUserID
        ), "userID = ?", $userID);
    $userID = $newUserID;
    }

    echo '<p>Muutokset tehtiin onnistuneesti!</p>';  
}
echo '</div>';


?>
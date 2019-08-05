<?php
if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/employees/employees.php';</script>";
    exit;
}


$errors = array();

if (isset($_POST['userID'])){
    $userID = $_POST['userID'];
}

$checkPermsID = $userID;
require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";

if ($_SESSION['logged_in_user']->level >= 2 && isset($_POST['newUserID'])){
    $newUserID = $_POST['newUserID'];
     // Checks if given (different) username already exists in database
    $userIDcheck = mysqli_fetch_row(tc_query( "SELECT userID FROM employees WHERE userID = '$newUserID' AND userID <> '$userID'"));
    if (empty($newUserID) || !empty($userIDcheck)) {array_push($errors, "Käyttäjätunnus on varattu tai se oli tyhjä!"); }
}

if (isset($_POST['level']) && $_SESSION['logged_in_user']->level >= 3) {$level = intval($_POST['level']);}
else {$level = 0;}

if (!isset($_POST['displayName']) || $_POST['displayName'] == "") {array_push($errors, "Nimi oli tyhjä!");}
else {$displayName = $_POST['displayName'];}

if ( isset($_POST['password']) && $_POST['password'] != "" && $level > 0) {
    $password = password_hash($_POST['password'].$salt, PASSWORD_DEFAULT);
}

if (!isset($_POST['group_name']) || $_POST['group_name'] == "") {array_push($errors, "Et valinnut toimistoa ja/tai ryhmää!");}
else {$groupID = $_POST['group_name'];}

if (isset($_POST['grouplist'])) {
    $grouplist = $_POST['grouplist'];
} else {
    $grouplist = array();
}

if (empty($_POST['earliest']) && empty($_POST['latest'])) {
    $earliestStart = null;
    $latestEnd = null;
}
else if ( empty($_POST['earliest']) != empty($_POST['latest']) ) {
    $earliestStart = null;
    $latestEnd = null;
    array_push($errors, "Mikäli haluat rajoittaa työaikoja, täytäthän molemmat rajat!");
} else {
    $earliestStart = $_POST['earliest'];
    $latestEnd =$_POST['latest'];
}

// Displays errors and cancels modifications
if (sizeof($errors) > 0) {
    foreach ($errors as &$error) {
        echo '<div class="box" style="background-color: var(--red); min-height: 50px; text-align: center; color: white; padding: 0;">';
        echo "<p>$error</p>";
        echo '</div>';
    }
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
    echo '</div>';
}



?>
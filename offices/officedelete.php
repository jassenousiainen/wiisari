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
    $officeData = mysqli_fetch_row(tc_query("SELECT * FROM offices WHERE officeID = ?",$officeID));
    $groupData = tc_query("SELECT * FROM groups WHERE officeID = ?",$officeID);
    $groupIDs = array();
    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="offices.php"> Takaisin</a>
            <div class="box">
                <h2>Toimisto poistettu</h2>
                <div class="section">
                    <p>Toimisto '.$officeData[1].' poistettu onnistuneesti.</p>';
                    if($_POST['groupDel'] === "yes"){
                        while ( $group = mysqli_fetch_array($groupData) ) {
                            echo '<p>Ryhmä '.$group[1].' poistettu onnistuneesti.</p>';
                            array_push($groupIDs, $group[0]);
                        }
                    }
                    if($_POST['userDel'] === "yes"){
                        echo '<p>Kaikki toimiston käyttäjät poistettu onnistuneesti.</p>';
                    }
                    echo'
                </div>
            </div>
        </div>
    </section>';
    tc_delete("offices", "officeID = ?", $officeID);
    if($_POST['groupDel'] === "yes") {
        tc_delete("groups", "officeID = ?", $officeID);
    }else{
        tc_update_strings("groups", array(
            'officeID' => 0,
          ), "officeID = ?", $officeID);
    }
    if($_POST['userDel'] === "yes" && !empty($groupIDs)){
        foreach ($groupIDs as $groupID) {
            tc_delete("employees", "groupID = ?", $groupID);
        }
    }else{
        foreach ($groupIDs as $groupID) {
            tc_update_strings("employees", array(
                'groupID' => 0,
              ), "groupID = ?", $groupIDs);
        }
    }
}

?>            
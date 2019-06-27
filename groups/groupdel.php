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


if (isset($_POST['groupID'])) {

    $groupID = $_POST['groupID'];
    $query = tc_query("SELECT * FROM groups WHERE groupID = ?",$groupID);
    if($query != FALSE){
        $groupData = mysqli_fetch_row($query);
    }

    tc_delete("groups", "groupID = ?", $groupID);
    if($_POST['userDel'] === "yes"){
            tc_delete("employees", "groupID = ?", $groupID);
    }else{
        tc_update_strings("employees", array(
            'groupID' => 0,
          ), "groupID = ?", $groupID);
    }
    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="groups.php"> Takaisin</a>
            <div class="box">
                <h2>Ryhmä poistettu</h2>
                <div class="section">;
                    <p>Ryhmä <b>';  
                    if(isset($groupData)){
                        echo $groupData[1];
                    }
                    echo '</b> poistettu onnistuneesti.</p>';
                    if($_POST['userDel'] === "yes"){
                        echo '<p>Kaikki ryhmän käyttäjät poistettu onnistuneesti.</p>';
                    }
               echo' </div>
            </div>
        </div>
    </section>';

}

?>            
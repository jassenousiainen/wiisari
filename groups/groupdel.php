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

    tc_delete("groups", "groupID = ?", $groupID);
    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="groups.php"> Takaisin</a>
            <div class="box">
                <h2>Ryhmä poistettu</h2>
                <div class="section">
                    <p>Ryhmä poistettu onnistuneesti.</p>
                </div>
            </div>
        </div>
    </section>';

}

?>            
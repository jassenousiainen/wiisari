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


if (isset($_POST['officeID'])) {

    $officeID = $_POST['officeID'];

    tc_delete("offices", "officeID = ?", $officeID);
    tc_delete("groups", "officeID = ?", $officeID);
    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="offices.php"> Takaisin</a>
            <div class="box">
                <h2>Toimisto poistettu</h2>
                <div class="section">
                    <p>Toimiston kaikki ryhmät poistettu onnistuneesti.</p>
                </div>
            </div>
        </div>
    </section>';

}

?>            
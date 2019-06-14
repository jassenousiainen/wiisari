<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Käyttäjän tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 2) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


if (isset($_POST['deleteuser'])) {

    $userID = $_POST['deleteuser'];

    require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php"; // This blocks access to rest of the page if supervisor doesn't have access to this groups employee

    tc_delete("employees", "userID = ?", $userID);
    tc_delete("info", "userID = ?", $userID);
    tc_delete("supervises", "userID = ?", $userID);   

    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";

    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="employees.php">Takaisin</a>
            <div class="box">
                <h2>Käyttäjä poistettu</h2>
                <div class="section">
                    <p>Henkilön kaikki tiedot poistettu onnistuneesti.</p>
                </div>
            </div>
        </div>
    </section>';

}

?>            
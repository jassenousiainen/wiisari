<?php
require '../common.php';
session_start();

include "$_SERVER[DOCUMENT_ROOT]/header.php";
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>WIISARI - Tulosta viivakoodeja</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if ($_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$supervisorID = $_SESSION['logged_in_user']->userID;

    if ($_SESSION['logged_in_user']->level >= 3) {
        $groupquery = tc_query( "SELECT groupName, officeName, groupID FROM groups NATURAL JOIN offices ORDER BY groupName;" );
    } else {
        $groupquery = tc_query( "SELECT groupName, officeName, groupID
                            FROM supervises NATURAL JOIN groups NATURAL JOIN offices 
                            WHERE userID = '$supervisorID'
                            ORDER BY groupName;" );
    }

    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="/mypage.php">Oma sivu</a>
            <div class="box">
                <h2>Valitse ryhmät viivakoodien tulostukseen</h2>
                <div class="section">
                    <p>Huomaa, että yksittäisen henkilön viivakoodin saat henkilöstö -sivulta</p>
                    <form action="'.$self.'" method="post">
                        <p>Valitse ryhmä:</p>
                        <select name="groupID">
                            <option value="all" selected>Kaikki ryhmäsi</option>';
    while ($grp = mysqli_fetch_array($groupquery)) {
        echo '              <option value="'.$grp['groupID'].'">'.$grp['groupName'].'</option>';
    }
    echo '              </select>
                        <br><br>
                        <button type="submit" class="btn send">Hae</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    ';

if (isset($_POST['groupID'])) {
    include "barcodeprinter.php";
}

?>
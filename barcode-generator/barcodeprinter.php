<?php
require '../common.php';
session_start();

echo "<title>WIISARI - Tulosta viivakoodeja</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if ($_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$supervisorID = $_SESSION['logged_in_user']->userID;


if ($request == "GET") {
    include "$_SERVER[DOCUMENT_ROOT]/header.php";
    include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

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


}
else if ($request == "POST") {
    include('src/BarcodeGenerator.php');
    include('src/BarcodeGeneratorPNG.php');
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();


    if (isset($_POST['groupID'])) {      
        if ($_POST['groupID'] == "all") {
            if ($_SESSION['logged_in_user']->level >= 3) {
                $groupID = array();
                $fetchquery = tc_query("SELECT groupID FROM groups");
                while ($row = mysqli_fetch_array($fetchquery)) {
                    array_push($groupID, $row[0]);
                }
            } else {
                $groupID = array();
                $fetchquery = tc_query("SELECT groupID FROM supervises WHERE userID = '$supervisorID'");
                while ($row = mysqli_fetch_array($fetchquery)) {
                    array_push($groupID, $row[0]);
                }
            }
        } else {
            $groupID = $_POST['groupID'];

            // Checks if selected group belongs to this supervisors groups
            if ( $_POST['groupID'] != "all" && $_SESSION['logged_in_user']->level < 3 && empty(mysqli_fetch_row(tc_query("SELECT * FROM supervises WHERE groupID ='$groupID' AND userID = '$supervisorID'"))) ) {
                echo "Sinulla ei ole oikeutta valittuun ryhmään";
                exit;
            } else {
                $groupID = array($groupID);
            }
        }
        $employee_query = tc_query("SELECT * FROM employees WHERE groupID IN (".implode(',',$groupID).") ORDER BY displayName");
    }
    else if (isset($_POST['userID'])) {
        $userID = $_POST['userID'];
        require "../grouppermissions.php";
        $employee_query = tc_query("SELECT * FROM employees WHERE userID = '$userID'");
    }
    else {
        echo "VIRHE! Ryhmää tai henkilöä ei valittu";
    }

    

    echo '
    <head>
        <link rel="stylesheet" href="/css/barcode-generator.css">
    </head>';
    if (isset($_POST['groupID'])) {
        echo '<a href="/barcode-generator/barcodeprinter.php" class="back">Takaisin</a>';
    } else {
        echo '<form action="/employees/employeeinfo.php" method="post">
                <button type="submit" class="back" name="userID" value="'.$userID.'">Takaisin</button>
            </form>';
    }

    while ($emp = mysqli_fetch_array($employee_query)) {
        $displayName = $emp['displayName'];
        $userID = $emp['userID'];

        echo '<div class="barcodeGenBox">
                <p class="wiisarilogo">WIISARI</p>
                <p class="name">'.$displayName.'</p>';
        echo '  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($userID, $generator::TYPE_CODE_128, 2, 60)) . '">';
        echo '  <p class="code">'.$userID.'</p>
                <p class="tstlogo">Turun Seudun TST ry</p>';
        echo '</div>';
    }
}


?>
<?php

if ($_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

    include('src/BarcodeGenerator.php');
    include('src/BarcodeGeneratorSVG.php');
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    $supervisorID = $_SESSION['logged_in_user']->userID;

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
    else if (isset($_POST['userBarcode'])) {
        $userBarcode = $_POST['userBarcode'];
        $checkPermsID = $userBarcode;
        require "../grouppermissions.php";
        $employee_query = tc_query("SELECT * FROM employees WHERE userID = '$userBarcode'");
    }
    else {
        echo "VIRHE! Ryhmää tai henkilöä ei valittu";
    }

    

    echo '
    <div class="print-box">';

    while ($emp = mysqli_fetch_array($employee_query)) {
        $displayName = $emp['displayName'];
        $userBarcode = $emp['userID'];

        echo '<div class="barcodeGenBox">
                <p class="wiisarilogo">WIISARI</p>
                <p class="name">'.$displayName.'</p>';
        echo    $generator->getBarcode($userBarcode, $generator::TYPE_CODE_128, 2, 60);
        echo '  <p class="code">'.$userBarcode.'</p>
                <p class="tstlogo">Turun Seudun TST ry</p>';
        echo '</div>';
    }
    echo '</div>
          <script>window.print()</script>';


?>
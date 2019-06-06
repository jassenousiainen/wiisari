<?php
if (!isset($_SESSION['logged_in_user']) || !$_SESSION['logged_in_user']->isSuperior()) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


$error = false;

if (!isset($_POST['admin'])) { $adminrights = 0; }
else { $adminrights = 1; }

if (!isset($_POST['reports'])) { $reportrights = 0; }
else { $reportrights = 1; }

if (!isset($_POST['time_admin'])) { $timerights = 0; }
else { $timerights = 1; }

if (!isset($_POST['displayname']) || $_POST['displayname'] == "") {$error = true; $displayname = "error";}
else {$displayname = $_POST['displayname'];}
/*
if ( (!isset($_POST['password']) || $_POST['password'] == "") && ($adminrights == 1 || $reportrights == 1 || $timerights == 1) ) {$error = true; $password = "error";}
else {$password = $_POST['password'];}
*/
if (!isset($_POST['barcode']) || $_POST['barcode'] == "") {$error = true; $barcode = "error";}
else {
    $barcode = $_POST['barcode'];
    $barcodecheck = mysqli_fetch_row(tc_query( "SELECT barcode FROM employees WHERE barcode = '$barcode' AND empfullname <> '$empfullname'"));
    if (!empty($barcodecheck)) {$error = true; $barcode = "error";}
}

if (!isset($_POST['office_name']) || $_POST['office_name'] == "") {$error = true; $office = "error";}
else {$office = $_POST['office_name'];}

if (!isset($_POST['group_name']) || $_POST['group_name'] == "") {$error = true; $group = "error";}
else {$group = $_POST['group_name'];}



if ($error) {
    echo '<div class="box" style="background-color: var(--red); min-height: 50px; text-align: center; color: white; padding: 0;">';
    if ($displayname == "error") { echo '<p>Nimi oli tyhjä!</p>'; }
    if ($barcode == "error") { echo '<p>Viivakoodissa oli virhe tai se oli varattu!</p>'; }
    if ($office == "error" || $group == "error") { echo '<p>Et valinnut toimistoa ja/tai ryhmää!</p>'; }
}
else {
    echo '<div class="box" style="background-color: var(--lightgreen); min-height: 50px; text-align: center; color: white; padding: 0;">';
    tc_update_strings("employees", array(
        'displayname' => $displayname,
        'barcode'     => $barcode,
        'groups'      => $group,
        'office'      => $office
    ), "empfullname = ?", $empfullname);

    if ($_SESSION['logged_in_user']->admin == 1) {
        tc_update_strings("employees", array(
            'admin'       => "$adminrights",
            'reports'     => "$reportrights",
            'time_admin'  => "$timerights"
        ), "empfullname = ?", $empfullname);
    }
    echo '<p>Muutokset tehtiin onnistuneesti!</p>';  
}
echo '</div>';


?>
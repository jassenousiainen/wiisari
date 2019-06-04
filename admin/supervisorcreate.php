<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Kellotuseditori</title>\n";

include 'header_get.php';

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->admin == 0) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$error = false;

if  ( isset($_POST['create']) ) {
    
    // Chekcs the input from the form
    if (!isset($_POST['empfullname']) || $_POST['empfullname'] == "" ) {$error = true; $empfullname = "error";}
    else {
        $empfullname = $_POST['empfullname'];
        $empfullnamecheck = mysqli_fetch_row(tc_query( "SELECT empfullname FROM employees WHERE empfullname = '$empfullname'"));
        if (!empty($empfullnamecheck)) {$error = true; $empfullname = "error";}
    }

    if (!isset($_POST['displayname']) || $_POST['displayname'] == "") {$error = true; $displayname = "error";}
    else {$displayname = $_POST['displayname'];}

    if (!isset($_POST['password']) || $_POST['password'] == "") {$error = true; $password = "error";}
    else {$password = $_POST['password'];}

    if (!isset($_POST['barcode']) || $_POST['barcode'] == "") {$error = true; $barcode = "error";}
    else {
        $barcode = $_POST['barcode'];
        $barcodecheck = mysqli_fetch_row(tc_query( "SELECT barcode FROM employees WHERE barcode = '$barcode'"));
        if (!empty($barcodecheck)) {$error = true; $barcode = "error";}
    }

    if (!isset($_POST['office_name']) || $_POST['office_name'] == "") {$error = true; $office = "error";}
    else {$office = $_POST['office_name'];}

    if (!isset($_POST['group_name']) || $_POST['group_name'] == "") {$error = true; $group = "error";}
    else {$group = $_POST['group_name'];}


    // Displays the form again with input fields that contained errors
    if ($error) {
        echo '<section class="container">
            <div class="middleContent">
            <div class="box">
                <h2>Luo uusi valvoja (luonnissa tapahtui virhe!)'.$_POST['group_name'].'</h2>
                <div class="section">
                <form name="form" action="'.$self.'" method="post">
                <table>
                <tbody>';
        if ($empfullname == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Käyttäjätunnus oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Käyttäjätunnus:</td>
                        <td><input name="empfullname" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Järjestelmän sisäiseen käyttöön</td>
                    </tr>';
        } else {
            echo '<input name="empfullname" value="'.$empfullname.'" type="hidden">';
        }
        if ($displayname == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Nimi oli tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Nimi:</td>
                        <td><input name="displayname" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Henkilön nimi</td>
                    </tr>';
        } else {
            echo '<input name="displayname" value="'.$displayname.'" type="hidden">';
        }
        if ($password == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Salasana oli tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Salasana:</td>
                        <td><input name="password" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Tällä valvoja kirjautuu järjestelmään</td>
                    </tr>';
        } else {
            echo '<input name="password" value="'.$password.'" type="hidden">';
        }
        if ($barcode == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Viivakoodi oli varattu tai tyhjä, täytäthän sen uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Viivakoodi:</td>
                        <td><input name="barcode" type="text" required="true"></td>
                        <td style="color: grey; font-size: 12px;">Tällä valvoja kellottaa itsensä töihin</td>
                    </tr>';
        } else {
            echo '<input name="barcode" value="'.$barcode.'" type="hidden">';
        }
        if ($office == "error" || $group == "error") {
            echo '  <tr>
                        <td class="errortext" colspan="3">Et valinnut toimistoa tai ryhmää, valitsethan uudelleen:</td>
                    </tr>
                    <tr>
                        <td>Toimisto:</td>
                        <td><select name="office_name" onchange="group_names();" required="true"></select></td>
                        <td style="color: grey; font-size: 12px;">Toimisto, johon valvoja kuuluu</td>
                    </tr>';
        
            echo '  <tr>
                        <td>Ryhmä:</td>
                        <td><select name="group_name" required="true"></select></td>
                        <td style="color: grey; font-size: 12px;">Ryhmä, johon valvoja kuuluu</td>
                    </tr>';
        } else {
            echo '<input name="office_name" value="'.$office.'" type="hidden">';
            echo '<input name="group_name" value="'.$group.'" type="hidden">';
        }
        echo '
                    <tr>
                        <td><button name="create" type="submit" class="btn">Jatka</button></td>
                    </tr>
                </tbody>
                </table>
            </form>
        </div>
        </div>
    </section>';
    }

}
else if ( isset($_POST['send']) ) {

}


?>
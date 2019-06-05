<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Kellotuseditori</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->admin == 0) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

$error = false;

if  ( isset($_POST['create']) ) {
    
    // Chekcs the input for errors
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

    if (!isset($_POST['admin'])) { $adminrights = 0; }
    else { $adminrights = 1; }

    if (!isset($_POST['reports'])) { $reportrights = 0; }
    else { $reportrights = 1; }

    if (!isset($_POST['time_admin'])) { $timerights = 0; }
    else { $timerights = 1; }

    // Displays the form again with input fields that contained errors
    if ($error) {
        echo '<section class="container">
            <div class="middleContent">
            <div class="box">
                <h2>Luo uusi valvoja (luonnissa tapahtui virhe!)</h2>
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
    else {  // This part is run only if all inputs have been checked to be of correct form

        
        //$super = array("empfullname" => $empfullname, "employee_passwd" => $password, "displayname" => $displayname, "barcode" => $barcode, "groups" => $groups, "office" => $office);
        //tc_insert_strings("employee", $super);

        $groupquery = tc_query( "SELECT groupname, officename, groupid FROM groups NATURAL JOIN offices ORDER BY officename;" );

        echo '<section class="container">
            <div class="middleContent">
                <div class="box">
                    <h2>Valitse henkilön '.$displayname.' valvottavat ryhmät</h2>
                    <div class="section">
                        <form name="form" action="'.$self.'" method="post">
                            <button type="submit" name="send" value="'.$empfullname.'" class="btn" style="float:right; margin-bottom: 10px;">Vahvista ryhmät</button>
                            <table class="sorted">
                                <thead>
                                    <tr>
                                        <th data-placeholder="Hae toimistoa">Toimisto</th>
                                        <th data-placeholder="Hae ryhmää">Ryhmä</th>
                                        <th class="sorter-false filter-false">Valitse</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr style="height: 20px;"></tr>
                                    <tr class="tablesorter-ignoreRow">
                                        <th colspan="3" class="ts-pager form-horizontal">
                                        <button type="button" class="btn first"><i class="fas fa-angle-double-left"></i></button>
                                        <button type="button" class="btn prev"><i class="fas fa-angle-left"></i></button>
                                        <span class="pagedisplay"></span>
                                        <button type="button" class="btn next"><i class="fas fa-angle-right"></i></button>
                                        <button type="button" class="btn last"><i class="fas fa-angle-double-right"></i></button>
                                        </th>
                                    </tr>
                                    <tr class="tablesorter-ignoreRow">
                                        <th colspan="3" class="ts-pager form-horizontal">
                                        max rivit: <select class="pagesize browser-default" title="Select page size">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option selected="selected" value="30">30</option>
                                            <option value="40">40</option>
                                            <option value="all">Kaikki rivit</option>
                                        </select>
                                        sivu: <select class="pagenum browser-default" title="Select page number"></select>
                                        </th>
                                    </tr>
                                </tfoot>
                                <tbody>';
            
                while ( $group = mysqli_fetch_array($groupquery) ) {
            
            echo '                  <tr>
                                        <td>'.$group[0].'</td>
                                        <td>'.$group[1].'</td>
                                        <td style="text-align:center;">
                                            <label class="container">
                                            <input type="checkbox" name="grouplist[]" value='.$group[2].' class="check">
                                            <span class="checkmark"></span>
                                            </label>
                                        </td>
                                    </tr>';
                }
            echo '              </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </section>';

        echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
    }

}
else if ( isset($_POST['send']) ) {
    echo '<section class="container">
            <div class="middleContent">
                <div class="box">
                    <h2>Valitse henkilön '.$displayname.' valvottavat ryhmät</h2>
                    <div class="section">
                    </div>
                </div>
            </div>
        </section>';
}


?>
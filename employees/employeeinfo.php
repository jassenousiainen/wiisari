<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Käyttäjän tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || !$_SESSION['logged_in_user']->isSuperior()) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


if (isset($_POST['empfullname'])) {

    $empfullname = $_POST['empfullname'];
    
    require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";

    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";

    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="employees.php"> Takaisin</a>';

    // Updates the edited info to database and shows that the edit was successful
    if (isset($_POST['editinfo'])) {
        require "$_SERVER[DOCUMENT_ROOT]/employees/employee-edit.php";
    }

    $empdata = mysqli_fetch_row(tc_query("SELECT * FROM employees WHERE empfullname = '$empfullname'"));

    echo '
            <div class="box">
                <h2>Henkilön '.$empdata[3].' tiedot</h2>
                <div class="section">
                    <p>Henkilön työtiedot:</p>';
    if ($empdata[12] == 'in') {
        $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE fullname = '$empdata[0]' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
        $currentWorkTime = time() - (int)$lastIn;
        echo '      <div class="tile" style="background-color: var(--green); color: white;"><i class="fas fa-user-check"></i><span>Töissä</span></div>
                    <p>Tuli töihin: klo '.date('H:i j.n.Y', $lastIn).'';
    } else {
        echo '      <div class="tile" style="background-color: var(--red); color: white;"><i class="fas fa-times"></i><span>Poissa töistä</span></div>';
    }
    echo '
                </div>
                <div class="section">
                    <p>Muokkaa tämän henkilön työaikoja:</p>
                    <form action="time_editor" method="post">
                        <button class="btn" type="submit" name="timeeditor" value="'.$empdata[0].'">Kellotuseditoriin</button>
                    </form>
                </div>
                <div class="section">
                    <p>Henkilötiedot:</p>
                    <form name="form" action="'.$self.'" method="post">
                        <input type="hidden" name="editinfo" value="edit"></input>
                        <table style="max-width: 600px;">
                            <tr>
                                <td>Käyttäjätunnus:</td>
                                <td>'.$empdata[0].'<input name="empfullname" value="'.$empdata[0].'" type="hidden"></td>
                            </tr>
                            <tr>
                                <td>Nimi:</td>
                                <td><input type="text" name="displayname" value="'.$empdata[3].'"></input></td>
                            </tr>
                            <tr>
                                <td>Viivakoodi:</td> 
                                <td><input type="text" name="barcode" value="'.$empdata[5].'"></input></td>
                            </tr>
                            <tr>
                                <td>Toimisto:</td>
                                <td>
                                    <select name="office_name" onfocus="office_names();" onchange="group_names();" required="true">
                                        <option selected>'.$empdata[7].'</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Ryhmä:</td>
                                <td>
                                    <select name="group_name" required="true" onfocus="group_names();">
                                        <option selected>'.$empdata[6].'</opiton>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Adminoikeudet:</td>
                                <td>';
    if ($_SESSION['logged_in_user']->admin == 1) {  // Only admin user can adjust permissions
        echo '                      <label class="container">';
        if ($empdata[8] == "1") {echo '<input type="checkbox" name="admin" value="1" class="check" id="admin" checked>';} 
        else {echo '<input type="checkbox" name="admin" value="1" class="check" id="admin">';}
        echo '                          <span class="checkmark"></span>
                                    </label>';
    } else {
        if ($empdata[8] == "1") {echo 'kyllä';} else {echo 'ei';}
    }
    echo '                      </td>
                            </tr>
                            <tr>
                                <td>Raporttioikeudet:</td>
                                <td>';
    if ($_SESSION['logged_in_user']->admin == 1) {  // Only admin user can adjust permissions
        echo '                      <label class="container">';
        if ($empdata[9] == "1") {echo '<input type="checkbox" name="reports" value="1" class="check" id="reports" checked>';} 
        else {echo '<input type="checkbox" name="reports" value="1" class="check" id="reports">';}
        echo '                          <span class="checkmark"></span>
                                    </label>';
    } else {
        if ($empdata[9] == "1") {echo 'kyllä';} else {echo 'ei';}
    }
    echo '                      </td>
                            </tr>
                            <tr>
                                <td>Editorioikeudet:</td>
                                <td>';
    if ($_SESSION['logged_in_user']->admin == 1) {  // Only admin user can adjust permissions
        echo '                      <label class="container">';
        if ($empdata[10] == "1") {echo '<input type="checkbox" name="time_admin" value="1" class="check" id="time" checked>';} 
        else {echo '<input type="checkbox" name="time_admin" value="1" class="check" id="time">';}
        echo '                          <span class="checkmark"></span>
                                    </label>';
    } else {
        if ($empdata[10] == "1") {echo 'kyllä';} else {echo 'ei';}
    }
    echo '                      </td>
                            </tr>';
    if ($_SESSION['logged_in_user']->admin == 1) {  // Only admin user can adjust password
        echo '              <tr id="password">
                                <td>Salasana:</td>
                                <td><input name="password" type="text" placeholder="kirjoita uusi salasana"></td>
                            </tr>';
    }  
    echo '              </table>';

    if ($_SESSION['logged_in_user']->admin == 1 && $empdata[8] != 1) { // Only admin user can alter supervised groups

        $groupquery = tc_query( "SELECT groupname, officename, groupid FROM groups NATURAL JOIN offices ORDER BY groupname;" );

        echo '          <p class="chooseGroups"><b>Valitse valvottavat ryhmät:</b></p>
                        <table class="sorted chooseGroups">
                            <thead>
                                <tr>
                                    <th data-placeholder="Hae ryhmää">Ryhmä</th>
                                    <th data-placeholder="Hae toimistoa">Toimisto</th>
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
            
                    $issupervised = mysqli_fetch_row(tc_query("SELECT groupid FROM supervises WHERE fullname = '$empdata[0]' AND groupid = '$group[2]'"));

                    echo '      <tr>
                                    <td>'.$group[0].'</td>
                                    <td>'.$group[1].'</td>
                                    <td style="text-align:center;">
                                        <label class="container">';
                    if (!empty($issupervised)) {echo '<input type="checkbox" name="grouplist[]" value='.$group[2].' class="check" checked>';}
                    else {echo '<input type="checkbox" name="grouplist[]" value='.$group[2].' class="check">';}                        
                    echo '                      <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>';
                }
            echo '          </tbody>
                        </table>';

        echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
    }
    echo '
                        <br>
                        <br><button name="editinfo" type="submit" class="btn">Muuta tietoja <i class="fas fa-paper-plane"></i></button>
                     </form>
                </div>
            </div>
        </div>
    </section>';


}

?>
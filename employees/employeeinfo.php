<?php

include "$_SERVER[DOCUMENT_ROOT]/header.php";
session_start();
include "$_SERVER[DOCUMENT_ROOT]/topmain.php";

echo "<title>Käyttäjän tiedot</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}


if (isset($_POST['userID'])) {

    $userID = $_POST['userID'];

    $checkPermsID = $userID;
    require "$_SERVER[DOCUMENT_ROOT]/grouppermissions.php";     // This blocks access to rest of the page if supervisor doesn't have access to this groups employee

    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get.php";

    // Generates barcode for printing
    if (isset($_POST['userBarcode'])) {
        include "../barcode-generator/barcodeprinter.php";
    }

    echo '
    <section class="container">
        <div class="middleContent">
            <a class="btn back" href="employees.php">Takaisin</a>';

    // Updates the edited info to database and shows that the edit was successful or it contained errors
    if (isset($_POST['editinfo'])) {
        require "$_SERVER[DOCUMENT_ROOT]/employees/employee-edit.php";
    }

    $empdata = mysqli_fetch_array(tc_query("SELECT * FROM employees WHERE userID = '$userID'"));
    $grpdata = mysqli_fetch_row(tc_query("SELECT groupName, officeName , groupID
                                        FROM employees NATURAL JOIN groups NATURAL JOIN offices
                                        WHERE userID = '$empdata[0]';
                                        "));
    echo '
            <div class="box">
                <h2>Henkilön '.$empdata[1].' tiedot</h2>
                <div class="section">';
    if ($empdata[5] == 'in') {
        $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE userID = '$empdata[0]' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
        $currentWorkTime = time() - (int)$lastIn;
        $lastInStr = new DateTime("@$lastIn");
        $lastInStr->setTimeZone(new DateTimeZone($timezone));
        echo '      <div class="tile" style="background-color: var(--green); color: white;"><i class="fas fa-user-check"></i><span>Töissä</span></div>
                    <p>Tuli töihin: '.$lastInStr->format("d.m.Y H:i");
    } else {
        $lastOut = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE userID = '$empdata[0]' AND `inout` = 'out' ORDER BY timestamp DESC"))[0];
        echo '      <div class="tile" style="background-color: var(--red); color: white;"><i class="fas fa-user-times"></i><span>Poissa töistä</span></div>';
        if (!empty($lastOut)) {
            $lastOutStr = new DateTime("@$lastOut");
            $lastOutStr->setTimeZone(new DateTimeZone($timezone));
            echo '<p>Lähti töistä: '.$lastOutStr->format("d.m.Y H:i");
        }
    }
    echo '      </div>';

    if ($_SESSION['logged_in_user']->level >= 2) {
        echo '  <div class="section">
                    <p>Työtuntiraportti:</p>';
        echo "      <form name='form' action='/reports/personalreport.php' method='post' onsubmit=\"return isFromOrToDate();\">
                        <input type='text' id='from' autocomplete='off' size='10' maxlength='10' name='from_date' placeholder='välin alku' required> -
                        <input type='text' id='to' value='".date("d.n.Y")."'' autocomplete='off' size='10' maxlength='10' name='to_date' placeholder='välin loppu' required>
                        <br><br>
                        <label class='switch'>
                            Näytä yksittäiset kirjaukset
                            <input type='checkbox' name='tmp_show_details' value='1' class='check'>
                            <span class='slider'></span>
                        </label>
                        <br><br>
                        <button class='btn' type='submit' name='single_user_report' value='".$empdata[0]."'><i class='fas fa-hourglass-half'></i> Hae työtuntiraportti</button>
                    </form>
                </div>";
        echo '  <div class="section">
                    <p>Muokkaa henkilön työaikoja/kellotuksia:</p>      
                    <form action="time_editor" method="post">
                        <button class="btn" type="submit" name="timeeditor" value="'.$empdata[0].'"><i class="fas fa-user-clock"></i> Kellotuseditoriin</button>
                    </form>
                </div>';
    }

    echo '
                <div class="section">
                    <p>Generoi henkilölle tulostettava viivakoodi:</p>
                    <form action="'.$self.'" method="post">
                        <button class="btn" type="submit" name="userBarcode" value="'.$empdata[0].'"><i class="fas fa-barcode"></i> Hae</button>
                        <input type="hidden" name="userID" value="'.$empdata[0].'">
                        <a class="btn" href="../dowload.php?text='.$empdata[0].'"><i class="fas fa-download"></i> Lataa SVG</a>
                    </form>
                </div>
    ';
    
    echo '      <div class="section">
                    <p>Henkilötiedot:</p>
                    <form name="form" action="'.$self.'" method="post">
                        <input type="hidden" name="editinfo" value="edit"></input>
                        <table style="max-width: 600px;">
                            <tr>
                                <td>Käyttäjätunnus:</td>
                                <td>';
    if ($_SESSION['logged_in_user']->level >= 2) {
        echo '                      <input type="text" name="newUserID" value="'.$empdata[0].'"></input>';
    } else {
        echo $empdata[0];
    }
    echo '                      </td>
                                <input name="userID" value="'.$empdata[0].'" type="hidden">
                            </tr>
                            <tr>
                                <td>Nimi:</td>
                                <td><input type="text" name="displayName" value="'.$empdata[1].'"></input></td>
                            </tr>
                            <tr>
                                <td>Toimisto:</td>
                                <td>
                                    <select name="office_name" onfocus="office_names();" onchange="group_names();" required="true">
                                        <option selected>'.$grpdata[1].'</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Ryhmä:</td>
                                <td>
                                    <select name="group_name" required="true" onfocus="group_names();">
                                        <option value="'.$grpdata[2].'" selected>'.$grpdata[0].'</opiton>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                      <td><br></td>
                    </tr>
                    <tr>
                        <td>Aikaisin tuloaika</td>
                        <td><input name="earliest" type="time" value="'.$empdata['earliestStart'].'"></td>
                    </tr>
                    <tr>
                        <td>Myöhäisin lähtöaika</td>
                        <td><input name="latest" type="time" value="'.$empdata['latestEnd'].'"></td>
                    </tr>
                    <tr>
                        <td><br></td>
                    </tr>';

    if ($_SESSION['logged_in_user']->level >= 3) {  // Only admin can adjust users permission level and password
        $lvl0 = ""; $lvl1 = ""; $lvl2 = ""; $lvl3 = "";
        if ($empdata[3] == 0) {$lvl0 = "checked";}
        else if ($empdata[3] == 1) {$lvl1 = "checked";}
        else if ($empdata[3] == 2) {$lvl2 = "checked";}
        else if ($empdata[3] == 3) {$lvl3 = "checked";}

        echo '              <tr>
                                <td>(taso 0) Työntekijä:</td>
                                <td>
                                    <label class="container">
                                        <input type="radio" name="level" value="0" class="check" '.$lvl0.'>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>(taso 1) Normaali valvoja:</td>
                                <td>
                                    <label class="container">
                                        <input type="radio" name="level" value="1" class="check" id="reports" '.$lvl1.'>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>(taso 2) Valvoja + editointi:</td>
                                <td>
                                    <label class="container">
                                        <input type="radio" name="level" value="2" class="check" id="editor" '.$lvl2.'>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>(taso 3) Admin:</td>
                                <td>
                                    <label class="container">
                                        <input type="radio" name="level" value="3" class="check" id="admin" '.$lvl3.'>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr id="password">
                                <td>Salasana:</td>
                                <td><input name="password" type="text" placeholder="kirjoita uusi salasana"></td>
                            </tr>';
    } else {
        $lvl = "";
        if ($empdata[3] == 0) {$lvl = "Työntekijä (taso 0)";}
        else if ($empdata[3] == 1) {$lvl = "Normaali valvoja (taso 1)";}
        else if ($empdata[3] == 2) {$lvl = "Valvoja + editointi (taso 2)";}
        else if ($empdata[3] == 3) {$lvl = "Admin (taso 3)";}

        echo '              <tr>
                                <td>Oikeustaso:</td>
                                <td>'.$lvl.'</td>
                            </tr>';
    }
    echo '                      </table>';

    if ($_SESSION['logged_in_user']->level >= 3) { // Only admin user can alter supervised groups

        $groupquery = tc_query( "SELECT groupName, officeName, groupID FROM groups NATURAL JOIN offices ORDER BY groupName;" );

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
            
                    $issupervised = mysqli_fetch_row(tc_query("SELECT groupID FROM supervises WHERE userID = '$empdata[0]' AND groupID = '$group[2]'"));

                    echo '      <tr>
                                    <td>'.$group[0].'</td>
                                    <td>'.$group[1].'</td>
                                    <td style="text-align:center;">
                                        <label class="switch">';
                    if (!empty($issupervised)) {echo '<input type="checkbox" name="grouplist[]" value='.$group[2].' class="check" checked>';}
                    else {echo '<input type="checkbox" name="grouplist[]" value='.$group[2].' class="check">';}                        
                    echo '                      <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>';
                }
            echo '          </tbody>
                        </table>';

        echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
    }
    echo '              <br><br>';
    if ($_SESSION['logged_in_user']->level >= 2) {
        echo '          <button name="editinfo" type="submit" class="btn send">Muuta tietoja</button>';
    }
    echo '          </form>
                </div>';

    if ($_SESSION['logged_in_user']->level >= 2) {
        echo '  <div class="section">
                    <p><b>Poista käyttäjä:</b></p>
                    <p>Henkilötietojen poiston lisäksi kaikki muu tieto, kuten kellotukset poistetaan.</p>';?>
                    <form action="/employees/employeedelete.php" method="post" onsubmit="return confirm('Oletko varma että haluat poista käyttän?');">
        <?php echo '   <button name="deleteuser" value="'.$userID.'" type="submit" class="btn del trash">Poista</button>
                    </form>
                 </div>';
    }

    echo '  </div>
        </div>
    </section>';


}

?>
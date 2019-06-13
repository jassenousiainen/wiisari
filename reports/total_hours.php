<?php
require '../common.php';
session_start();

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];
$current_page = "total_hours.php";



if (!isset($tzo)) {
    settype($tzo, "integer");
    if (isset($_COOKIE['tzoffset'])) {
        $tzo = $_COOKIE['tzoffset'];
        $tzo = $tzo * 60;
     } else {
         $tzo = 0;
     }
}

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 1) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

include 'header_get_reports.php';

echo "<title>Työtunnit</title>\n";

include 'topmain.php';


$supervisorID = $_SESSION['logged_in_user']->userID;

if ($_SESSION['logged_in_user']->level >= 3) {
    $groupquery = tc_query( "SELECT groupName, officeName, groupID FROM groups NATURAL JOIN offices ORDER BY groupName;" );
} else {
    $groupquery = tc_query( "SELECT groupName, officeName, groupID
                        FROM supervises NATURAL JOIN groups NATURAL JOIN offices 
                        WHERE userID = '$supervisorID'
                        ORDER BY groupName;" );
}

if ($request == 'GET') {

    echo '
    <section class="container">
        <div class="middleContent">
            <div class="box">
                <h2>Hae työtuntiraportti</h2>
                <div class="section">
                    <form name="form" action="'.$self.'" method="post">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Valitse ryhmä</td>
                                    <td>
                                        <select name="groupID">';
    while ($grp = mysqli_fetch_array($groupquery)) {
        echo '                              <option value="'.$grp['groupID'].'">'.$grp['groupName'].'</option>';
    }
    echo '                              </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alku pvm:</td>
                                    <td><input id="from" autocomplete="off" type="text" size="10" maxlength="10" name="from_date"></td>
                                </tr>
                                <tr>
                                    <td>Loppu pvm:</td>
                                    <td><input id="to" autocomplete="off" type="text" size="10" maxlength="10" name="to_date"></td>
                                </tr>
                                <tr>
                                    <td>Hae raportit CSV -tiedostoon: </td>
                                    <td>
                                        <label class="container">
                                            <input type="checkbox" name="csv" value="1" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Näytä yksittäiset kirjaukset</td>
                                    <td>
                                        <label class="container">
                                            <input type="checkbox" name="tmp_show_details" value="1" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                </tr>
                                <tr>
                                    <td>Pyöristä työajat:</td>
                                </tr>
                                <tr>
                                    <td>Lähimpään 5 minuuttiin</td>
                                    <td>
                                        <label class="container">
                                            <input type="radio" name="tmp_round_time" value="1" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lähimpään 10 minuuttiin</td>
                                    <td>
                                        <label class="container">
                                            <input type="radio" name="tmp_round_time" value="2" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lähimpään 15 minuuttiin</td>
                                    <td>
                                        <label class="container">
                                            <input type="radio" name="tmp_round_time" value="3" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lähimpään 20 minuuttiin</td>
                                    <td>
                                        <label class="container">
                                            <input type="radio" name="tmp_round_time" value="4" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lähimpään 30 minuuttiin</td>
                                    <td>
                                        <label class="container">
                                            <input type="radio" name="tmp_round_time" value="5" class="check">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Älä pyöristä</td>
                                    <td>
                                        <label class="container">
                                            <input type="radio" name="tmp_round_time" value="0" class="check" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <button class="btn" type="submit" name="submit" value="Edit Time">Hae raportti <i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>';

    exit;

} else {

    echo '
    <section class="container">
        <div class="middleContent extraWide">
        <a href="/reports/total_hours.php" class="btn back"> Takaisin</a>';


    // ===== POST VALIDATION ===== //
    
    $errors = array();

    $groupID = $_POST['groupID'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    if (isset($_POST['tmp_round_time'])) {
        $tmp_round_time = $_POST['tmp_round_time'];
    } else {
        $tmp_round_time = "error";
    }
    
    $tmp_paginate = one_or_empty(@$_POST['tmp_paginate']);
    $tmp_show_details = one_or_empty(@$_POST['tmp_show_details']);
    $tmp_display_ip = one_or_empty(@$_POST['tmp_display_ip']);
    $tmp_display_office = one_or_empty(@$_POST['tmp_display_office']);
    $tmp_csv = one_or_empty(@$_POST['csv']);

    
    // Displays error incase user selected group that doesn't exist
    if (($groupID != "all") && (!empty($groupID))) {
        $group_name = tc_select_value("groupName", "groups", "groupID = ?", $groupID);
        if (!isset($group_name)) {
            array_push($errors, "Valittua ryhmää ei löytynyt");
        }
    } else if (empty($groupID)) {
        array_push($errors, "Et valinnut ryhmää");
    }

    // Checks if selected group belongs to this supervisors groups
    if ( $groupID != "all" && $_SESSION['logged_in_user']->level < 3 && empty(mysqli_fetch_row(tc_query("SELECT * FROM supervises WHERE groupID ='$groupID' AND userID = '$supervisorID'"))) ) {
        array_push($errors, "Sinulla ei ole oikeutta valittuun ryhmään");
    } 
    else if ( $groupID != "all" && $_SESSION['logged_in_user']->level >= 3 && empty(mysqli_fetch_row(tc_query("SELECT * FROM groups WHERE groupID ='$groupID'"))) ) {
        array_push($errors, "Valittua ryhmää ei löytynyt");
    }

    // Checks rounding for errors
    if (!empty($tmp_round_time) && 
        $tmp_round_time != '1' && 
        $tmp_round_time != '2' &&
        $tmp_round_time != '3' && 
        $tmp_round_time != '4' && 
        $tmp_round_time != '5') 
    {
        array_push($errors, "Pyöristys valittu virheellisesti");    
    }

    // Checks the from-date for errors
    if (empty($from_date)) {
        array_push($errors, "Alku päivämäärä oli tyhjä");
    } elseif (preg_match('#^[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}$#', $from_date) === 0) {
        array_push($errors, "Alku päivämäärä oli annettu virheellisessä muodossa");
    } else {
        $split_from = explode('.', $from_date);
        $from_month = $split_from[1];
        $from_day = $split_from[0];
        $from_year = $split_from[2];

        if ($from_month > 12 || $from_day > 31) {
            array_push($errors, "Alku päivämäärä oli annettu virheellisessä muodossa");
        }
    }


    // Checks the to-date for errors
    if (empty($to_date)) {
        array_push($errors, "Lopetus päivämäärä oli tyhjä");
    } elseif (preg_match('#^[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}$#', $to_date) === 0) {
        array_push($errors, "Lopetus päivämäärä oli annettu virheellisessä muodossa");
    } else {
        $split_to = explode('.', $to_date);
        $to_month = $split_to[1];
        $to_day = $split_to[0];
        $to_year = $split_to[2];

        if ($to_month > 12 || $to_day > 31) {
            array_push($errors, "Lopetus päivämäärä oli annettu virheellisessä muodossa");
        }
    }


    // Convert the datestrings to timestamps
    if (!empty($from_date)) {
        $from_date = "$from_month/$from_day/$from_year";
        $from_timestamp = strtotime($from_date);
        $from_date = $_POST['from_date'];
    }

    if (!empty($to_date)) {
        $to_date = "$to_month/$to_day/$to_year";
        $to_timestamp = strtotime($to_date . " 23:59") + 60;
        $to_date = $_POST['to_date'];
    }

    if ( !empty($from_timestamp) && !empty($to_timestamp) && $from_timestamp >= $to_timestamp) {
        array_push($errors, "Päivämäärät annettu väärässä järjestyksessä");
    }


    // This part is run only if the input contained any errors
    if (sizeof($errors) > 0) {
        foreach ($errors as &$error) {
            echo '<div class="box" style="background-color: var(--red); min-height: 50px; text-align: center; color: white; padding: 0;">';
            echo "<p>$error</p>";
            echo '</div>';
        }
        exit;
    }

    // ===== POST VALIDATION FINISHED ===== //



    echo '<div class="box">
            <div class="section">';

    
    $rpt_stamp = time();
    $rpt_time = date($timefmt, $rpt_stamp);
    $rpt_date = date($datefmt, $rpt_stamp);

    echo "<p>Raportti haettu: $rpt_date klo $rpt_time</p>";
    echo "<p>Raporttiin valittu: $group_name</p>";
    echo "<p>Alkaen: $from_date (00:00)</p>";
    echo "<p>Päättyen: $to_date (24:00)</p>";

    if (!empty($tmp_csv)) {
        echo "<a class=\"link\" href=\"get_csv.php?rpt=hrs_wkd&display_ip=$tmp_display_ip&csv=$tmp_csv&office=All&group=$group_name&fullname=All&from=$from_timestamp&to=$to_timestamp&tzo=$tzo&paginate=$tmp_paginate&round=$tmp_round_time&details=$tmp_show_details&rpt_run_on=$rpt_stamp&rpt_date=$rpt_date&from_date=$from_date\">Lataa CSV -tiedosto</a></td></tr>\n";
    }

    echo '</div>
        <div class="section">
            <table class="reports">';

    $employees_cnt = 0;
    $employees_empfullname = array();
    $employees_displayname = array();
    $info_cnt = 0;
    $info_fullname = array();
    $info_inout = array();
    $info_timestamp = array();
    $info_notes = array();
    $info_date = array();
    $x_info_date = array();
    $info_start_time = array();
    $info_end_time = array();
    $punchlist_in_or_out = array();
    $punchlist_punchitems = array();
    $secs = 0;
    $total_hours = 0;
    $row_count = 0;
    $page_count = 0;
    $punch_cnt = 0;
    $tmp_z = 0;

    // retrieve a list of users //

    $where = array("tstamp IS NOT NULL");
    $qparm = array();

    
    $result = tc_query("SELECT userID, displayName FROM employees WHERE groupID = '$groupID'");

    while ($row = mysqli_fetch_array($result)) {
        $employees_empfullname[] = "" . $row['userID'] . "";
        $employees_displayname[] = "" . $row['displayName'] . "";
        $employees_cnt++;
    }

    for ($x = 0; $x < $employees_cnt; $x++) {

        echo "<tr>
                <td style='color: var(--blue); font-weight: bold;'>$employees_displayname[$x]</td>
            </tr>";
            
        echo "<tr>
                <td>Päivämäärä</td>
                <td>Tunnit</td>
            </tr>";

            $result = tc_query(<<<QUERY
   SELECT i.userID, i.inout, i.timestamp, i.notes, p.in_or_out, p.punchitems, p.color
     FROM {$db_prefix}info      AS i
     JOIN {$db_prefix}employees AS e ON e.userID = i.userID
     JOIN {$db_prefix}punchlist AS p ON i.inout = p.punchitems
    WHERE e.userID  = ?
      AND i.timestamp   >= ?
      AND i.timestamp   <  ?
 ORDER BY i.timestamp ASC
QUERY
            , array($employees_empfullname[$x], $from_timestamp, $to_timestamp));

            while ($row = mysqli_fetch_array($result)) {
                $info_fullname[] = "" . $row['userID'] . "";
                $info_inout[] = "" . $row['inout'] . "";
                $info_timestamp[] = "" . $row['timestamp'] . "" + $tzo;
                $info_notes[] = "" . $row['notes'] . "";
                $punchlist_in_or_out[] = "" . $row['in_or_out'] . "";
                $punchlist_punchitems[] = "" . $row['punchitems'] . "";
                $punchlist_color[] = "" . $row['color'] . "";
                $info_cnt++;
            }

            for ($y = 0; $y < $info_cnt; $y++) {

                //      $info_date[] = date($datefmt, $info_timestamp[$y]);
                $x_info_date[] = date($datefmt, $info_timestamp[$y]);
                $info_date[] = date('n/j/y', $info_timestamp[$y]);
                $info_start_time[] = strtotime($info_date[$y]);
                $info_end_time[] = $info_start_time[$y] + 86399;

                if (isset($tmp_info_date)) {
                    if ($tmp_info_date == $info_date[$y]) {
                        if (empty($punchlist_in_or_out[$y])) {
                            $punch_cnt++;
                            if ($status == "out") {
                                $secs = $secs + ($info_timestamp[$y] - $out_time);
                            } elseif ($status == "in") {
                                $secs = $secs + ($info_timestamp[$y] - $in_time);
                            }
                            $status = "out";
                            $out_time = $info_timestamp[$y];
                            if ($y == $info_cnt - 1) {
                                $hours = secsToHours($secs, $tmp_round_time);
                                $total_hours = $total_hours + $hours;
                                $row_color = $color2; // Initial row color
                                if (empty($y)) {
                                    $yy = 0;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                } else {
                                    $yy = $y - 1;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                }
                                echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$y]</td>\n";
                                if ($hours < 10) {
                                    echo "      <td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                }
                                $row_color = ($row_color == $color1) ? $color2 : $color1;
                                $row_count++;
                                if ($tmp_show_details == "1") {
                                    echo "  <tr><td width=100% colspan=2>\n";
                                    echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                    for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                        $time_formatted = date($timefmt, $info_timestamp[$z]);
                                        echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                        echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                        echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                        if (@$tmp_display_ip == "1") {
                                            echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                            color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                        }
                                        echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                        $row_color = ($row_color == $color1) ? $color2 : $color1;
                                        $row_count++;
                                        $tmp_z++;
                                    }
                                    echo "</table></td></tr>\n";
                                    if ($row_count >= "40") {
                                        $row_count = "0";
                                        $page_count++;
                                        $temp_page_count = $page_count + 1;
                                        if (!empty($tmp_paginate)) {
                                            echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                            echo "<table width=100% align=center class=misc_items border=0
                                          cellpadding=3 cellspacing=0>\n";
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='font-size:9px;color:#000000;'>Date Range: $from_date &ndash; $to_date</td></tr>\n";
                                            echo "</table></td></tr>\n";
                                            if (strtolower($user_or_display) == "display") {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            } else {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            }
                                            echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                            echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                                  Worked</u></b></td></tr>\n";
                                        }
                                    }
                                }
                                $secs = 0;
                                $punch_cnt = 0;
                            }
                        } else {
                            $punch_cnt++;
                            if ($y == $info_cnt - 1) {
                                if (($info_timestamp[$y] <= $rpt_stamp) && ($rpt_stamp < ($to_timestamp + $tzo)) && ($x_info_date[$y] == $rpt_date)) {
                                    if ($status == "in") {
                                        $secs = $secs + ($rpt_stamp - $info_timestamp[$y]) + ($info_timestamp[$y] - $in_time);
                                    } elseif ($status == "out") {
                                        $secs = $secs + ($rpt_stamp - $info_timestamp[$y]);
                                    }
                                    $currently_punched_in = '1';
                                } elseif (($info_timestamp[$y] <= $rpt_stamp) && ($x_info_date[$y] == $rpt_date)) {
                                    if ($status == "in") {
                                        $secs = $secs + (($to_timestamp + $tzo) - $info_timestamp[$y]) + ($info_timestamp[$y] - $in_time);
                                    } elseif ($status == "out") {
                                        $secs = $secs + (($to_timestamp + $tzo) - $info_timestamp[$y]);
                                    }
                                    $currently_punched_in = '1';
                                } else {
                                    $secs = $secs + (($info_end_time[$y] + 1) - $info_timestamp[$y]);
                                }
                                //                      if (($info_timestamp[$y] <= $rpt_stamp) && ($x_info_date[$y] == $rpt_date)) {
                                //                          if ($status == "in") {
                                //                              $secs = $secs + ($rpt_stamp - $info_timestamp[$y]) + ($info_timestamp[$y] - $in_time);
                                //                          } elseif ($status == "out") {
                                //                              $secs = $secs + ($rpt_stamp - $info_timestamp[$y]);
                                //                          }
                                //                          $currently_punched_in = '1';
                                //                      } else {
                                //                          $secs = $secs + (($info_end_time[$y] + 1) - $info_timestamp[$y]);
                                //                      }
                            } else {
                                if ($status == "in") {
                                    $secs = $secs + ($info_timestamp[$y] - $in_time);
                                }
                                $in_time = $info_timestamp[$y];
                                $previous_days_end_time = $info_end_time[$y] + 1;
                            }
                            $status = "in";
                            if ($y == $info_cnt - 1) {
                                $hours = secsToHours($secs, $tmp_round_time);
                                $total_hours = $total_hours + $hours;
                                $row_color = $color2; // Initial row color
                                if ((empty($y)) || ($y == $info_cnt - 1)) {
                                    $yy = 0;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                } else {
                                    $yy = $y - 1;
                                    $date_formatted = date('l, ', $info_timestamp[$y - 1]);
                                }
                                echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$y]</td>\n";
                                if ($hours < 10) {
                                    echo "      <td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                }
                                $row_color = ($row_color == $color1) ? $color2 : $color1;
                                $row_count++;
                                if ($tmp_show_details == "1") {
                                    echo "  <tr><td width=100% colspan=2>\n";
                                    echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                    for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                        $time_formatted = date($timefmt, $info_timestamp[$z]);
                                        echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                        echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                        echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                        if (@$tmp_display_ip == "1") {
                                            echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                            color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                        }
                                        echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                        $row_color = ($row_color == $color1) ? $color2 : $color1;
                                        $row_count++;
                                        $tmp_z++;
                                    }
                                    echo "</table></td></tr>\n";
                                    if ($row_count >= "40") {
                                        $row_count = "0";
                                        $page_count++;
                                        $temp_page_count = $page_count + 1;
                                        if (!empty($tmp_paginate)) {
                                            echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                            echo "<table width=100% align=center class=misc_items border=0
                                          cellpadding=3 cellspacing=0>\n";
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='font-size:9px;color:#000000;'>Date Range: $from_date &ndash; $to_date</td></tr>\n";
                                            echo "</table></td></tr>\n";
                                            if (strtolower($user_or_display) == "display") {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            } else {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            }
                                            echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                            echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                                  Worked</u></b></td></tr>\n";
                                        }
                                    }
                                }
                                $secs = 0;
                                $punch_cnt = 0;
                            }
                        }
                    } else {

                        //// print totals for previous day ////

                        //// if the previous has only a single In punch and no Out punches, configure the $secs ////

                        if (isset($tmp_info_date)) {
                            if ($status == "out") {
                                $secs = $secs;
                            } elseif ($status == "in") {
                                $secs = $secs + ($previous_days_end_time - $in_time);
                            }
                            $hours = secsToHours($secs, $tmp_round_time);
                            $total_hours = $total_hours + $hours;
                            $row_color = $color2; // Initial row color
                            if (empty($y)) {
                                $yy = 0;
                                $date_formatted = date('l, ', $info_timestamp[$y]);
                            } else {
                                $yy = $y - 1;
                                $date_formatted = date('l, ', $info_timestamp[$y - 1]);
                            }
                            echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                            border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$yy]</td>\n";
                            if ($hours < 10) {
                                echo "      <td nowrap style='color:#000000;:31px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            } else {
                                echo "      <td nowrap style='color:#000000;:25px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            }
                            $row_color = ($row_color == $color1) ? $color2 : $color1;
                            $row_count++;
                            if ($tmp_show_details == "1") {
                                echo "  <tr><td width=100% colspan=2>\n";
                                echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                    $time_formatted = date($timefmt, $info_timestamp[$z]);
                                    echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                    echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                    echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                    if (@$tmp_display_ip == "1") {
                                        echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                    color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                    }
                                    echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                    $row_color = ($row_color == $color1) ? $color2 : $color1;
                                    $row_count++;
                                    $tmp_z++;
                                }
                                echo "</table></td></tr>\n";
                                if ($row_count >= "40") {
                                    $row_count = "0";
                                    $page_count++;
                                    $temp_page_count = $page_count + 1;
                                    if (!empty($tmp_paginate)) {
                                        echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                        echo "<table width=100% align=center class=misc_items border=0
                                  cellpadding=3 cellspacing=0>\n";
                                        echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                    $rpt_date (page $temp_page_count)</td>
                                    <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                        echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                    style='font-size:9px;color:#000000;'>Date
                                    Range: $from_date &ndash; $to_date</td></tr>\n";
                                        echo "</table></td></tr>\n";
                                        if (strtolower($user_or_display) == "display") {
                                            echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                        style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                        border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                        } else {
                                            echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                        style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                        border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                        }
                                        echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                        echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                          Worked</u></b></td></tr>\n";
                                    }
                                }

                            }
                            $secs = 0;
                            unset($in_time);
                            unset($out_time);
                            unset($previous_days_end_time);
                            unset($status);
                            unset($tmp_info_date);
                            unset($date_formatted);
                        }
                        $tmp_info_date = $info_date[$y];
                        $previous_days_end_time = $info_end_time[$y] + 1;
                        $punch_cnt++;
                        if (empty($punchlist_in_or_out[$y])) {
                            $status = "out";
                            $secs = $info_timestamp[$y] - $info_start_time[$y];
                            $out_time = $info_timestamp[$y];
                            $previous_days_end_time = $info_end_time[$y] + 1;
                            if ($y == $info_cnt - 1) {
                                $hours = secsToHours($secs, $tmp_round_time);
                                $total_hours = $total_hours + $hours;
                                $row_color = $color2; // Initial row color
                                if (empty($y)) {
                                    $yy = 0;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                } else {
                                    $yy = $y - 1;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                }
                                echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$y]</td>\n";
                                if ($hours < 10) {
                                    echo "      <td nowrap style='color:#000000;:31px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;:25px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                }
                                $row_color = ($row_color == $color1) ? $color2 : $color1;
                                $row_count++;
                                if ($tmp_show_details == "1") {
                                    echo "  <tr><td width=100% colspan=2>\n";
                                    echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                    for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                        $time_formatted = date($timefmt, $info_timestamp[$z]);
                                        echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                        echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                        echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                        if (@$tmp_display_ip == "1") {
                                            echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                            color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                        }
                                        echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                        $row_color = ($row_color == $color1) ? $color2 : $color1;
                                        $row_count++;
                                        $tmp_z++;
                                    }
                                    echo "</table></td></tr>\n";
                                    if ($row_count >= "40") {
                                        $row_count = "0";
                                        $page_count++;
                                        $temp_page_count = $page_count + 1;
                                        if (!empty($tmp_paginate)) {
                                            echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                            echo "<table width=100% align=center class=misc_items border=0
                                          cellpadding=3 cellspacing=0>\n";
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='font-size:9px;color:#000000;'>Date Range: $from_date &ndash; $to_date</td></tr>\n";
                                            echo "</table></td></tr>\n";
                                            if (strtolower($user_or_display) == "display") {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            } else {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            }
                                            echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                            echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                                  Worked</u></b></td></tr>\n";
                                        }
                                    }
                                }
                                $secs = 0;
                                $punch_cnt = 0;
                            }
                        } else {
                            if ($y == $info_cnt - 1) {
                                if (($info_timestamp[$y] <= $rpt_stamp) && ($rpt_stamp < ($to_timestamp + $tzo)) && ($x_info_date[$y] == $rpt_date)) {
                                    $secs = $secs + ($rpt_stamp - $info_timestamp[$y]);
                                    $currently_punched_in = '1';
                                } elseif (($info_timestamp[$y] <= $rpt_stamp) && ($x_info_date[$y] == $rpt_date)) {
                                    $secs = $secs + (($to_timestamp + $tzo) - $info_timestamp[$y]);
                                    $currently_punched_in = '1';
                                } else {
                                    $secs = $secs + (($info_end_time[$y] + 1) - $info_timestamp[$y]);
                                }
                                //                      if (($info_timestamp[$y] <= $rpt_stamp) && ($x_info_date[$y] == $rpt_date)) {
                                //                          $secs = $secs + ($rpt_stamp - $info_timestamp[$y]);
                                //                          $currently_punched_in = '1';
                                //                      } else {
                                //                          $secs = $secs + (($info_end_time[$y] + 1) - $info_timestamp[$y]);
                                //                      }
                            } else {
                                $status = "in";
                                $in_time = $info_timestamp[$y];
                                $previous_days_end_time = $info_end_time[$y] + 1;
                            }
                            if ($y == $info_cnt - 1) {
                                $hours = secsToHours($secs, $tmp_round_time);
                                $total_hours = $total_hours + $hours;
                                $row_color = $color2; // Initial row color
                                if (empty($y)) {
                                    $yy = 0;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                } else {
                                    $yy = $y - 1;
                                    $date_formatted = date('l, ', $info_timestamp[$y]);
                                }
                                echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$y]</td>\n";
                                if ($hours < 10) {
                                    echo "      <td nowrap style='color:#000000;:31px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;:25px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                }
                                $row_color = ($row_color == $color1) ? $color2 : $color1;
                                $row_count++;
                                if ($tmp_show_details == "1") {
                                    echo "  <tr><td width=100% colspan=2>\n";
                                    echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                    for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                        $time_formatted = date($timefmt, $info_timestamp[$z]);
                                        echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                        echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                        echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                        if (@$tmp_display_ip == "1") {
                                            echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                            color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                        }
                                        echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                        $row_color = ($row_color == $color1) ? $color2 : $color1;
                                        $row_count++;
                                        $tmp_z++;
                                    }
                                    echo "</table></td></tr>\n";
                                    if ($row_count >= "40") {
                                        $row_count = "0";
                                        $page_count++;
                                        $temp_page_count = $page_count + 1;
                                        if (!empty($tmp_paginate)) {
                                            echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                            echo "<table width=100% align=center class=misc_items border=0
                                          cellpadding=3 cellspacing=0>\n";
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='font-size:9px;color:#000000;'>Date Range: $from_date &ndash; $to_date</td></tr>\n";
                                            echo "</table></td></tr>\n";
                                            if (strtolower($user_or_display) == "display") {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            } else {
                                                echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                                style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                                border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                            }
                                            echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                            echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                                  Worked</u></b></td></tr>\n";
                                        }
                                    }
                                }
                                $secs = 0;
                                $punch_cnt = 0;
                            }
                        }
                    }
                } else {

                    ///// this is for the start of the first entry for the first day /////

                    $tmp_info_date = $info_date[$y];
                    $previous_days_end_time = $info_end_time[$y] + 1;
                    if (empty($punchlist_in_or_out[$y])) {
                        $out = 1;
                        $status = "out";
                        if ($info_date[$y] == $from_date) {
                            $secs = $info_timestamp[$y] - $from_timestamp - $tzo;
                        } else {
                            $secs = $info_timestamp[$y] - $info_start_time[$y];
                        }
                        $out_time = $info_timestamp[$y];
                        $previous_days_end_time = $info_end_time[$y] + 1;
                        if ($y == $info_cnt - 1) {
                            $hours = secsToHours($secs, $tmp_round_time);
                            $total_hours = $total_hours + $hours;
                            $row_color = $color2; // Initial row color
                            if (empty($y)) {
                                $yy = 0;
                                $date_formatted = date('l, ', $info_timestamp[$y]);
                            } else {
                                $yy = $y - 1;
                                $date_formatted = date('l, ', $info_timestamp[$y]);
                            }
                            echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                            border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$y]</td>\n";
                            if ($hours < 10) {
                                echo "      <td nowrap style='color:#000000;:31px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            } else {
                                echo "      <td nowrap style='color:#000000;:25px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            }
                            $row_color = ($row_color == $color1) ? $color2 : $color1;
                            $row_count++;
                            if ($tmp_show_details == "1") {
                                echo "  <tr><td width=100% colspan=2>\n";
                                echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                    $time_formatted = date($timefmt, $info_timestamp[$z]);
                                    echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                    echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                    echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                    if (@$tmp_display_ip == "1") {
                                        echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                        color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                    }
                                    echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                    $row_color = ($row_color == $color1) ? $color2 : $color1;
                                    $row_count++;
                                    $tmp_z++;
                                }
                                echo "</table></td></tr>\n";
                                if ($row_count >= "40") {
                                    $row_count = "0";
                                    $page_count++;
                                    $temp_page_count = $page_count + 1;
                                    if (!empty($tmp_paginate)) {
                                        echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                        echo "<table width=100% align=center class=misc_items border=0
                                      cellpadding=3 cellspacing=0>\n";
                                        echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                        $rpt_date (page $temp_page_count)</td>
                                        <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                        echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                        style='font-size:9px;color:#000000;'>Date Range: $from_date &ndash; $to_date</td></tr>\n";
                                        echo "</table></td></tr>\n";
                                        if (strtolower($user_or_display) == "display") {
                                            echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                            style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                            border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                        } else {
                                            echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                            style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                            border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                        }
                                        echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                        echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                              Worked</u></b></td></tr>\n";
                                    }
                                }
                            }
                            $secs = 0;
                            $punch_cnt = 0;
                        }
                    } else {
                        $secs = 0;
                        $status = "in";
                        $in_time = $info_timestamp[$y];
                        $previous_days_end_time = $info_end_time[$y] + 1;
                        if ($y == $info_cnt - 1) {
                            if (($info_timestamp[$y] <= $rpt_stamp) && ($rpt_stamp < ($to_timestamp + $tzo)) && ($x_info_date[$y] == $rpt_date)) {
                                $secs = $secs + ($rpt_stamp - $info_timestamp[$y]);
                                $currently_punched_in = '1';
                            } elseif (($info_timestamp[$y] <= $rpt_stamp) && ($x_info_date[$y] == $rpt_date)) {
                                $secs = $secs + (($to_timestamp + $tzo) - $info_timestamp[$y]);
                                $currently_punched_in = '1';
                            } else {
                                $secs = $secs + (($info_end_time[$y] + 1) - $info_timestamp[$y]);
                            }
                        }
                        if ($y == $info_cnt - 1) {
                            $hours = secsToHours($secs, $tmp_round_time);
                            $total_hours = $total_hours + $hours;
                            $row_color = $color2; // Initial row color
                            if (empty($y)) {
                                $yy = 0;
                                $date_formatted = date('l, ', $info_timestamp[$y]);
                            } else {
                                $yy = $y - 1;
                                $date_formatted = date('l, ', $info_timestamp[$y]);
                            }
                            echo "  <tr bgcolor=\"$row_color\" align=\"left\"><td style=\"color:#000000;border-style:solid;border-color:#888888;
                            border-width:1px 0px 0px 0px;\" nowrap>$date_formatted$x_info_date[$y]</td>\n";
                            if ($hours < 10) {
                                echo "      <td nowrap style='color:#000000;:31px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            } else {
                                echo "      <td nowrap style='color:#000000;:25px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            }
                            $row_color = ($row_color == $color1) ? $color2 : $color1;
                            $row_count++;
                            if ($tmp_show_details == "1") {
                                echo "  <tr><td width=100% colspan=2>\n";
                                echo "<table width=100% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                                for ($z = $tmp_z; $z <= $punch_cnt; $z++) {
                                    $time_formatted = date($timefmt, $info_timestamp[$z]);
                                    echo "  <tr bgcolor=\"$row_color\" align=\"left\">\n";
                                    echo "      <td align=left width=13% nowrap style=\"color:$punchlist_color[$z];\">$info_inout[$z]</td>\n";
                                    echo "      <td nowrap align=right width=10% style='padding-right:25px;'>$time_formatted</td>\n";
                                    if (@$tmp_display_ip == "1") {
                                        echo "      <td nowrap align=left width=25% style='padding-right:25px;
                                        color:$punchlist_color[$z];'>$info_ipaddress[$z]</td>\n";
                                    }
                                    echo "      <td width=77%>$info_notes[$z]</td></tr>\n";
                                    $row_color = ($row_color == $color1) ? $color2 : $color1;
                                    $row_count++;
                                    $tmp_z++;
                                }
                                echo "</table></td></tr>\n";
                                if ($row_count >= "40") {
                                    $row_count = "0";
                                    $page_count++;
                                    $temp_page_count = $page_count + 1;
                                    if (!empty($tmp_paginate)) {
                                        echo "<tr style='page-break-before:always;'><td width=100% colspan=2>\n";
                                        echo "<table width=100% align=center class=misc_items border=0
                                      cellpadding=3 cellspacing=0>\n";
                                        echo "  <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time,
                                        $rpt_date (page $temp_page_count)</td>
                                        <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                                        echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                        style='font-size:9px;color:#000000;'>Date Range: $from_date &ndash; $to_date</td></tr>\n";
                                        echo "</table></td></tr>\n";
                                        if (strtolower($user_or_display) == "display") {
                                            echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                            style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                            border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                        } else {
                                            echo "  <tr><td class=notdisplay_rpt width=100% colspan=2
                                            style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                                            border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b>&nbsp;(cont'd)</td></tr>\n";
                                        }
                                        echo "  <tr><td width=75% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
                                        echo "      <td width=25% class=notdisplay_rpt nowrap align=left style='color:#27408b;'><b><u>Hours
                                              Worked</u></b></td></tr>\n";
                                    }
                                }
                            }
                            $secs = 0;
                            $punch_cnt = 0;
                        }
                    }
                } // ends if (isset($tmp_info_date))
            } // ends for $y

            unset($in_time);
            unset($out_time);
            unset($previous_days_end_time);
            unset($status);
            unset($tmp_info_date);
            unset($date_formatted);
            unset($x_info_date);
            $my_total_hours = number_format($total_hours, 2);

            echo "<tr align=\"left\"><td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;'><b>Kokonaistunnit</b></td>\n";
                if ($my_total_hours < 10) {
                    echo "                <td nowrap style='fcolor:#000000;border-style:solid;border-color:#888888;
                          border-width:1px 0px 0px 0px;:30px;'><b>$my_total_hours</b></td></tr>\n";
                } elseif ($my_total_hours < 100) {
                    echo "                <td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                          border-width:1px 0px 0px 0px;:23px;'><b>$my_total_hours</b></td></tr>\n";
                } else {
                    echo "                <td nowrap style='color:#000000;border-style:solid;border-color:#888888;
                          border-width:1px 0px 0px 0px;:15px;'><b>$my_total_hours</b></td></tr>\n";
                }
                echo "              <tr><td height=40 colspan=2 style='border-style:solid;border-color:#888888;border-width:1px 0px 0px 0px;'>&nbsp;</td></tr>\n";
            
                $row_count++;

            $row_count = "0";
            $page_count++;
            $temp_page_count = $page_count + 1;

            if (!empty($tmp_paginate)) {
                if ($x != ($employees_cnt - 1)) {
                    echo "            </table>\n";
                    echo "            <table style='page-break-before:always;' width=80% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>\n";
                    echo "              <tr><td class=notdisplay_rpt width=80% style='font-size:9px;color:#000000;'>Run on: $rpt_time, $rpt_date (page
                              $temp_page_count)</td>
                                <td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>$rpt_name</td></tr>\n";
                    echo "               <tr><td width=80%></td><td class=notdisplay_rpt nowrap style='font-size:9px;color:#000000;'>Date Range: $from_date -
                               $to_date</td></tr>\n";
                    echo "            </table>\n";
                    echo "            <table width=80% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>\n";
                }
            }

            //// reset everything before running the loop on the next user ////

            $tmp_z = 0;
            $row_count = 0;
            $total_hours = 0;
            $my_total_hours = 0;
            $info_cnt = 0;
            $punch_cnt = 0;
            $secs = 0;
            unset($info_fullname);
            unset($info_inout);
            unset($info_timestamp);
            unset($info_notes);
            unset($info_ipaddress);
            unset($punchlist_in_or_out);
            unset($punchlist_punchitems);
            unset($punchlist_color);
            unset($info_date);
            unset($info_start_time);
            unset($info_end_time);
            unset($tmp_info_date);
            unset($hours);
            unset($date_formatted);
            unset($currently_punched_in);
            unset($x_info_date);
    } // end for $x


echo '</div></section>';

}
echo "            </table>\n";
exit;
?>

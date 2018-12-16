<?php

session_start();

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];
$current_page = "total_hours.php";

require '../common.php';

if (!isset($tzo)) {
    settype($tzo, "integer");
    if (isset($_COOKIE['tzoffset'])) {
        $tzo = $_COOKIE['tzoffset'];
        $tzo = $tzo * 60;
     } else {
         $tzo = 0;
     }
}


echo "<title>Henkilökohtaiset työtunnit</title>\n";

if ($request == 'GET') {

    include 'header_get_reports.php';
    include 'topmain.php';


// LOMAKE
  echo "
  <div class='ownReportsBox'>
  <h2>Hae oma työtuntiraportti</h2>
    <form name='form' action='$self' method='post' onsubmit=\"return isFromOrToDate();\">
      <input type='hidden' name='date_format' value='$js_datefmt'>

        <div class='reportsField'>
          <label><b>Käyttäjätunnus: </b></label>
          <input type='password' name='left_barcode' maxlength='250' size='17' value='' autocomplete='off' autofocus>
        </div>

        <input class='button' type='submit' name='quickreport' value='Nopea raportti'/>

        <p><b>Valitse aikaväli</b></p>
        <div class='reportsField'>
          <label for='from'>Alkaen: </label>
          <input type='text' id='from' autocomplete='off' size='10' maxlength='10' name='from_date' style='color:#27408b'>
        </div>

        <div class='reportsField'>
          <label for='to'>Päättyen: </label>
          <input type='text' id='to' autocomplete='off' size='10' maxlength='10' name='to_date' style='color:#27408b'>
        </div>

        <div class='reportsField'>
          <label for='tmp_show_details'>Näytä yksittäiset kirjaukset</label>
          <input type='checkbox' name='tmp_show_details' value='1' ".(yes_no_bool($show_details) ? ' checked' : '')." style='height:15px; width:20px; float:none;'>
        </div>

        <br>
        <input class='button' type='submit' name='customreport' value='Kustomoitu raportti'/>

      </form>
  </div>";
    exit;




} else if (isset($_POST['quickreport'])) { //  =========== NOPEA RAPORTTI ===========
  require '../common.php';
  include 'header_post_reports.php';
  include 'topmain.php';

  echo "<title>Omat Tunnit</title>\n";

  function convertToHours($tmstmp) {
    $hours = floor($tmstmp / 3600);
    $minutes = floor(($tmstmp / 60) % 60);
    $seconds = $tmstmp % 60;
    if ($tmstmp > 0) {
      return $hours > 0 ? "$hours tuntia, $minutes minuuttia" : ($minutes > 0 ? "$minutes minuuttia, $seconds sekuntia" : "$seconds sekuntia");
    } else {
      return " ";
    }
  }

  $timeNow = time();
  $barcode = (yes_no_bool($barcode_clockin) ? strtoupper($_POST['left_barcode']) : "");
  $fullname = tc_select_value("empfullname", "employees", "barcode = ?", $barcode);
  $displayname = tc_select_value("displayname", "employees", "barcode = ?", $barcode);

  if (!has_value($fullname)) {
    echo "<h3 style='color:red;'>Antamallasi käyttäjätunnuksella ei löytynyt ketään.</h3>";
  }

  $monthtime = array_fill(1, 12, " ");
  $weektime = array_fill(1, 52, " ");

  $infoQuery = tc_query(<<<QUERY
SELECT *
FROM info
WHERE fullname = '$fullname' AND `inout` = 'out'
ORDER BY timestamp DESC
QUERY
);

  while ( $tempOut = mysqli_fetch_array($infoQuery) ) {   // Käydään läpi työntekijän kaikki kirjaukset
    if ( date('Y', $tempOut[3]) == date('Y', $timeNow) ) { // Lasketaan vain tämän vuoden kirjaukset
      $tempstamp = $tempOut[3];
      $month = date('n', $tempOut[3]); // 1-12
      $week = date('W', $tempOut[3]); // 1-52

      $nextInfoQuery = tc_query( "SELECT * FROM info WHERE fullname = '$fullname' AND timestamp < '$tempstamp' ORDER BY timestamp DESC"); // Haetaan seuraava kirjaus (eli sisäänkirjaus)
      $tempIn = mysqli_fetch_row($nextInfoQuery);

      $time = (int)$tempOut[3] - (int)$tempIn[3]; // Lasketaan uloskirjauksen ja sisäänkirjauksen erotus
      $monthtime[$month] += $time;
      $weektime[$week] += $time;

    } else {
      break;
    }
  }

  echo '<div class="ownReportsBox" style="width:500px;">
          <h2> '.$displayname.' työtunnit </h2>
          <center><p style="color: grey; margin: 0;"> Vuosi '. date('Y', $timeNow).'</p></center>
          <p> Työaikasi tällä viikolla: <b>' .convertToHours($weektime[date('W', $timeNow)]). '</b> </p> <br>';

  if ($monthtime[12] > 0) echo 'Joulukuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[12]). '</div><br>';
  if ($monthtime[11] > 0) echo 'Marraskuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[11]). '</div><br>';
  if ($monthtime[10] > 0) echo 'Lokakuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[10]). '</div><br>';
  if ($monthtime[9] > 0) echo 'Syyskuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[9]). '</div><br>';
  if ($monthtime[8] > 0) echo 'Elokuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[8]). '</div><br>';
  if ($monthtime[7] > 0) echo 'Heinäkuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[7]). '</div><br>';
  if ($monthtime[6] > 0) echo 'Kesäkuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[6]). '</div><br>';
  if ($monthtime[5] > 0) echo 'Toukokuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[5]). '</div><br>';
  if ($monthtime[4] > 0) echo 'Huhtikuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[4]). '</div><br>';
  if ($monthtime[3] > 0) echo 'Maaliskuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[3]). '</div><br>';
  if ($monthtime[2] > 0) echo 'Helmikuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[2]). '</div><br>';
  if ($monthtime[1] > 0) echo 'Tammikuu: <div class="monthlyHours">' .convertToHours((int)$monthtime[1]). '</div><br>';

  echo '</div>';




} else if (isset($_POST['customreport'])) { // =========== KUSTOMOITU RAPORTTI ===========

    include 'topmain.php';
    include 'header_post_reports.php';

    $barcode = strtoupper($_POST['left_barcode']);
    $fullname = tc_select_value("empfullname", "employees", "barcode = ?", $barcode);

    @$office_name = tc_select_value("office", "employees", "barcode = ?", $barcode);
    @$group_name = tc_select_value("groups", "employees", "barcode = ?", $barcode);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $tmp_round_time = '0';
    $tmp_paginate = '0';
    $tmp_show_details = one_or_empty(@$_POST['tmp_show_details']);
    $tmp_display_ip = one_or_empty(@$_POST['tmp_display_ip']);
    $tmp_display_office = one_or_empty(@$_POST['tmp_display_office']);
    $tmp_csv = '1';

    // begin post validation //

    if ($from_date == "" || $to_date == "") {
      echo "<h3 style='color:red;'>Täytäthän molemmat päivämäärät</h3>";
      exit;
    }

    if (!has_value($fullname)) {
      echo "<h3 style='color:red;'>Antamallasi käyttäjätunnuksella ei löytynyt ketään.</h3>";
      exit;
    }

    if ($fullname != "All") {
        $result = tc_select("empfullname, displayname", "employees",  "empfullname = ?", $fullname);

        while ($row = mysqli_fetch_array($result)) {
            $empfullname = "" . $row['empfullname'] . "";
            $displayname = "" . $row['displayname'] . "";
        }
        if (!isset($empfullname)) {
            echo "Something is fishy here.\n";
            exit;
        }
    }

    if (($office_name != "All") && (!empty($office_name))) {
        $getoffice = tc_select_value("officename", "offices", "officename = ?", $office_name);
        if (!isset($getoffice)) {
            echo "Something smells fishy here.\n";
            exit;
        }
    }

    if (($group_name != "All") && (!empty($group_name))) {
        $getgroup = tc_select_value("groupname", "groups", "groupname = ?", $group_name);
        if (!isset($getgroup)) {
            echo "Something smells fishy here.\n";
            exit;
        }
    }

    if ((!empty($tmp_round_time)) && ($tmp_round_time != '1') && ($tmp_round_time != '2') && ($tmp_round_time != '3') && ($tmp_round_time != '4') && ($tmp_round_time != '5')) {
        $evil_post = '1';
        if ($use_reports_password == "yes") {
            include '../admin/topmain.php';
        } else {
            include 'topmain.php';
        }
        echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
        echo "  <tr valign=top>\n";
        echo "    <td>\n";
        echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
        echo "        <tr class=right_main_text>\n";
        echo "          <td valign=top>\n";
        echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
        echo "              <tr>\n";
        echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    Choose a rounding method.</td></tr>\n";
        echo "            </table>\n";
    }

    if (!isset($evil_post)) {
        if (empty($from_date)) {
            $evil_post = '1';
            if ($use_reports_password == "yes") {
                include '../admin/topmain.php';
            } else {
                include 'topmain.php';
            }
            echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
            echo "  <tr valign=top>\n";
            echo "    <td>\n";
            echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
            echo "        <tr class=right_main_text>\n";
            echo "          <td valign=top>\n";
            echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
            echo "              <tr>\n";
            echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid From Date is required.</td></tr>\n";
            echo "            </table>\n";
        } elseif (!preg_match('< ^ ([0-9]?[0-9])+ [-/.]+ ([0-9]?[0-9])+ [-/.]+ (([0-9]{2})|([0-9]{4})) $ >x', $from_date, $date_regs)) {
            $evil_post = '1';
            if ($use_reports_password == "yes") {
                include '../admin/topmain.php';
            } else {
                include 'topmain.php';
            }
            echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
            echo "  <tr valign=top>\n";
            echo "    <td>\n";
            echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
            echo "        <tr class=right_main_text>\n";
            echo "          <td valign=top>\n";
            echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
            echo "              <tr>\n";
            echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid From Date is required.</td></tr>\n";
            echo "            </table>\n";

        } else {

            if ($calendar_style == "amer") {
                if (isset($date_regs)) {
                    $from_month = $date_regs[1];
                    $from_day = $date_regs[2];
                    $from_year = $date_regs[3];
                }
                if ($from_month > 12 || $from_day > 31) {
                    $evil_post = '1';
                    if ($use_reports_password == "yes") {
                        include '../admin/topmain.php';
                    } else {
                        include 'topmain.php';
                    }
                    echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
                    echo "  <tr valign=top>\n";
                    echo "    <td>\n";
                    echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
                    echo "        <tr class=right_main_text>\n";
                    echo "          <td valign=top>\n";
                    echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
                    echo "              <tr>\n";
                    echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid From Date is required.</td></tr>\n";
                    echo "            </table>\n";
                }
            } elseif ($calendar_style == "euro") {
                if (isset($date_regs)) {
                    $from_month = $date_regs[2];
                    $from_day = $date_regs[1];
                    $from_year = $date_regs[3];
                }
                if ($from_month > 12 || $from_day > 31) {
                    $evil_post = '1';
                    if ($use_reports_password == "yes") {
                        include '../admin/topmain.php';
                    } else {
                        include 'topmain.php';
                    }
                    echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
                    echo "  <tr valign=top>\n";
                    echo "    <td>\n";
                    echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
                    echo "        <tr class=right_main_text>\n";
                    echo "          <td valign=top>\n";
                    echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
                    echo "              <tr>\n";
                    echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid From Date is required.</td></tr>\n";
                    echo "            </table>\n";
                }
            }
        }
    }

    if (!isset($evil_post)) {
        if (empty($to_date)) {
            $evil_post = '1';
            if ($use_reports_password == "yes") {
                include '../admin/topmain.php';
            } else {
                include 'topmain.php';
            }
            echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
            echo "  <tr valign=top>\n";
            echo "    <td>\n";
            echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
            echo "        <tr class=right_main_text>\n";
            echo "          <td valign=top>\n";
            echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
            echo "              <tr>\n";
            echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid To Date is required.</td></tr>\n";
            echo "            </table>\n";
        } elseif (!preg_match('< ^ ([0-9]?[0-9])+ [-/.]+ ([0-9]?[0-9])+ [-/.]+ (([0-9]{2})|([0-9]{4})) $ >x', $to_date, $date_regs)) {
            $evil_post = '1';
            if ($use_reports_password == "yes") {
                include '../admin/topmain.php';
            } else {
                include 'topmain.php';
            }
            echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
            echo "  <tr valign=top>\n";
            echo "    <td>\n";
            echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
            echo "        <tr class=right_main_text>\n";
            echo "          <td valign=top>\n";
            echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
            echo "              <tr>\n";
            echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid To Date is required.</td></tr>\n";
            echo "            </table>\n";

        } else {

            if ($calendar_style == "amer") {
                if (isset($date_regs)) {
                    $to_month = $date_regs[1];
                    $to_day = $date_regs[2];
                    $to_year = $date_regs[3];
                }
                if ($to_month > 12 || $to_day > 31) {
                    $evil_post = '1';
                    if ($use_reports_password == "yes") {
                        include '../admin/topmain.php';
                    } else {
                        include 'topmain.php';
                    }
                    echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
                    echo "  <tr valign=top>\n";
                    echo "    <td>\n";
                    echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
                    echo "        <tr class=right_main_text>\n";
                    echo "          <td valign=top>\n";
                    echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
                    echo "              <tr>\n";
                    echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid To Date is required.</td></tr>\n";
                    echo "            </table>\n";
                }
            } elseif ($calendar_style == "euro") {
                if (isset($date_regs)) {
                    $to_month = $date_regs[2];
                    $to_day = $date_regs[1];
                    $to_year = $date_regs[3];
                }
                if ($to_month > 12 || $to_day > 31) {
                    $evil_post = '1';
                    if ($use_reports_password == "yes") {
                        include '../admin/topmain.php';
                    } else {
                        include 'topmain.php';
                    }
                    echo "<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>\n";
                    echo "  <tr valign=top>\n";
                    echo "    <td>\n";
                    echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
                    echo "        <tr class=right_main_text>\n";
                    echo "          <td valign=top>\n";
                    echo "            <table align=center class=table_border width=60% border=0 cellpadding=0 cellspacing=3>\n";
                    echo "              <tr>\n";
                    echo "                <td class=table_rows width=20 align=center><img src='../images/icons/cancel.png' /></td><td class=table_rows_red>
                    A valid To Date is required.</td></tr>\n";
                    echo "            </table>\n";
                }
            }
        }
    }

    if (isset($evil_post)) {
        echo "            <br />\n";
        echo "            <form name='form' action='$self' method='post' onsubmit=\"return isFromOrToDate();\">\n";
        echo "            <table align=center class=table_border width=60% border=0 cellpadding=3 cellspacing=0>\n";
        echo "              <tr>\n";
        echo "                <th class=rightside_heading nowrap halign=left colspan=3><img src='../images/icons/report.png' />&nbsp;&nbsp;&nbsp;
                    Hours Worked Report</th></tr>\n";
        echo "              <tr><td height=15></td></tr>\n";
        echo "              <input type='hidden' name='date_format' value='$js_datefmt'>\n";
        if ($username_dropdown_only == "yes") {

            echo "              <tr><td class=table_rows height=25 width=20% style='padding-left:32px;' nowrap>Username:</td><td colspan=2 align=left width=80%
                          style='color:red;font-family:Tahoma;font-size:10px;padding-left:20px;'>
                      <select name='user_name'>\n";
            echo "                    <option value ='All'>All</option>\n";
            echo html_options(tc_select("empfullname", "employees", "1=1 ORDER BY empfullname ASC"));

            echo "                  </select>&nbsp;*</td></tr>\n";
        } else {

            echo "              <tr><td class=table_rows height=25 width=20% style='padding-left:32px;' nowrap>Choose Office:</td><td colspan=2 width=80%
                      style='color:red;font-family:Tahoma;font-size:10px;padding-left:20px;'>
                      <select name='office_name' onchange='group_names();'>\n";
            echo "                      </select></td></tr>\n";
            echo "              <tr><td class=table_rows height=25 width=20% style='padding-left:32px;' nowrap>Choose Group:</td><td colspan=2 width=80%
                      style='color:red;font-family:Tahoma;font-size:10px;padding-left:20px;'>
                      <select name='group_name' onfocus='group_names();'>
                          <option selected>$group_name</option>\n";
            echo "                      </select></td></tr>\n";
            echo "              <tr><td class=table_rows height=25 width=20% style='padding-left:32px;' nowrap>Choose Username:</td><td colspan=2 width=80%
                      style='color:red;font-family:Tahoma;font-size:10px;padding-left:20px;'>
                      <select name='user_name' onfocus='user_names();'>
                          <option selected>$fullname</option>\n";
            echo "                      </select></td></tr>\n";
        }
        echo "              <tr><td class=table_rows style='padding-left:32px;' width=20% nowrap>From Date: ($tmp_datefmt)</td><td
                      style='color:red;font-family:Tahoma;font-size:10px;padding-left:20px;' width=80% >
                      <input type='text' size='10' maxlength='10' name='from_date' value='$from_date' style='color:#27408b'>&nbsp;*&nbsp;&nbsp;
                      <a href=\"#\" onclick=\"form.from_date.value='';cal.select(document.forms['form'].from_date,'from_date_anchor','$js_datefmt');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\" style='font-size:11px;color:#27408b;'>Pick Date</a></td><tr>\n";
        echo "              <tr><td class=table_rows style='padding-left:32px;' width=20% nowrap>To Date: ($tmp_datefmt)</td><td
                      style='color:red;font-family:Tahoma;font-size:10px;padding-left:20px;' width=80% >
                      <input type='text' size='10' maxlength='10' name='to_date' value='$to_date' style='color:#27408b'>&nbsp;*&nbsp;&nbsp;
                      <a href=\"#\" onclick=\"form.to_date.value='';cal.select(document.forms['form'].to_date,'to_date_anchor','$js_datefmt');
                      return false;\" name=\"to_date_anchor\" id=\"to_date_anchor\" style='font-size:11px;color:#27408b;'>Pick Date</a></td><tr>\n";
        echo "              <tr><td class=table_rows align=right colspan=3 style='color:red;font-family:Tahoma;font-size:10px;'>*&nbsp;required&nbsp;</td></tr>\n";
        echo "            </table>\n";
        echo "            <div style=\"position:absolute;visibility:hidden;background-color:#ffffff;layer-background-color:#ffffff;\" id=\"mydiv\"
                 height=200>&nbsp;</div>\n";

        echo '<div style="width: 60%; margin-left: auto; margin-right: auto;">';

        echo '<p><input type="checkbox" name="csv" value="1"' . ($tmp_csv == "1" ? " checked" : "") . '>&nbsp;';
        echo "Export to CSV? (link to CSV file will be in the top right of the next page)</p>\n";

        echo '<p><input type="checkbox" name="tmp_paginate" value="1"' . ($tmp_paginate == "1" ? " checked" : "") . '>&nbsp;';
        echo "Paginate this report so each user's time is printed on a separate page?\n";

        echo '<p><input type="checkbox" name="tmp_show_details" value="1" onchange="javascript:form.tmp_display_office.disabled=!this.checked;form.tmp_display_ip.disabled=!this.checked"' . (($tmp_show_details == "1") ? " checked" : "") . '>&nbsp;';
        echo "Show punch-in/out details?\n</p>";

        if (yes_no_bool($ip_logging)) {
            echo '<p>&nbsp; &nbsp; &nbsp;<input type="checkbox" name="tmp_display_ip" value="1"' . ($tmp_display_ip == "1" ? " checked" : "") . '>&nbsp;';
            echo "Display connecting ip address information?\n</p>";
        }

        echo '<p>&nbsp; &nbsp; &nbsp;<input type="checkbox" name="tmp_display_office" value="1"' . ($tmp_display_office == "1" ? " checked" : "") . '>&nbsp;';
        echo "Display office information?\n</p>";

        echo "<p>Round each user's time?</br>\n";
        echo "<input type='radio' name='tmp_round_time' value='1'" . (($round_time == '1') ? " checked" : "") . ">&nbsp;To the nearest 5 minutes (1/12th of an hour)<br>\n";

        echo "<input type='radio' name='tmp_round_time' value='2'" . (($round_time == '2') ? " checked" : "") . ">&nbsp;To the nearest 10 minutes (1/6th of an hour)<br>\n";
        echo "<input type='radio' name='tmp_round_time' value='3'" . (($round_time == '3') ? " checked" : "") . ">&nbsp;To the nearest 15 minutes (1/4th of an hour)<br>\n";
        echo "<input type='radio' name='tmp_round_time' value='4'" . (($round_time == '4') ? " checked" : "") . ">&nbsp;To the nearest 20 minutes (1/3rd of an hour)<br>\n";
        echo "<input type='radio' name='tmp_round_time' value='5'" . (($round_time == '5') ? " checked" : "") . ">&nbsp;To the nearest 30 minutes (1/2 of an hour)<br>\n";
        echo "<input type='radio' name='tmp_round_time' value='0'" . (empty($round_time)   ? " checked" : "") . ">&nbsp;Do not round<br>\n";
        echo "</p>\n";

        echo '</div>';

        echo "            <table align=center width=60% border=0 cellpadding=0 cellspacing=3>\n";
        echo "              <tr><td width=30><input type='image' name='submit' value='Edit Time' align='middle'
                      src='../images/buttons/next_button.png'></td><td><a href='index.php'><img src='../images/buttons/cancel_button.png'
                      border='0'></td></tr></table></form></td></tr>\n";
        include '../footer.php';
        exit;
    }

    // end post validation //

    if (!empty($from_date)) {
        $from_date = "$from_month/$from_day/$from_year";
        $from_timestamp = strtotime($from_date . " " . $report_start_time) - $tzo;
        $from_date = $_POST['from_date'];
    }

    if (!empty($to_date)) {
        $to_date = "$to_month/$to_day/$to_year";
        $to_timestamp = strtotime($to_date . " " . $report_end_time) - $tzo + 60;
        $to_date = $_POST['to_date'];
    }

    $rpt_stamp = time() + @$tzo;
    $rpt_time = date($timefmt, $rpt_stamp);
    $rpt_date = date($datefmt, $rpt_stamp);

    $emp_name = $fullname;
    if (strtolower($user_or_display) == "display") {
        $emp_field_name = "displayname";
    } else {
        $emp_field_name = "empfullname";
    }

    $tmp_fullname = $fullname;
    if ((strtolower($user_or_display) == "display") && ($tmp_fullname != "All")) {
        $tmp_fullname = $displayname;
    }
    if (($office_name == "All") && ($group_name == "All") && ($tmp_fullname == 'All')) {
        $tmp_fullname = "Offices: All &rarr; Groups: All &rarr; Users: All";
    } elseif ((empty($office_name)) && (empty($group_name)) && ($tmp_fullname == 'All')) {
        $tmp_fullname = "All Users";
    } elseif ((empty($office_name)) && (empty($group_name)) && ($tmp_fullname != 'All')) {
        $tmp_fullname = $tmp_fullname;
    } elseif (($office_name != "All") && ($group_name == "All") && ($tmp_fullname == 'All')) {
        $tmp_fullname = "Office: $office_name &rarr; Groups: All &rarr; Users: All";
    } elseif (($office_name != "All") && ($group_name != "All") && ($tmp_fullname == 'All')) {
        $tmp_fullname = "Office: $office_name &rarr; Group: $group_name &rarr; Users: All";
    }
    $rpt_name = "$tmp_fullname";

    echo "<table width=80% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>\n";
    echo "<tr><td width=80% style='color:#000000;'>Raportti haettu: $rpt_time, $rpt_date</td><td nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
    echo "<tr><td width=80%></td><td nowrap style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
    if (!empty($tmp_csv)) {
        echo "<tr class=notprint><td width=80%></td><td nowrap style='color:#000000;'><a style='color:#27408b; text-decoration:underline;' href=\"get_csv.php?rpt=hrs_wkd&display_ip=$tmp_display_ip&csv=$tmp_csv&office=$office_name&group=$group_name&fullname=$fullname&from=$from_timestamp&to=$to_timestamp&tzo=$tzo&paginate=$tmp_paginate&round=$tmp_round_time&details=$tmp_show_details&rpt_run_on=$rpt_stamp&rpt_date=$rpt_date&from_date=$from_date\">Lataa CSV -tiedosto</a></td></tr>\n";
    }
    echo "</table>\n";
    echo "<table width=80% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>\n";

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

    if ($fullname != "All") {
        $where[] = "empfullname = ?";
        $qparm[] = $fullname;
    }

    if ($office_name != "All") {
        $where[] = "office = ?";
        $qparm[] = $office_name;

        if ($group_name != "All") {
            $where[] = "groups = ?";
            $qparm[] = $group_name;
        }
    }

    $where = implode(" AND ", $where) . " ORDER BY $emp_field_name ASC";
    $result = tc_select("empfullname, displayname", "employees", $where, $qparm);

    while ($row = mysqli_fetch_array($result)) {
        $employees_empfullname[] = "" . $row['empfullname'] . "";
        $employees_displayname[] = "" . $row['displayname'] . "";
        $employees_cnt++;
    }

    for ($x = 0; $x < $employees_cnt; $x++) {

        if (($employees_empfullname[$x] == $fullname) || ($fullname == "All")) {

            if (strtolower($user_or_display) == "display") {
                echo "<tr><td width=100% colspan=2 style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888; border-width:0px 0px 1px 0px;\"><b>$employees_displayname[$x]</b></td></tr>\n";
            } else {
                echo "<tr><td width=100% colspan=2 style=\"font-size:11px;color:#000000;border-style:solid;border-color:#888888; border-width:0px 0px 1px 0px;\"><b>$employees_empfullname[$x]</b></td></tr>\n";
            }
            echo "  <tr><td width=75% nowrap align=left style='color:#27408b;'><b><u>Date</u></b></td>\n";
            echo "      <td width=25% nowrap align=left style='color:#27408b;'><b><u>Työtunnit (tunneissa)</u></b></td></tr>\n";
            $row_color = $color2; // Initial row color

            $result = tc_query(<<<QUERY
   SELECT i.fullname, i.inout, i.timestamp, i.notes, i.ipaddress, i.punchoffice, p.in_or_out, p.punchitems, p.color
     FROM {$db_prefix}info      AS i
     JOIN {$db_prefix}employees AS e ON e.empfullname = i.fullname
     JOIN {$db_prefix}punchlist AS p ON i.inout = p.punchitems
    WHERE e.empfullname  = ?
      AND i.timestamp   >= ?
      AND i.timestamp   <  ?
      AND e.empfullname <> 'admin'
 ORDER BY i.timestamp ASC
QUERY
            , array($employees_empfullname[$x], $from_timestamp, $to_timestamp));

            while ($row = mysqli_fetch_array($result)) {
                $info_fullname[] = "" . $row['fullname'] . "";
                $info_inout[] = "" . $row['inout'] . "";
                $info_timestamp[] = "" . $row['timestamp'] . "" + $tzo;
                $info_notes[] = "" . $row['notes'] . "";
                $info_ipaddress[] = "" . $row['ipaddress'] . " " . $row['punchoffice'] . "";
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
                                    echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
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
                                    echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
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
                                echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            } else {
                                echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                        echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                    $rpt_date (page $temp_page_count)</td>
                                    <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                        echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                    style='color:#000000;'>Date
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
                                    echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
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
                                    echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                    border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                                } else {
                                    echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                            echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                            $rpt_date (page $temp_page_count)</td>
                                            <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                            echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                            style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
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
                                echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            } else {
                                echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                        echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                        $rpt_date (page $temp_page_count)</td>
                                        <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                        echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                        style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
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
                                echo "      <td nowrap style='color:#000000;padding-left:31px;border-style:solid;border-color:#888888;
                                border-width:1px 0px 0px 0px;'>$hours</td></tr>\n";
                            } else {
                                echo "      <td nowrap style='color:#000000;padding-left:25px;border-style:solid;border-color:#888888;
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
                                        echo "  <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time,
                                        $rpt_date (page $temp_page_count)</td>
                                        <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                                        echo "  <tr><td width=80%></td><td class=notdisplay_rpt nowrap
                                        style='color:#000000;'>Aikavälillä: $from_date &ndash; $to_date</td></tr>\n";
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
            if (isset($currently_punched_in)) {
                echo "  </table>\n";
                echo "    <table width=80% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
                echo "              <tr align=\"left\"><td width=12% nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;padding-left:3px;'><b>Kokonaistuntimäärä</b></td>
                              <td width=63% align=left style='padding-left:10px;color:#FF0000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;'><b>$employees_empfullname[$x] is currently punched in.</b></td>\n";
                if ($my_total_hours < 10) {
                    echo "                <td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;padding-left:30px;'><b>$my_total_hours</b></td></tr>\n";
                } elseif ($my_total_hours < 100) {
                    echo "                <td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;padding-left:23px;'><b>$my_total_hours</b></td></tr>\n";
                } else {
                    echo "                <td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;padding-left:15px;'><b>$my_total_hours</b></td></tr>\n";
                }
                echo "              <tr><td height=40 colspan=3 style='border-style:solid;border-color:#888888;border-width:1px 0px 0px 0px;'>&nbsp;</td></tr>\n";
                echo " </table></td></tr><table width=80% align=center class=misc_items border=0 cellpadding=0 cellspacing=0>\n";
            } else {
                echo "              <tr align=\"left\"><td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                              border-width:1px 0px 0px 0px;'><b>Kokonaistuntimäärä</b></td>\n";
                if ($my_total_hours < 10) {
                    echo "                <td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                          border-width:1px 0px 0px 0px;padding-left:30px;'><b>$my_total_hours</b></td></tr>\n";
                } elseif ($my_total_hours < 100) {
                    echo "                <td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                          border-width:1px 0px 0px 0px;padding-left:23px;'><b>$my_total_hours</b></td></tr>\n";
                } else {
                    echo "                <td nowrap style='font-size:11px;color:#000000;border-style:solid;border-color:#888888;
                          border-width:1px 0px 0px 0px;padding-left:15px;'><b>$my_total_hours</b></td></tr>\n";
                }
                echo "              <tr><td height=40 colspan=2 style='border-style:solid;border-color:#888888;border-width:1px 0px 0px 0px;'>&nbsp;</td></tr>\n";
            }
            $row_count++;

            $row_count = "0";
            $page_count++;
            $temp_page_count = $page_count + 1;

            if (!empty($tmp_paginate)) {
                if ($x != ($employees_cnt - 1)) {
                    echo "            </table>\n";
                    echo "            <table style='page-break-before:always;' width=80% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>\n";
                    echo "              <tr><td class=notdisplay_rpt width=80% style='color:#000000;'>Raportti haettu: $rpt_time, $rpt_date (page
                              $temp_page_count)</td>
                                <td class=notdisplay_rpt nowrap style='color:#000000;'>$rpt_name</td></tr>\n";
                    echo "               <tr><td width=80%></td><td class=notdisplay_rpt nowrap style='color:#000000;'>Aikavälillä: $from_date -
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
        } // end if
    } // end for $x
}
echo "            </table>\n";
exit;
?>

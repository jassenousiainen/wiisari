<?php
session_start();

require 'common.php';
include 'header.php';

if (!isset($_GET['printer_friendly'])) {

    if (isset($_SESSION['valid_user'])) {
        $set_logout = "1";
    }

    include 'topmain.php';

    echo '
    <div class="lomake">
    <form name="timeclock" action="kirjaus.php" autocomplete="off" method="post">
      <br/>
    	<label class="tunnusOtsikko" for="left-barcode">Käyttäjätunnus:</label>
			<br/>
    	<input type="password" id="left_barcode" name="left_barcode" maxlength="250" size="17" value="" autocomplete="off" autofocus>
      <p style="color: grey">(Lopuksi paina ENTER)</p>

    	<input type="text" style="display:none;">
			<br/>
    	<input type="submit" name="submit_button" value="Sisään/Ulos" class="submit"/>

		</form>
	</div>
    ';


    //include 'leftmain.php';
}

echo "<title>Kellokalle</title>\n";
/**$current_page = "timeclock.php";

if (!isset($_GET['printer_friendly'])) {
    echo "    <td align=left class=right_main scope=col>\n";
    echo "      <table width=100% height=100% border=0 cellpadding=5 cellspacing=1>\n";
    echo "        <tr class=right_main_text>\n";
    echo "          <td valign=top>\n";
}

// code to allow sorting by Name, In/Out, Date, Notes //

if (!isset($_GET['sortcolumn']) or preg_match('/[^\w]/', $_GET['sortcolumn'])) {
    $sortcolumn = (($show_display_name == "yes") ? "displayname" : "fullname");
} else {
    $sortcolumn = addslashes($_GET['sortcolumn']);
}

if (!isset($_GET['sortdirection']) or preg_match('/[^\w]/', $_GET['sortdirection'])) {
    $sortdirection = "asc";
} else {
    $sortdirection = addslashes($_GET['sortdirection']);
}

if ($sortdirection == "asc") {
    $sortnewdirection = "desc";
} else {
    $sortnewdirection = "asc";
}

// determine what users, office, and/or group will be displayed on main page //

$where = array("e.disabled <> '1'", "e.empfullname <> 'admin'");
$qparm = array();

if (yes_no_bool($display_current_users)) {
    $current_users_date = strtotime(date($datefmt));
    $where[] = "i.timestamp < ?";
    $qparm[] = $current_users_date + 86400 - @$tzo;
    $where[] = "i.timestamp >= ?";
    $qparm[] = $current_users_date - @$tzo;
}

if ($display_office != "all") {
    $where[] = "e.office = ?";
    $qparm[] = $display_office;
}

if ($display_group != "all") {
    $where[] = "e.groups = ?";
    $qparm[] = $display_group;
}

$where = implode(" AND ", $where);
$result = tc_query(<<<QUERY
   SELECT i.*, e.*, p.*
     FROM {$db_prefix}info      AS i
     JOIN {$db_prefix}employees AS e ON (e.empfullname = i.fullname AND i.timestamp = e.tstamp)
     JOIN {$db_prefix}punchlist AS p ON i.inout = p.punchitems
    WHERE $where
 ORDER BY `$sortcolumn` $sortdirection
QUERY
, $qparm);

$tclock_stamp = time() + @$tzo;
$tclock_time = date($timefmt, $tclock_stamp);
$tclock_date = date($datefmt, $tclock_stamp);
$report_name = "Current Status Report";

echo "            <table width=100% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>\n";

if (!isset($_GET['printer_friendly'])) {
    echo "              <tr class=display_hide>\n";
} else {
    echo "              <tr>\n";
}

echo "                <td nowrap style='font-size:9px;color:#000000;padding-left:10px;'>$report_name&nbsp;&nbsp;---->&nbsp;&nbsp;As of: $tclock_time,
                    $tclock_date</td></tr>\n";
echo "            </table>\n";
include 'display.php';

if (!isset($_GET['printer_friendly'])) {
    include 'footer.php';
}
*/
?>

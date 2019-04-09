<?php

require 'common.php';

ob_start();
echo "<html>\n";

// grab the connecting ip address. //

$connecting_ip = get_ipaddress();

if (empty($connecting_ip)) {
    return false;
}

// determine if connecting ip address is allowed to connect to PHP Timeclock //

if ($restrict_ips == "yes") {
    for ($x = 0; $x < count($allowed_networks); $x++) {
        $is_allowed = ip_range($allowed_networks[$x], $connecting_ip);
        if (!empty($is_allowed)) {
            $allowed = true;
        }
    }
    if (!isset($allowed)) {
        echo "You are not authorized to view this page.";
        exit;
    }
}

// connect to db //

tc_connect();

// include css and timezone offset//

if (($use_client_tz == "yes") && ($use_server_tz == "yes")) {
    echo 'Please reconfigure your config.inc.php file, you cannot have both $use_client_tz AND $use_server_tz set to \'yes\'';
    exit;
}

echo "<head>\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';


echo '<script type="text/javascript" src="/scripts/jquery-3.1.1.min.js"></script>';
// different css for employee login page
if ($_SERVER['REQUEST_URI'] == '/timeclock.php') {
  echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/gradient.css" id="theme"/>';
  echo '<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>';
  echo '<script type="text/javascript" src="/scripts/wiisari.js"></script>';
} else {
  echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/default.css" id="theme" />';
}

if (isset($_SESSION['logged_in_user']) && $_SERVER['REQUEST_URI'] == '/mypage.php') {
  echo '<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>';
  echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">';
  echo '<script src="/scripts/datepicker-fi.js"></script>';
  echo '<script type="text/javascript" src="/scripts/mypage.js"></script>';
  echo '<script type="text/javascript" src="/scripts/Chart.bundle.min-v2.7.3.js"></script>';
  echo '<script type="text/javascript" src="/scripts/chartjs-plugin-deferred.min.js"></script>';
  if ($_SESSION['logged_in_user']->isSuperior()){
    include "$_SERVER[DOCUMENT_ROOT]/scripts/dropdown_get_reports.php";
  }
}
else if ($_SERVER['REQUEST_URI'] == '/timeeditor/time_editor.php')  {
  echo '<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>';
  echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">';
  echo '<script src="/scripts/datepicker-fi.js"></script>';
  /* materialize stylesheet & icon font */
  /*echo '<link rel="stylesheet" href="/scripts/tablesorter/materialize.min.css">';*/
  echo '<link rel="stylesheet" href="/scripts/tablesorter/icon.css">';
  /* materialize theme */
  echo '<link rel="stylesheet" href="/scripts/tablesorter/theme.materialize.css">';
  /* tablesorter plugin */
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.js"></script>';
  /* tablesorter widget file */
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.widgets.js"></script>';
  /* pager plugin */
  echo '<link rel="stylesheet" href="/scripts/tablesorter/jquery.tablesorter.pager.css">';
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.pager.js"></script>';
}

//echo "<link rel='stylesheet' type='text/css' media='print' href='css/print.css' />\n";
echo '<link rel="shortcut icon" href="/images/icons/wiisari_title.png" type="image/x-icon"/>';
echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">';


// set refresh rate for each page //
if ($refresh == "none") {
    echo "</head>\n";
} else {
    echo "<meta http-equiv='refresh' content=\"$refresh;URL=timeclock.php\">\n";
    echo "<script language=\"javascript\" src=\"scripts/pnguin_timeclock.js\"></script>\n";
    echo "</head>\n";
}

//date_default_timezone_set('Europe/Helsinki');
//setTimeZone();

?>
<body>

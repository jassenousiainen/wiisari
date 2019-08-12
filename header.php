<?php

require 'common.php';

//ob_start();
echo "<html>\n";


// connect to db //
tc_connect();

echo "<head>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>\n";

$current = $_SERVER['PHP_SELF'];


/* ----- JQuery ----- */
echo "<script type='text/javascript' src='/scripts/jquery-3.1.1.min.js'></script>\n";
echo '<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>'."\n";
if ($current != '/timeclock.php') {
  echo '<link rel="stylesheet" href="/scripts/jquery-ui.min.css">'."\n";
  echo '<script src="/scripts/datepicker-fi.js"></script>'."\n";
}
/* ------------------ */


/* ----- CSS ----- */
if ($current == '/timeclock.php') {
  echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/gradient.css" id="theme"/>'."\n";
} else {
  echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/default.css" id="theme" />'."\n"; 
}
if ($current == '/employees/employeeinfo.php' || $current == '/barcode-generator/barcodefetch.php') {
  echo '<link rel="stylesheet" type="text/css" href="/css/barcode-generator.css"/>'."\n";
}
/* --------------- */


/* ----- Custom JS scripts ----- */
if ($current == '/timeclock.php') {
  echo '<script type="text/javascript" src="/scripts/wiisari.js"></script>'."\n";
} 
else if ($current == '/mypage.php') {
  echo '<script type="text/javascript" src="/scripts/mypage.js"></script>'."\n";
} 
else if ($current == '/employees/employeecreate.php') {
  echo '<script type="text/javascript" src="/scripts/employeecreate.js"></script>'."\n";
} 
else if ($current == '/employees/employeeinfo.php') {
  echo '<script type="text/javascript" src="/scripts/employeeinfo.js"></script>'."\n";
}
if ($current != '/timeclock.php') {
  echo '<script type="text/javascript" src="/scripts/menu.js"></script>'."\n";
}
/* ----------------------------- */


/* ----- Plugins ----- */
if ($current == '/mypage.php') {
  /* chartJS */
  echo '<script type="text/javascript" src="/scripts/Chart.bundle.min-v2.7.3.js"></script>'."\n";
  echo '<script type="text/javascript" src="/scripts/chartjs-plugin-deferred.min.js"></script>'."\n";
}
if ($current != '/timeclock.php') {
  /* Tablesorter */
  echo '<link rel="stylesheet" href="/css/wiisari.tablesorter.css">'."\n";
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.js"></script>'."\n";
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.widgets.js"></script>'."\n";
  echo '<link rel="stylesheet" href="/scripts/tablesorter/jquery.tablesorter.pager.css">'."\n";
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.pager.js"></script>'."\n";
}

echo '<link rel="stylesheet" href="/fonts/fontawesome/css/all.min.css">'."\n";
/* ------------------- */


echo '<link rel="shortcut icon" href="/images/wiisari_title.png" type="image/x-icon"/>'."\n";

echo '
</head>
<body class="side">';

?>

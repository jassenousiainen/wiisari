<?php

require 'common.php';

//ob_start();
echo "<html>\n";


// connect to db //
tc_connect();

echo "<head>\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';


/* ----- JQuery ----- */
echo '<script type="text/javascript" src="/scripts/jquery-3.1.1.min.js"></script>';
echo '<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>';
if ($_SERVER['REQUEST_URI'] != '/timeclock.php') {
  echo '<link rel="stylesheet" href="/scripts/jquery-ui.min.css">';
  echo '<script src="/scripts/datepicker-fi.js"></script>';
}
/* ------------------ */


/* ----- CSS ----- */
if ($_SERVER['REQUEST_URI'] == '/timeclock.php') {
  echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/gradient.css" id="theme"/>';
} else {
  echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/default.css" id="theme" />'; 
}
if ($_SERVER['REQUEST_URI'] == '/employees/employeeinfo.php' || $_SERVER['REQUEST_URI'] == '/barcode-generator/barcodefetch.php') {
  echo '<link rel="stylesheet" type="text/css" href="/css/barcode-generator.css"/>';
}
/* --------------- */


/* ----- Custom JS scripts ----- */
if ($_SERVER['REQUEST_URI'] == '/timeclock.php') {
  echo '<script type="text/javascript" src="/scripts/wiisari.js"></script>';
} 
else if ($_SERVER['REQUEST_URI'] == '/mypage.php') {
  echo '<script type="text/javascript" src="/scripts/mypage.js"></script>';
} 
else if ($_SERVER['REQUEST_URI'] == '/employees/employeecreate.php') {
  echo '<script type="text/javascript" src="/scripts/employeecreate.js"></script>';
} 
else if ($_SERVER['REQUEST_URI'] == '/employees/employeeinfo.php') {
  echo '<script type="text/javascript" src="/scripts/employeeinfo.js"></script>';
}
/* ----------------------------- */


/* ----- Plugins ----- */
if ($_SERVER['REQUEST_URI'] == '/mypage.php') {
  /* chartJS */
  echo '<script type="text/javascript" src="/scripts/Chart.bundle.min-v2.7.3.js"></script>';
  echo '<script type="text/javascript" src="/scripts/chartjs-plugin-deferred.min.js"></script>';
}
if ($_SERVER['REQUEST_URI'] != '/timeclock.php') {
  /* Tablesorter */
  echo '<link rel="stylesheet" href="/css/wiisari.tablesorter.css">';
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.js"></script>';
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.widgets.js"></script>';
  echo '<link rel="stylesheet" href="/scripts/tablesorter/jquery.tablesorter.pager.css">';
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.pager.js"></script>';
}

echo '<link rel="stylesheet" href="/fonts/fontawesome/css/all.min.css">';
/* ------------------- */


echo '<link rel="shortcut icon" href="/images/wiisari_title.png" type="image/x-icon"/>';

?>
<body>

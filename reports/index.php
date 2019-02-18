<?php
include '../admin/header.php';
session_start();
include 'topmain.php';

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

require '../common.php';

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->reports == 0) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}



echo "<title>$title - Reports</title>\n";

echo "
<div class='raportitValinta'>
<h2>Hae tuntiraportit:</h2>
<br/>
<a class='raporttiLinkki' href='total_hours.php'>Työtunnit työntekijöittäin</a>
<br> </br>
<a class='raporttiLinkki' href='timerpt.php'>Päivittäiset tapahtumat</a>
<br> </br>
<a class='raporttiLinkki' href='audit.php'>Audit Log</a>
</div>
";
//include '../footer.php';
?>

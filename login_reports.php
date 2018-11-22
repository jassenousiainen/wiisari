<?php
session_start();

require 'common.php';
include 'header.php';
include 'topmain.php';
echo "<title>$title - Reports Login</title>\n";

$self = $_SERVER['PHP_SELF'];

if (isset($_POST['login_userid']) && (isset($_POST['login_password']))) {
    $login_userid = $_POST['login_userid'];
    $login_password = crypt($_POST['login_password'], 'xy');

    $query = "select empfullname, employee_passwd, reports from " . $db_prefix . "employees
              where empfullname = '" . $login_userid . "'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

    while ($row = mysqli_fetch_array($result)) {

        $reports_username = "" . $row['empfullname'] . "";
        $reports_password = "" . $row['employee_passwd'] . "";
        $reports_auth = "" . $row['reports'] . "";
    }

    if (($login_userid == @$reports_username) && ($login_password == @$reports_password) && ($reports_auth == "1")) {
        $_SESSION['valid_reports_user'] = $login_userid;
    }

}

if (isset($_SESSION['valid_reports_user'])) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = 'reports/index.php';</script>";
    exit;

} else {

    // build form

    echo "<div class='loginAdmin'>
      <form name='auth' method='post' action='$self'>
        <h2>Kirjaudu raporttinäkymään</h2>
        <br/>
        <div class='field'>
          <label>Käyttäjätunnus: </label>
          <input type='text' name='login_userid'>
        </div>
        <div class='field'>
          <label>Salasana: </label>
          <input type='password' name='login_password'>
        </div>
        <br/>
        <button type='submit' onClick='admin.php'>Kirjaudu</button>";

    if (isset($login_userid)) {
        echo "<p style='color:red;'>Käyttäjätunnus ja/tai salasana on väärin</p>";
    }
    echo "</form>\n";
    echo "</div>";
    echo "<script language=\"javascript\">document.forms['auth'].login_userid.focus();</script>\n";
}

echo "</body>\n";
echo "</html>\n";
?>

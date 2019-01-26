<?php
session_start();

require 'common.php';
include 'header.php';
include 'topmain.php';
echo "<title>Admin Login</title>\n";

$self = $_SERVER['PHP_SELF'];

if (isset($_POST['login_userid']) && (isset($_POST['login_password']))) {
    $login_userid = $_POST['login_userid'];
    $login_password = crypt($_POST['login_password'], 'xy');

    $query = "select empfullname, employee_passwd, admin, time_admin from " . $db_prefix . "employees
              where empfullname = '" . $login_userid . "'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

    while ($row = mysqli_fetch_array($result)) {

        $admin_username = "" . $row['empfullname'] . "";
        $admin_password = "" . $row['employee_passwd'] . "";
        $admin_auth = "" . $row['admin'] . "";
        $time_admin_auth = "" . $row['time_admin'] . "";
    }

    if (($login_userid == @$admin_username) && ($login_password == @$admin_password) && ($admin_auth == "1")) {
        $_SESSION['valid_user'] = $login_userid;
    } elseif (($login_userid == @$admin_username) && ($login_password == @$admin_password) && ($time_admin_auth == "1")) {
        $_SESSION['time_admin_valid_user'] = $login_userid;
    }

}

if (isset($_SESSION['valid_user'])) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = 'admin/index.php';</script>";
    exit;
} elseif (isset($_SESSION['time_admin_valid_user'])) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = 'admin/timeadmin.php';</script>";
    exit;

} else {

    // build form

    echo "<div class='loginBox'>
      <form name='auth' method='post' action='$self'>
        <h2>Kirjaudu Hallintaan</h2>
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

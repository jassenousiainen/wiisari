<?php
session_start();

echo "<html>\n";

require 'common.php';


$self = $_SERVER['PHP_SELF'];


// This works similarly to: if ($request=POST)
if (isset($_POST['login_userid']) && (isset($_POST['login_password']))) {
    include 'header.php';

    $login_userid = $_POST['login_userid'];
    $login_password = crypt($_POST['login_password'], 'xy');

    $query = "select empfullname, employee_passwd, admin, time_admin , reports from " . $db_prefix . "employees
              where empfullname = '" . $login_userid . "'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

    while ($row = mysqli_fetch_array($result)) {
        $admin_username = "" . $row['empfullname'] . "";
        $admin_password = "" . $row['employee_passwd'] . "";
        $admin_auth = "" . $row['admin'] . "";
        $time_admin_auth = "" . $row['time_admin'] . "";
        $reports_auth = "" . $row['reports'] . "";
    }

    // Sets the logged-in status(es) to session variable
    if ( ($login_userid == @$admin_username) && ($login_password == @$admin_password) ) {
      if ( $admin_auth == "1" ) {
        $_SESSION['valid_user'] = $login_userid;
        $_SESSION['logged_in'] = $login_userid;
      }
      if ( $time_admin_auth == "1" ) {
        $_SESSION['time_admin_valid_user'] = $login_userid;
        $_SESSION['logged_in'] = $login_userid;
      }
      if ( $reports_auth == "1" ) {
        $_SESSION['valid_reports_user'] = $login_userid;
        $_SESSION['logged_in'] = $login_userid;
      }
    }
}


if (isset($_SESSION['logged_in'])) {
  include 'topmain.php';
    echo "<h2>Tervetuloa, " .$_SESSION['logged_in']. "</h2>
    <p>You have access to these pages:</p>";

    if ( isset($_SESSION['valid_user']) ) {
      echo '<a href="/admin/index.php">Hallinta</a><br>';
    }
    if ( isset($_SESSION['time_admin_valid_user']) ) {
      echo '<a href="/admin/timeadmin.php">Aikaeditori</a><br>';
    }
    if ( isset($_SESSION['valid_reports_user']) ) {
      echo '<a href="/reports/index.php">Tuntiraportit</a><br>';
    }
    exit;

} else {  // This part is run if there is no users logged in in this session
    //include 'topmain.php';

    echo '
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <title>Wiisari - Login</title>
      <link rel="stylesheet" type="text/css" media="screen" href="css/default.css" id="theme"
      <link rel="shortcut icon" href="images/icons/wiisari_title.png" type="image/x-icon"/>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    </head>';
    echo "<body class='loginPage'>";
    // build form
    echo "
      <form class='loginBox' name='auth' method='post' action='$self'>
        <img src='/images/icons/wiisari_title.png'>
        <h2>Kirjaudu Wiisariin</h2>
        <p>Kirjutumalla pääset hallintapaneeliin ja raporttinäkymään</p>
        <input type='text' name='login_userid' placeholder='Käyttäjätunnus'>
        <input type='password' name='login_password' placeholder='Salasana'>
        <button type='submit'>Kirjaudu</button>
        <a href='/index.php'>Takaisin etusivulle</a>";

    if (isset($login_userid)) {
        echo "<p style='color:red;'>Käyttäjätunnus ja/tai salasana on väärin</p>";
    }

    echo "</form>";
}

echo "</body>\n";
echo "</html>\n";
?>

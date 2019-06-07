<?php

require 'common.php';

session_start();
tc_connect();

$self = $_SERVER['PHP_SELF'];

// Login with adminrights (level > 0)
if (isset($_POST['login_userid']) && (isset($_POST['login_password']))) {
    $login_userid = $_POST['login_userid'];
    $admin_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE userID = '$login_userid'"));
    if ( ($login_userid == $admin_data[0]) && password_verify($_POST['login_password'].$salt , $admin_data[4]) && $admin_data[3] >= 1) {
        $_SESSION['logged_in_user'] = new User($login_userid, $admin_data[3]);
    }
} // Login as employee (level == 0)
else if (isset($_POST['login_userid'])) {
  $login_userid = tc_select_value("userID", "employees", "userID = ?", $_POST['login_userid']);
  if ( has_value($login_userid) ) {
    $_SESSION['logged_in_user'] = new User($login_userid, 0);
  }
}

// If user is already logged in, redirect to mypage
if ( isset($_SESSION['logged_in_user']) ) {
  echo "<script type='text/javascript' language='javascript'> window.location.href = '/mypage.php';</script>";
  exit;
} else {  // This part is run if there is no user logged in in this session
    echo "<html>\n";
    echo '
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <title>Wiisari - Login</title>
      <link rel="stylesheet" type="text/css" media="screen" href="css/default.css" id="theme"/>
      <script type="text/javascript" src="/scripts/jquery-3.1.1.min.js"></script>
      <script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>
      <script type="text/javascript" src="/scripts/loginpage.js"></script>
      <link rel="shortcut icon" href="images/icons/wiisari_title.png" type="image/x-icon"/>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    </head>';
    echo "<body class='loginPage'>
      <div class='skew-container top'> <div class='skew-bg'></div> </div>
      <div id='chooseLogin'>
        <h2 class='wiisari'>WIISARI</h2>
        <h2>Valitse kirjautumissivu</h2>
        <a id='admin' class='btn tile'><i class='fas fa-toolbox'></i><span>Hallinta</span></a>
        <a id='employee' class='btn tile'><i class='fas fa-user'></i><span>Työntekijä</span></a>
        <br>
        <a class='link' href='/index.php'>Takaisin etusivulle</a>
      </div>";
    // employee form
    echo "
    <div id='employeeSlideLogin'>
      <form class='loginBox' name='auth' method='post' action='$self'>
        <h2 class='wiisari'>WIISARI</h2>
        <h2>Kirjaudu Wiisariin</h2>
        <p>Kirjautumalla pääset omalle sivulle</p>
        <input type='password' name='login_userid' autocomplete='off' placeholder='Käyttäjätunnus/viivakoodi'>";
        if (isset($_POST['login_userid']) && !isset($_POST['login_password'])) {
            echo "<p style='color:red;'>Käyttäjätunnuksella ei löytynyt ketään</p>";
        }
    echo "<button type='submit'>Kirjaudu</button>
        <a class='link' id='employeeSlideBack'><i class='fas fa-arrow-circle-left'></i></a>
      </form></div>";
    // admin form
    echo "
    <div id='adminSlideLogin'>
      <form class='loginBox' name='auth' method='post' action='$self'>
        <h2 class='wiisari'>WIISARI</h2>
        <h2>Kirjaudu Wiisariin</h2>
        <p>Kirjautumalla pääset omalle sivulle, hallintapaneeliin ja raporttinäkymään</p>
        <input type='text' name='login_userid' placeholder='Käyttäjätunnus'>
        <input type='password' name='login_password' placeholder='Salasana'>";
        if (isset($_POST['login_password'])) {
            echo "<p style='color:red;'>Käyttäjätunnus ja/tai salasana on väärin</p>";
        }
    echo "<button type='submit'>Kirjaudu</button>
        <a class='link' id='adminSlideBack'><i class='fas fa-arrow-circle-right'></i></a>
      </form></div>";
    echo "<div class='skew-container bottom'> <div class='skew-bg'></div> </div>";
}
echo "</body>\n";
echo "</html>\n";
?>
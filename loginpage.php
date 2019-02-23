<?php

require 'common.php';
session_start();

$self = $_SERVER['PHP_SELF'];


// This works similarly to: if ($request=POST)
// Admin or Time-admin or reports-user
if (isset($_POST['login_userid']) && (isset($_POST['login_password']))) {
    tc_connect();

    $login_userid = $_POST['login_userid'];
    $login_password = crypt($_POST['login_password'], 'xy');

    $admin_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE empfullname = '$login_userid'"));

    if ( ($login_userid == $admin_data[0]) && ($login_password == $admin_data[2]) && ( $admin_data[8] == '1' || $admin_data[9] == '1' || $admin_data[10] == '1')) {
        $_SESSION['logged_in_user'] = new User($login_userid);
    }
}
// Employee (=Doesn't have any admin rights)
else if ( isset($_POST['login_barcode']) ) {

  $login_barcode = $_POST['login_barcode'];
  $login_barcode_userid = tc_select_value("empfullname", "employees", "barcode = ?", $login_barcode);

  if ( has_value($login_barcode_userid) ) {
    $_SESSION['logged_in_user'] = new User($login_barcode_userid);
  }
}



if ( isset($_SESSION['logged_in_user']) ) {
  echo "<script type='text/javascript' language='javascript'> window.location.href = '/mypage.php';</script>";
  exit;


} else {  // This part is run if there is no users logged in in this session

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

      <div id='chooseLogin'>
        <img src='/images/icons/wiisari_title.png'>
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
        <img src='/images/icons/wiisari_title.png'>
        <h2>Kirjaudu Wiisariin</h2>
        <p>Kirjautumalla pääset omalle sivulle</p>
        <input type='password' name='login_barcode' autocomplete='off' placeholder='Käyttäjätunnus/viivakoodi'>";
        if (isset($login_barcode)) {
            echo "<p style='color:red;'>Käyttäjätunnuksella ei löytynyt ketään</p>";
        }
    echo "<button type='submit'>Kirjaudu</button>
        <a class='link' id='employeeSlideBack'><i class='fas fa-arrow-circle-left'></i></a>
      </form></div>";


    // admin form
    echo "
    <div id='adminSlideLogin'>
      <form class='loginBox' name='auth' method='post' action='$self'>
        <img src='/images/icons/wiisari_title.png'>
        <h2>Kirjaudu Wiisariin</h2>
        <p>Kirjautumalla pääset omalle sivulle, hallintapaneeliin ja raporttinäkymään</p>
        <input type='text' name='login_userid' placeholder='Käyttäjätunnus'>
        <input type='password' name='login_password' placeholder='Salasana'>";
        if (isset($login_userid)) {
            echo "<p style='color:red;'>Käyttäjätunnus ja/tai salasana on väärin</p>";
        }
    echo "<button type='submit'>Kirjaudu</button>
        <a class='link' id='adminSlideBack'><i class='fas fa-arrow-circle-right'></i></a>
      </form></div>";
}

echo "</body>\n";
echo "</html>\n";
?>

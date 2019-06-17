<?php

require 'common.php';

session_start();
pdo_connect();

$self = $_SERVER['PHP_SELF'];

// Login with adminrights (level > 0)
if (isset($_POST['login_userid']) && (!empty($_POST['login_password']))) {
  $getuser_stmt = $pdo->prepare("SELECT userID, level, adminPassword FROM employees WHERE userID = ?");
  $getuser_stmt->execute(array($_POST['login_userid']));
  $row = $getuser_stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    if (password_verify($_POST['login_password'].$salt, $row['adminPassword'])) {
      $_SESSION['logged_in_user'] = new User($row['userID'], $row['level']);
    }
  }
} // Login as employee (level == 0)
else if (isset($_POST['login_userid'])) {
  $getuser_stmt = $pdo->prepare("SELECT userID FROM employees WHERE userID = ?");
  $getuser_stmt->execute(array($_POST['login_userid']));
  $row = $getuser_stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    $_SESSION['logged_in_user'] = new User($row['userID'], 0);
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
      <link rel="stylesheet" type="text/css" media="screen" href="css/loginpage.css"/>
      <link rel="stylesheet" type="text/css" media="screen" href="css/loginpage.normalize.css"/>
      <script type="text/javascript" src="/scripts/jquery-3.1.1.min.js"></script>
      <script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>
      <script type="text/javascript" src="/scripts/loginpage.js"></script>
      <link rel="shortcut icon" href="images/icons/wiisari_title.png" type="image/x-icon"/>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    </head>';
    echo "<body class='loginPage'>
      <div class='skew-container top'> <div class='skew-bg'></div> </div>";
    echo "
    <div id='adminSlideLogin'>
      <form class='loginBox' name='auth' method='post' action='$self'>
        <h2 class='wiisari'>WIISARI</h2>
        <h2>Kirjaudu Wiisariin</h2>
        <p>Kirjautumalla pääset omalle sivulle. Jos olet valvoja täytä myös salasana.</p>
        <span class='input input--hoshi'>
					<input name='login_userid' class='input__field input__field--hoshi' type='text' id='input-4' autocomplete='off' />
					<label class='input__label input__label--hoshi input__label--hoshi-color-1' for='input-4'>
						<span class='input__label-content input__label-content--hoshi'>Käyttäjätunnus</span>
					</label>
        </span>";
    echo '
        <span class="input input--hoshi">
					<input name="login_password" class="input__field input__field--hoshi" type="password" id="input-5" />
					<label class="input__label input__label--hoshi input__label--hoshi-color-3" for="input-5">
						<span class="input__label-content input__label-content--hoshi">Salasana (valvoja)</span>
					</label>
				</span>';
        if (isset($_POST['login_password'])) {
            echo "<p style='color:red;'>Käyttäjätunnus ja/tai salasana on väärin</p>";
        }
    echo "<button type='submit'>Kirjaudu</button>
          <a class='link' href='/timeclock.php'>Takaisin etusivulle</a>
      </form></div>";
    echo "<div class='skew-container bottom'> <div class='skew-bg'></div> </div>";
}
echo "</body>\n";
echo "</html>\n";
?>
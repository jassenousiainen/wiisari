<?php
require '../common.php';
echo "<head>
        <link rel='stylesheet' type='text/css' media='screen' href='../css/default.css' id='theme'/>
        <link rel='shortcut icon' href='../images/icons/wiisari_title.png' type='image/x-icon'/>
        <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.2/css/all.css' integrity='sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr' crossorigin='anonymous'>
      </head>";

session_start();
include 'topmain.php';



if (!isset($_SESSION['logged_in_user']) || !$_SESSION['logged_in_user']->isSuperior()) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

echo "<title>$title - Töissä olevat työntekijät</title>\n";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (!isset($_POST['office_name']) || !isset($_POST['group_name']) || !isset($_POST['user_name'])) {
    echo '<h2>Virhe! Työntekijälista on tyhjä.</h2>';
    exit;
  }

  $office = $_POST['office_name'];
  $group = $_POST['group_name'];
  /*$user = $_POST['user_name'];

  if ($user != 'All') {
    $query = tc_query("SELECT * FROM employees WHERE inout_status = 'in' AND empfullname = '$user'");
  }
  else*/ if ($group != 'All') {
    $query = tc_query("SELECT * FROM employees WHERE inout_status = 'in' AND groups = '$group' ORDER BY tstamp DESC");
  }
  else if ($office != 'All') {
    $query = tc_query("SELECT * FROM employees WHERE inout_status = 'in' AND office = '$office' ORDER BY tstamp DESC");
  }
  else {
    $query = tc_query("SELECT * FROM employees WHERE inout_status = 'in' ORDER BY tstamp DESC");
  }

  echo '<section class="container">
          <div class="middleContent">
            <div class="box">
              <h2>Tällä hetkellä töissä olevat työntekijät</h2>
              <div class="section">
                <table>
                  <tr>
                    <th style="text-align:left;">Nimi</th>
                    <th style="text-align:left;">Sisään klo</th>
                    <th style="text-align:left;">Toimisto</th>
                    <th style="text-align:left;">Osasto</th>
                  </tr>';

                  $count = 0;

                  while ($emp = mysqli_fetch_row($query)) {
                    $count ++;

                    $logTime = new DateTime();
                    $logTime->setTimestamp($emp[1]);
                    $logTime->setTimeZone(new DateTimeZone('Europe/Helsinki'));

                    if ($count%2 == 1) {
  echo '              <tr style="background-color: white; height:33px;">';
                    } else {
  echo '              <tr style="background-color: var(--light); height:33px;">';
                    }

  echo '              <td>'.$emp[3].'</td>
                      <td>'.$logTime->format("H:i").' ('.$logTime->format("d.m.Y").')</td>
                      <td>'.$emp[7].'</td>
                      <td>'.$emp[6].'</td>
                    <tr>';
                  }

  echo '        </table>
              </div>
            </div>
          </div>
        </section>';

}

?>

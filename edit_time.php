<?php
include 'header.php';
session_start();
include 'topmain.php';

echo "<title>Kellotuseditori</title>\n";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->time_admin == 0) {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
    exit;
}

if ($request == 'GET') {

  $employee_query = tc_query("SELECT * FROM employees WHERE disabled = 0 ORDER BY displayname ASC");

echo '<section class="container">
        <div class="mainBox">
          <div>
            <h2>Kellotuseditori - valitse työntekijä</h2>
            <div class="section">
            <form action="/edit_time.php" method="post">
              <table>';

  while ( $employee = mysqli_fetch_array($employee_query) ) {
    echo        '<tr>
                  <td style="width:80%;">'.$employee[3].'</td>
                  <td><button class="btn" value="'.$employee[0].'"type="submit" name="emp">Valitse</button></td>
                </tr>';
  }

echo '          </table>
              </form>
            </div>
          </div>
        </div>
      </section>';

}

?>

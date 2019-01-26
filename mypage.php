<?php
session_start();

require 'common.php';
include 'header.php';

echo "<title>WIISARI - Oma sivu</title>\n";
include 'topmain.php';


function employees_total_count() {
  $employees_total = 0;
  $query_total = tc_query("SELECT * FROM employees WHERE disabled = '0'");
  while (mysqli_fetch_array($query_total)) {
    $employees_total += 1;
  }
  return $employees_total;
}
function employees_total_in_count() {
  $employees_total_in = 0;
  $query_total_in = tc_query("SELECT * FROM employees WHERE disabled = '0' AND inout_status = 'in'");
  while (mysqli_fetch_array($query_total_in)) {
    $employees_total_in += 1;
  }
  return $employees_total_in;
}


// User can't access the page unless they are logged in
if (isset($_SESSION['logged_in'])) {
  $logged_in_user = $_SESSION['logged_in'];
  $fullname = tc_select_value("empfullname", "employees", "empfullname = ?", $logged_in_user);
  $displayname = tc_select_value("displayname", "employees", "empfullname = ?", $logged_in_user);
  $logged_in_barcode = tc_select_value("barcode", "employees", "empfullname = ?", $logged_in_user);
  $inout_status = tc_select_value("inout_status", "employees", "empfullname = ?", $logged_in_user);


  echo '
  <section class="mypageHead">
    <div>
      <h2>Tervetuloa omalle sivulle '.$displayname.'</h2>
      <p>Täällä näet omat tietosi ja tilastosi Wiisarissa.
      <br>Voit myös helposti kellottaa itsesi sisään tai ulos.</p>
      <p>
        <a id="primary" href="/index.php">Etusivulle</a>
        <a id="secondary" href="logout.php">Kirjaudu ulos</a>
      </p>
    </div>
  </section>';


  echo '<section class="container">';


  echo '<div class="leftInfo">
          <h2>Kellotus</h2>
          <form class="mypage_inout"action="inout.php" method="post">';
          echo '<input type="text" style="display:none;" name="mypage" value="mypage">';
          if ($inout_status == "in") {
            echo '<p>Olet tällä hetkellä kirjautuneena sisään</p>
            <input type="password" style="display:none;" name="left_barcode"value="'.$logged_in_barcode.'" autocomplete="off">
            <button id="out" class="fas fa-sign-out-alt" type="submit"></button>';
          } else {
            echo '<p>Olet tällä hetkellä kirjautuneena ulos</p>
            <input type="password" style="display:none;" name="left_barcode"value="'.$logged_in_barcode.'" autocomplete="off">
            <button id="in" class="fas fa-sign-in-alt" type="submit"></button>';
          }
  echo '</form></div>';


  echo '<div class="mainBox">';

  if (isset($_SESSION['valid_user']) || isset($_SESSION['valid_reports_user']) || isset($_SESSION['time_admin_valid_user']) ){
    echo '
    <div class="admin">
      <p>Työntekijä tilastot:</p>
      <p>Työntekijöitä yhteensä: '.employees_total_count().'</p>
      <p>Työntekijöitä nyt kirjautuneena: '.employees_total_in_count().'</p>
    </div>';
  }

  echo '</div>';


  echo '<div class="rightInfo">
          <h2>Omat tiedot</h2>';

  echo '</div>';



  echo '</section>';

} else {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
}
?>

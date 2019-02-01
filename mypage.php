<?php

require 'common.php';
session_start();
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
if (isset($_SESSION['logged_in_user'])) {

  echo '
  <section class="mypageHead">
    <div>
      <h2>Tervetuloa omalle sivulle '.$_SESSION['logged_in_user']->displayname.'</h2>
      <p>Täällä näet omat tietosi ja tilastosi Wiisarissa.
      <br>Voit myös helposti kellottaa itsesi sisään tai ulos.</p>
      <p>
        <a id="primary" class="btn" href="/index.php">Etusivulle</a>
        <a id="secondary" class="btn" href="/logout.php">Kirjaudu ulos</a>
      </p>
    </div>
  </section>';


  echo '<section class="container">';


  echo '<div class="leftInfo">
          <h2>Kellotus</h2>
          <div class="section">
          <form class="mypage_inout"action="inout.php" method="post">';
          echo '<input type="text" style="display:none;" name="mypage" value="mypage">';
          if ($_SESSION['logged_in_user']->getInoutStatus() == "in") {
            echo '<div class="workTime">Olet ollut töissä nyt: <br> <span id="secs">'.$_SESSION['logged_in_user']->getCurrentWorkTime().'</span></div>';
            echo '<p>Kellota itsesi ulos:</p>
            <input type="password" style="display:none;" name="left_barcode"value="'.$_SESSION['logged_in_user']->barcode.'" autocomplete="off">
            <button id="out" class="fas fa-sign-out-alt" type="submit"></button>';
          } else {
            echo '<p>Kellota itsesi sisään:</p>
            <input type="password" style="display:none;" name="left_barcode"value="'.$_SESSION['logged_in_user']->barcode.'" autocomplete="off">
            <button id="in" class="fas fa-sign-in-alt" type="submit"></button>';
          }
  echo '  <textarea type="text" id="notes" name="notes" autocomplete="off" placeholder="Kirjoita halutessasi viesti, jonka haluat liittää mukaan tähän kirjaukseen."></textarea>
          </form>
          </div>
        </div>';


  echo '<div class="mainBox">';

  if ($_SESSION['logged_in_user']->isBasicAdmin()){
    echo '
    <div class="admin">
      <h2>Hallinnan toiminnot</h2>
      <p>Työntekijätilastot:</p>
      <p>Työntekijöitä yhteensä: '.employees_total_count().'</p>
      <p>Työntekijöitä nyt kirjautuneena: '.employees_total_in_count().'</p>
    </div>';
  }

    echo '<div class="first">
            <h2>Omat tunnit</h2>
              <p class="section">
                Hae nopea tuntiraportti, josta näet kuluvan vuoden tehdyt työtunnit.
                <br>
                <br>
                <a class="btn" href="/reports/quickreport.php">Nopea raportti</a>
              </p>';
      echo "  <div class='section'>
                Hae täysi tuntiraportti valitsemallasi aikavälillä.
                <br><br>
                <form name='form' action='/reports/personalreport.php' method='post' onsubmit=\"return isFromOrToDate();\">
                  <input type='text' id='from' autocomplete='off' size='10' maxlength='10' name='from_date' placeholder='välin alku'>
                  <input type='text' id='to' autocomplete='off' size='10' maxlength='10' name='to_date' placeholder='välin loppu'>
                  <br>
                  <label for='tmp_show_details'>Näytä yksittäiset kirjaukset</label>
                  <input type='checkbox' name='tmp_show_details' value='1' ".(yes_no_bool($show_details) ? ' checked' : '')." style='height:15px; width:20px; float:none;'>
                  <br><br>
                  <button class='btn' type='submit' name='customreport'>Kustomoitu raportti</button>
                </form>
              </div>";
    echo '</div>';

    echo '<div class="second">';
      echo '<h2>Omat tilastot</h2>';
    echo '</div>';
  echo '</div>';



  echo '</section>';

} else {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
}
?>

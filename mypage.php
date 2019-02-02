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
            echo '<div class="workTime">Olet ollut töissä nyt: <br> <b><span id="secs">'.$_SESSION['logged_in_user']->getCurrentWorkTime().'</span></b></div>';
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
      <p class="section">
        Sinulla on pääsy seuraaville sivuille:';
        if ($_SESSION['logged_in_user']->admin == '1') {
          echo '<br><br><a class="btn" href="/admin/index.php">Hallintapaneeli</a>';
        }
        if ($_SESSION['logged_in_user']->reports == '1') {
          echo '<br><br><a class="btn" href="/reports/index.php">Raportit</a>';
        }
        if ($_SESSION['logged_in_user']->admin == '1' || $_SESSION['logged_in_user']->time_admin == '1') {
          echo '<br><br><a class="btn" href="/admin/timeadmin.php">Kellotuseditori</a>';
        }
    echo '
      </p>
      <p class="section">
        <b>Työntekijätilastot</b>
        <br><br>
        Työntekijöitä yhteensä: '.employees_total_count().'
        <br>
        Työntekijöitä nyt kirjautuneena: '.employees_total_in_count().'
      </p>
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
      echo '  <canvas id="weektimechart" width="980" height="490"></canvas>';

      $currentWeek = ltrim(date('W', time()), 0);
      $WeekWorkTime = $_SESSION['logged_in_user']->getWeekWorkTime();

      $len = count($WeekWorkTime);
      for ($i = 1; $i < $len; $i++) {
        $weekTime[$i] = round($WeekWorkTime[$i]/3600.0, 2);
      }

      $labels = "labels: ['viikko 1', 'viikko 2', 'viikko 3', 'viikko 4', 'viikko 5', 'viikko 6']";
      $data;
      if ($currentWeek == 1) {
        $data = "data: [".$weekTime[$currentWeek].", , , , , ]";
      } else if ($currentWeek == 2) {
        $data = "data: [".$weekTime[$currentWeek-1].", ".$weekTime[$currentWeek].", , , , ]";
      } else if ($currentWeek == 3) {
        $data = "data: [".$weekTime[$currentWeek-2].", ".$weekTime[$currentWeek-1].", ".$weekTime[$currentWeek].", , , ]";
      } else if ($currentWeek == 4) {
        $data = "data: [".$weekTime[$currentWeek-3].", ".$weekTime[$currentWeek-2].", ".$weekTime[$currentWeek-1].", ".$weekTime[$currentWeek].", , ]";
      } else if ($currentWeek == 5) {
        $data = "data: [".$weekTime[$currentWeek-4].", ".$weekTime[$currentWeek-3].", ".$weekTime[$currentWeek-2].", ".$weekTime[$currentWeek-1].", ".$weekTime[$currentWeek].", ]";
      } else {
        $labels = "labels: ['viikko ".($currentWeek-5)."', 'viikko ".($currentWeek-4)."', 'viikko ".($currentWeek-3)."', 'viikko ".($currentWeek-2)."', 'viikko ".($currentWeek-1)."', 'viikko ".$currentWeek."']";
        $data = "data: [".$weekTime[$currentWeek-5].", ".$weekTime[$currentWeek-4].", ".$weekTime[$currentWeek-3].", ".$weekTime[$currentWeek-2].", ".$weekTime[$currentWeek-1].", ".$weekTime[$currentWeek]."]";
      }
echo "
<script>
var ctx = document.getElementById('weektimechart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        ".$labels.",
        datasets: [{
            label: 'tuntia',
            ".$data.",
            lineTension: 0.2,
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 2,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            pointStyle: 'circle',
            pointBackgroundColor: '#3e95cd'
        }]
    },
    options: {
				responsive: true,
				title: {
					display: true,
					text: 'Työtuntisi viikoittain'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Viikko'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Tunnit'
						},
            ticks: {
              beginAtZero:true
            }
					}]
				}
			}
});
var canvas = document.getElementById('weektimechart');
window.onresize = function () {
    canvas.style.width = '100%';
    canvas.height = canvas.width * .5;
}
</script>";
    echo '</div>';
  echo '</div>';



  echo '</section>';

} else {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
}
?>

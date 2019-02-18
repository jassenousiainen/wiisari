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
        if ($_SESSION['logged_in_user']->admin == '1' || $_SESSION['logged_in_user']->time_admin == '1') {
          echo '<br><br><a class="btn" href="/admin/timeadmin.php">Kellotuseditori</a>';
        }
        if ($_SESSION['logged_in_user']->reports == '1') {
          echo '<br><br><a class="btn" href="/reports/total_hours.php">Työtunnit työntekijöittäin</a>';
          echo '<br><br><a class="btn" href="/reports/timerpt.php">Päivittäiset tapahtumat</a>';
          echo '<br><br><a class="btn" href="/reports/audit.php">Muutosloki</a>';
        }


    echo '
      </p>
      <p class="section" style="overflow:auto;">
        <canvas id="clockedinChart" width="400" height="200" style="max-width:400px; float:right"></canvas>
        <b>Työntekijätilastot</b>
        <br><br>
        Työntekijöitä yhteensä: '.employees_total_count().'
        <br>
        Työntekijöitä nyt kirjautuneena: '.employees_total_in_count().'
      </p>
      <div class="section">
        <b>Hae töissä olevat työntekijät</b>
        <br>
        <form name="getReport" action="/reports/in_employees.php" method="post">
          <select id="office" name="office_name" onchange="group_names();"></select>
          <select id="group" name="group_name" onchange="user_names();"></select>
          <select is="user" name="user_name"></select>
          <button class="btn" type="submit">Hae työntekijät</button>
        </form>
        <script type="text/javascript">office_names()</script>
      </div>
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
                  <input type='text' id='from' autocomplete='off' size='10' maxlength='10' name='from_date' placeholder='välin alku'> -
                  <input type='text' id='to' value='".date("d.n.Y")."'' autocomplete='off' size='10' maxlength='10' name='to_date' placeholder='välin loppu'>
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
      echo '<div class="section">';
      echo '  <canvas id="weektimechart" width="900" height="450"></canvas>';

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



echo '  </div>';
echo '<div class="section">';
echo '  <canvas id="monthtimechart" width="900" height="450"></canvas>';

$currentMonth = ltrim(date('W', time()), 0);
$monthWorkTime = $_SESSION['logged_in_user']->getMonthWorkTime();

for ($i = 1; $i < count($monthWorkTime); $i++) {
  $monthTime[$i] = round($monthWorkTime[$i]/3600.0, 2);
}

    echo '</div></div>';
  echo '</div>';



  echo '</section>';

  if ($_SESSION['logged_in_user']->isBasicAdmin()){
    $employeesIn = employees_total_in_count();
    $employeesOut = employees_total_count() - $employeesIn;
    echo "<script>
    var ctx1 = document.getElementById('clockedinChart').getContext('2d');
    var clockedinChart = new Chart(ctx1, {
      type: 'pie',
      data: {
        labels: ['Sisällä', 'Ulkona'],
        datasets: [{
            label: 'tuntia',
            data: [".$employeesIn.", ".$employeesOut."],
            lineTension: 0.2,
            backgroundColor: ['rgb(75, 192, 192)','rgb(255, 99, 132)'],
            borderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          deferred: {
            xOffset: 150,   // defer until 150px of the canvas width are inside the viewport
            yOffset: '50%', // defer until 50% of the canvas height are inside the viewport
            delay: 500      // delay of 500 ms after the canvas is considered inside the viewport
          }
        }
      }
    });
  </script>";
  }


  echo "<script>
  var ctx2 = document.getElementById('weektimechart').getContext('2d');
  var myChart = new Chart(ctx2, {
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
          plugins: {
            deferred: {
              xOffset: 150,   // defer until 150px of the canvas width are inside the viewport
              yOffset: '50%', // defer until 50% of the canvas height are inside the viewport
              delay: 500      // delay of 500 ms after the canvas is considered inside the viewport
            }
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


  var ctx3 = document.getElementById('monthtimechart').getContext('2d');
  var monthTimeChart = new Chart(ctx3, {
  type: 'bar',
  data: {
    labels: ['Tammikuu', 'Helmikuu', 'Maaliskuu', 'Huhtikuu', 'Toukokuu', 'Kesäkuu', 'Heinäkuu', 'Elokuu', 'Syyskuu', 'Lokakuu', 'Marraskuu', 'Joulukuu'],
    datasets: [{
        label: 'tuntia',
        data: [".$monthTime[1].", ".$monthTime[2].", ".$monthTime[3].", ".$monthTime[4].", ".$monthTime[5].", ".$monthTime[6].",
         ".$monthTime[7].", ".$monthTime[8].", ".$monthTime[9].", ".$monthTime[10].", ".$monthTime[11].", ".$monthTime[12]."],
        lineTension: 0.2,
        borderColor: 'rgb(255, 99, 132)',
        borderWidth: 2,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        pointStyle: 'circle',
        pointBackgroundColor: '#3e95cd'
    }]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: 'Työtuntisi kuukausittain'
    },
    plugins: {
      deferred: {
        xOffset: 150,   // defer until 150px of the canvas width are inside the viewport
        yOffset: '50%', // defer until 50% of the canvas height are inside the viewport
        delay: 500      // delay of 500 ms after the canvas is considered inside the viewport
      }
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
          labelString: 'Kuukausi'
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

  var weekCanvas = document.getElementById('weektimechart');
  var monthCanvas = document.getElementById('monthtimechart');

  monthCanvas.style.width = '100%';
  monthCanvas.height = monthCanvas.width * .5;
  weekCanvas.style.width = '100%';
  weekCanvas.height = weekCanvas.width * .5;

  window.onresize = function () {
    monthCanvas.style.width = '100%';
    monthCanvas.height = monthCanvas.width * .5;
    weekCanvas.style.width = '100%';
    weekCanvas.height = weekCanvas.width * .5;
  }
        </script>";

} else {
    echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
}
?>

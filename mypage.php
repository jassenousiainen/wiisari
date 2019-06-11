<?php

require 'common.php';
session_start();
include 'header.php';

// User can't access the page unless they are logged in
if (!isset($_SESSION['logged_in_user'])) {
  echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
  exit;
}

echo "<title>WIISARI - Oma sivu</title>\n";
include 'topmain.php';


function employees_total_count() {
  return mysqli_fetch_row(tc_query("SELECT COUNT(userID) FROM employees"))[0];
}
function employees_total_in_count() {
  return mysqli_fetch_row(tc_query("SELECT COUNT(userID) FROM employees WHERE inoutStatus = 'in'"))[0];
}


echo '<section class="container myPage">';


echo '<div class="leftContent">
          <div class="box">
            <h2>Kellotus</h2>
            <div class="section">
              <form class="mypage_inout"action="inout.php" method="post">';
                echo '<input type="text" style="display:none;" name="mypage" value="mypage">';
                if ($_SESSION['logged_in_user']->getInoutStatus() == "in") {
                  echo '<div class="workTime">Olet ollut töissä nyt: <br> <b><span id="secs">'.$_SESSION['logged_in_user']->getCurrentWorkTime().'</span></b></div>';
                  echo '<p>Kellota itsesi ulos:</p>
                  <input type="password" style="display:none;" name="userID" value="'.$_SESSION['logged_in_user']->userID.'" autocomplete="off">
                  <button id="out" class="fas fa-sign-out-alt" type="submit"></button>';
                } else {
                  echo '<p>Kellota itsesi sisään:</p>
                  <input type="password" style="display:none;" name="userID" value="'.$_SESSION['logged_in_user']->userID.'" autocomplete="off">
                  <button id="in" class="fas fa-sign-in-alt" type="submit"></button>';
                }
                echo '<textarea type="text" id="notes" name="notes" autocomplete="off" placeholder="Kirjoita halutessasi viesti, jonka haluat liittää mukaan tähän kirjaukseen."></textarea>
              </form>
            </div>
          </div>
        </div>';


  echo '<div class="middleContent">';

if ($_SESSION['logged_in_user']->level > 0) {
  echo '
    <div class="box">
      <h2 class="orange">Hallinnan toiminnot</h2>
      <p class="section">
        Sinulla on käytössäsi seuraavat toiminnot: <br>';
          echo '<a class="btn tile" href="/employees/employees.php"><i class="fas fa-id-card"></i><span>Henkilöstö</span></a>';
          echo '<a class="btn tile" href="/admin/office_group.php"><i class="fas fa-building"></i><span>Toimistot / Ryhmät</span></a>';
          echo '<a class="btn tile" href="/reports/total_hours.php"><i class="fas fa-hourglass-half"></i><span>Työtunnit</span></a>';
          echo '<a class="btn tile" href="/reports/timerpt.php"><i class="fas fa-calendar-week"></i><span>Päivittäiset tapahtumat</span></a>';


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
    </div>';
}


    echo '<div class="box">
            <h2 class="purple">Omat tunnit</h2>
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
                  <br><br>
                  <label class='container'>
                    Näytä yksittäiset kirjaukset
                    <input type='checkbox' name='tmp_show_details' value='1' ".(yes_no_bool($show_details) ? ' checked' : '')." class='check'>
                    <span class='checkmark'></span>
                  </label>
                  <br><br>
                  <button class='btn' type='submit' name='customreport'>Täysi raportti</button>
                </form>
              </div>";
    echo '</div>';

    echo '<div class="box">';
      echo '<h2 class="green">Omat tilastot</h2>';
      echo '<div class="section">';
      echo '  <canvas id="weektimechart" width="910" height="450"></canvas>';

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
echo '  <canvas id="monthtimechart" width="910" height="450"></canvas>';

$currentMonth = ltrim(date('W', time()), 0);
$monthWorkTime = $_SESSION['logged_in_user']->getMonthWorkTime();

for ($i = 1; $i < count($monthWorkTime); $i++) {
  $monthTime[$i] = round($monthWorkTime[$i]/3600.0, 2);
}

    echo '</div></div>';
  echo '</div>';



  echo '</section>';

  if ($_SESSION['logged_in_user']->level > 0){
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

?>

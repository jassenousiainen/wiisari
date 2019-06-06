<?php

require 'common.php';
tc_connect();

// Note that since this class is used in session variable (which is stored on server) and not cookie the user cant alter or see these values in any way

class User {

  private $user_data;
  public $username;
  public $last;

  public function __construct($username, $admin, $time_admin, $reports) {
      $this->username = $username;
      $this->admin = $admin;
      $this->time_admin = $time_admin;
      $this->reports = $reports;
      $this->user_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE empfullname = '$username'"));

      $this->last_inout   = $this->user_data[1];
      $this->displayname  = $this->user_data[3];
      $this->email        = $this->user_data[4];
      $this->barcode      = $this->user_data[5];
      $this->groups       = $this->user_data[6];
      $this->office       = $this->user_data[7];
      $this->disabled     = $this->user_data[11];
      $this->inout_status = $this->user_data[12];
   }


   public function isSuperior() {
     if ($this->admin == 1 || $this->reports == 1 || $this->time_admin == 1) {
       return true;
     } else {
       return false;
     }
   }

   public function getInoutStatus() {
     return tc_select_value("inout_status", "employees", "empfullname = ?", $this->username);
   }

  // Count time from last login in seconds
  public function getCurrentWorkTime() {
    if ($this->getInoutStatus() == "in") {
      // Lookup previous login, so we can count time between login and current logout
      $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE fullname = '$this->username' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
      $currentWorkTime = time() - (int)$lastIn;
    } else {
      $currentWorkTime = 0;
    }
    return $currentWorkTime;
  }

  // Returns an array with this years worktime per each week
  public function getWeekWorkTime() {
    $weektime = array_fill(1, 53, 0);
    $outStamps = tc_query("SELECT * FROM info WHERE fullname = '$this->username' AND `inout` = 'out' ORDER BY timestamp DESC");

    while ( $tempOut = mysqli_fetch_array($outStamps) ) {   // Käydään läpi työntekijän kaikki kirjaukset
      if ( date('Y', $tempOut[3]) == date('Y', time()) ) { // Lasketaan vain tämän vuoden kirjaukset
        $tempstamp = $tempOut[3];
        $week = ltrim(date('W', $tempOut[3]), 0); // 1-52 (huomaa ltrimin käyttö aloittavien nollien poistamiseksi)

        // Haetaan seuraava kirjaus (eli sisäänkirjaus)
        $tempIn = mysqli_fetch_row(tc_query( "SELECT * FROM info WHERE fullname = '$this->username' AND `inout` = 'in' AND timestamp < '$tempstamp' ORDER BY timestamp DESC"));

        $time = (int)$tempOut[3] - (int)$tempIn[3]; // Lasketaan uloskirjauksen ja sisäänkirjauksen erotus
        if (is_numeric($time)) {
          $weektime[$week] += $time;
        }
      } else {
        break;
      }
    }
    return $weektime;
  }

  // Returns an array with this years worktime per each month
  public function getMonthWorkTime() {
    $monthtime = array_fill(1, 13, 0);
    $outStamps = tc_query("SELECT * FROM info WHERE fullname = '$this->username' AND `inout` = 'out' ORDER BY timestamp DESC");

    while ( $tempOut = mysqli_fetch_array($outStamps) ) {   // Käydään läpi työntekijän kaikki kirjaukset
      if ( date('Y', $tempOut[3]) == date('Y', time()) ) { // Lasketaan vain tämän vuoden kirjaukset
        $tempstamp = $tempOut[3];
        $month = date('n', $tempOut[3]); // 1-12

        $tempIn = mysqli_fetch_row(tc_query( "SELECT * FROM info WHERE fullname = '$this->username' AND `inout` = 'in' AND timestamp < '$tempstamp' ORDER BY timestamp DESC"));

        $time = (int)$tempOut[3] - (int)$tempIn[3];
        if (is_numeric($time)) {
          $monthtime[$month] += $time;
        }
      } else {
        break;
      }
    }
    return $monthtime;
  }

}


 ?>

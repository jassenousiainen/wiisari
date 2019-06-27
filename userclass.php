<?php

require 'common.php';
tc_connect();

// Note that since this class is used in session variable (which is stored on server) and not in a cookie the user cant alter or see these values in any way

class User {

  private $user_data;
  public $userID;
  public $last;

  // Note that the db query is ran only on construction of this instance
  // -> When user edits their own info, an instance of this class should be created again

  public function __construct($userID, $level) { 
      $this->userID = $userID;
      $this->level = intval($level);  // This is set in loginphase, so that logging with admin rights using just barcode wouldn't be possible
      $this->user_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE userID = '$userID'"));
      $this->displayName  = $this->user_data[1];
      $this->groupID      = $this->user_data[2];
      $this->adminPassword = $this->user_data[4];
      $this->inout_status = $this->user_data[5];
   }

  public function officeName() {
    return mysqli_fetch_row(tc_query("SELECT officeName FROM groups NATURAL JOIN offices WHERE groupID = '$this->groupID'"));
  }

  public function groupName() {
    return mysqli_fetch_row(tc_query("SELECT groupName FROM groups WHERE groupID = ?",$this->groupID));
  }

   public function getInoutStatus() {
     return tc_select_value("inoutStatus", "employees", "userID = ?", $this->userID);
   }

  // Count time from last login in seconds
  public function getCurrentWorkTime() {
    if ($this->getInoutStatus() == "in") {
      // Lookup previous login, so we can count time between login and current logout
      $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE userID = '$this->userID' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
      if ($lastIn > time()) {
        $currentWorkTime = 0;
      } else {
        $currentWorkTime = time() - (int)$lastIn;
      }  
    } else {
      $currentWorkTime = 0;
    }
    return $currentWorkTime;
  }

  // Returns an array with this years worktime per each week
  public function getWeekWorkTime() {
    $weektime = array_fill(1, 53, 0);
    $outStamps = tc_query("SELECT * FROM info WHERE userID = '$this->userID' AND `inout` = 'out' ORDER BY timestamp DESC");

    while ( $tempOut = mysqli_fetch_array($outStamps) ) {   // Käydään läpi työntekijän kaikki kirjaukset
      if ( date('Y', $tempOut[3]) == date('Y', time()) ) { // Lasketaan vain tämän vuoden kirjaukset
        $tempstamp = $tempOut[3];
        $week = ltrim(date('W', $tempOut[3]), 0); // 1-52 (huomaa ltrimin käyttö aloittavien nollien poistamiseksi)

        // Haetaan seuraava kirjaus (eli sisäänkirjaus)
        $query = tc_query( "SELECT * FROM info WHERE userID = '$this->userID' AND `inout` = 'in' AND timestamp < '$tempstamp' ORDER BY timestamp DESC");
          if($query != FALSE){
            $tempIn = mysqli_fetch_row($query);
          }

        if(isset($tempOut) && isset($tempIn)){
          $time = (int)$tempOut[3] - (int)$tempIn[3]; // Lasketaan uloskirjauksen ja sisäänkirjauksen erotus
        }
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
    $outStamps = tc_query("SELECT * FROM info WHERE userID = '$this->userID' AND `inout` = 'out' ORDER BY timestamp DESC");
    if($outStamps != FALSE){
      while ( $tempOut = mysqli_fetch_array($outStamps) ) {   // Käydään läpi työntekijän kaikki kirjaukset
        if ( date('Y', $tempOut[3]) == date('Y', time()) ) { // Lasketaan vain tämän vuoden kirjaukset
          $tempstamp = $tempOut[3];
          $month = date('n', $tempOut[3]); // 1-12

          $query = tc_query( "SELECT * FROM info WHERE userID = '$this->userID' AND `inout` = 'in' AND timestamp < '$tempstamp' ORDER BY timestamp DESC");
          if($query != FALSE){
            $tempIn = mysqli_fetch_row($query);
          }
          
          if(isset($tempOut) && isset($tempIn)){
            $time = (int)$tempOut[3] - (int)$tempIn[3];
          }
          if (is_numeric($time)) {
            $monthtime[$month] += $time;
          }
        } else {
          break;
        }
      }
    }
    return $monthtime;
  }

}


 ?>

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
      $query = tc_query( "SELECT * FROM employees WHERE userID = '$userID'");
      if($query != FALSE){
        $this->user_data = mysqli_fetch_row($query);
      }
      if(isset($this->user_data)){
        $this->displayName  = $this->user_data[1];
        $this->groupID      = $this->user_data[2];
        $this->adminPassword = $this->user_data[4];
        $this->inout_status = $this->user_data[5];
      }
   }

  public function officeName() {
    $query = tc_query("SELECT officeName FROM groups NATURAL JOIN offices WHERE groupID = '$this->groupID'");
    if($query != FALSE){
      return mysqli_fetch_row($query);
    }
  }

  public function groupName() {
    $groupID = $this->groupID;
    $query = tc_query("SELECT groupName FROM groups WHERE groupID = ?",$groupID);
    if($query != FALSE){
      return mysqli_fetch_row($query);
    }
  }

   public function getInoutStatus() {
     return tc_select_value("inoutStatus", "employees", "userID = ?", $this->userID);
   }

  // Count time from last login in seconds
  public function getCurrentWorkTime() {
    if ($this->getInoutStatus() == "in") {
      // Lookup previous login, so we can count time between login and current logout
      $query = tc_query("SELECT timestamp FROM info WHERE userID = '$this->userID' AND `inout` = 'in' ORDER BY timestamp DESC");
      if($query != FALSE){
        $lastIn = mysqli_fetch_row($query);
        if($lastIn != FALSE){
          $lastIn = $lastIn[0];
        }
      }
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
    $firstDayOfYear = new DateTime();
    $firstDayOfYear->setDate($firstDayOfYear->format('Y'), 1, 1);
    $firstDayOfYear->setTime(0, 0, 0);
    $firstStampOfYear = $firstDayOfYear->getTimestamp();

    $weektime = array_fill(1, 53, 0);
    $outStamps = tc_query("SELECT timestamp FROM info WHERE userID = '$this->userID' AND `inout` = 'out' AND timestamp > '$firstStampOfYear'  ORDER BY timestamp DESC");
    while ( $tempOutStamp = mysqli_fetch_array($outStamps)[0] ) {   // Käydään läpi työntekijän kaikki kirjaukset
          $week = ltrim(date('W', $tempOutStamp), 0); // 1-52 (huomaa ltrimin käyttö aloittavien nollien poistamiseksi)

          // Haetaan edeltävä kirjaus (eli sisäänkirjaus)
          $tempInStamp = mysqli_fetch_row(tc_query( "SELECT timestamp FROM info WHERE userID = '$this->userID' AND `inout` = 'in' AND timestamp < '$tempOutStamp' ORDER BY timestamp DESC"))[0];

          if(isset($tempOutStamp) && isset($tempInStamp)){
            $time = $tempOutStamp - $tempInStamp; // Lasketaan uloskirjauksen ja sisäänkirjauksen erotus
          }
          if (is_numeric($time)) {
            $weektime[$week] += $time;
          }
    }
    return $weektime;
  }

  // Returns an array with this years worktime per each month
  public function getMonthWorkTime() {
    $firstDayOfYear = new DateTime();
    $firstDayOfYear->setDate($firstDayOfYear->format('Y'), 1, 1);
    $firstDayOfYear->setTime(0, 0, 0);
    $firstStampOfYear = $firstDayOfYear->getTimestamp();

    $monthtime = array_fill(1, 13, 0);
    $outStamps = tc_query("SELECT timestamp FROM info WHERE userID = '$this->userID' AND `inout` = 'out' AND timestamp > '$firstStampOfYear'  ORDER BY timestamp DESC");
    while ( $tempOutStamp = mysqli_fetch_array($outStamps)[0] ) {   // Käydään läpi työntekijän kaikki kirjaukset
          $month = date('n', $tempOutStamp); // 1-12
          $tempInStamp = mysqli_fetch_row(tc_query( "SELECT timestamp FROM info WHERE userID = '$this->userID' AND `inout` = 'in' AND timestamp < '$tempOutStamp' ORDER BY timestamp DESC"))[0];
          if(isset($tempOutStamp) && isset($tempInStamp)){
            $time = $tempOutStamp - $tempInStamp;
            if (is_numeric($time)) {
              $monthtime[$month] += $time;
            }
          }    
    }
    return $monthtime;
  }

}


 ?>

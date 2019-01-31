<?php

require 'common.php';
tc_connect();


class User {

  private $user_data;
  private $username;
  public $last;

  public function __construct($username) {
      $this->username = $username;
      $this->user_data = mysqli_fetch_row(tc_query( "SELECT * FROM employees WHERE empfullname = '$username'"));

      $this->last_inout   = $this->user_data[1];
      $this->displayname  = $this->user_data[3];
      $this->email        = $this->user_data[4];
      $this->barcode      = $this->user_data[5];
      $this->groups       = $this->user_data[6];
      $this->office       = $this->user_data[7];
      $this->admin        = $this->user_data[8];
      $this->reports      = $this->user_data[9];
      $this->time_admin   = $this->user_data[10];
      $this->disabled     = $this->user_data[11];
      $this->inout_status = $this->user_data[12];
   }


   public function isBasicAdmin() {
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
    if ($inout_status == "out") {
      // Lookup previous login, so we can count time between login and current logout
      $lastIn = mysqli_fetch_row(tc_query("SELECT timestamp FROM info WHERE fullname = '$username' AND `inout` = 'in' ORDER BY timestamp DESC"))[0];
      $currentWorkTime = $tz_stamp - (int)$lastIn;
    } else {
      $currentWorkTime = 0;
    }
    return $currentWorkTime;
  }

}


 ?>

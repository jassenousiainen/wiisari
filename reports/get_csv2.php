<?php
require '../common.php';
tc_connect();
session_start();

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']->level < 1) {
  echo "<script type='text/javascript' language='javascript'> window.location.href = '/loginpage.php';</script>";
  exit;
}

if ((isset($_GET['group'])) && (isset($_GET['from'])) && (isset($_GET['to'])) ){

  $groupID = $_GET['group'];
  $from_timestamp = $_GET['from'];
  $to_timestamp = $_GET['to'];
  $group_name = $_GET['groupname'];
  if(isset($_GET['details']) && $_GET['details'] === '1'){
    $details = $_GET['details'];
  }
  $userID = $_SESSION['logged_in_user']->userID;

  
  // Checks that the current user has permissions for the selected groups
  if ($groupID != "all") {
    $group_name = tc_select_value("groupName", "groups", "groupID = ?", $groupID);
    if (!isset($group_name) || empty($groupID)) {
      die("Valittua ryhmää ei löytynyt");
    }
    $super = mysqli_fetch_row(tc_query("SELECT * FROM supervises WHERE groupID ='$groupID' AND userID = '$userID'"));
    if ( $_SESSION['logged_in_user']->level < 3 && empty($super) ) {
      die("Sinulla ei ole oikeutta valittuun ryhmään");
    }
  }


  $data = array();
  $dates = array();
  if(isset($details)){
    $data3 = array("Name,In/Out,Time,Date,Notes,Employee Totals");
  }else{
    $data3 = array("Name,Date,Daily Totals,Employee Totals");
  }
  if($groupID === "all"){
    if($_SESSION['logged_in_user']->level < 3){
      $query = "SELECT employees.userID FROM employees,supervises WHERE supervises.userID = '$userID' AND employees.groupID = supervises.groupID GROUP BY userID";
    }else{
      $query = "SELECT userID FROM employees";
    }
    $howManyUsers = mysqli_query($GLOBALS["___mysqli_ston"], $query);
  }else{
    $query = "SELECT userID FROM employees WHERE groupID = $groupID";
    $howManyUsers = mysqli_query($GLOBALS["___mysqli_ston"], $query);
  }

    while ($row = mysqli_fetch_array($howManyUsers)) {
      $data[$row[0]] = array("userID" => $row[0]);
    }

    foreach ($data as $v){
      $userID = $v['userID'];
      $query = "SELECT * FROM info WHERE timestamp > $from_timestamp AND timestamp < $to_timestamp AND userID = '$userID' ORDER BY punchID ASC";
      $secs = mysqli_query($GLOBALS["___mysqli_ston"], $query);
      $dates[$userID] = array();
      if(isset($details)){

        while ($row = mysqli_fetch_array($secs)) {
          $date = (new DateTime("@".$row['timestamp']))->setTimeZone(new DateTimeZone($timezone))->format('d.m.Y');

          if(!isset($dates[$userID][$date])){
            $dates[$userID][$date] = array();
          }

          if($row['inout'] === "in"){
            $intime = $row['timestamp'];
          }else{

            if(isset($intime) && $row['inout'] === "out"){
              $outtime = $row['timestamp'];
              $seclogin = $outtime - $intime;
              array_push($dates[$userID][$date],$seclogin);
              unset($intime);
            }

          }
          $time = (new DateTime("@".$row['timestamp']))->setTimeZone(new DateTimeZone($timezone))->format('H:i');
          $Dname = getDname($userID); 
          $notes = $row['notes'];
          $str = $Dname .",". $row['inout'] .",". $time .",". $date .",'" .$notes."'";
          array_push($data3, $str);
        }
        foreach($dates[$userID] as $key){
          $x = array_sum($key);
          $x = round($x/60/60,2);
          $k = array_search($key, $dates[$userID]);
          $data[$userID]['Days'][$k] = $x;
        }
        if(isset($data[$userID]['Days'])){
          $y = array_sum($data[$userID]['Days']);
        }else{
          $y = 0;
        }
        $Dname = getDname($userID);
        array_push($data3,"$Dname,,,,,$y");
        array_push($data3,"");

      }else{
        if($secs->num_rows === 0){
          $dates[$userID] = array();
        }else{
          while ($row = mysqli_fetch_array($secs)) {  
            $date = (new DateTime("@".$row['timestamp']))->setTimeZone(new DateTimeZone($timezone))->format('d.m.Y');
            if(!isset($dates[$userID][$date])){
              $dates[$userID][$date] = array();
            }
            if($row['inout'] === "in"){
              $intime = $row['timestamp'];
            }else{
              if(isset($intime) && $row['inout'] === "out"){
                $outtime = $row['timestamp'];
                $seclogin = $outtime - $intime;
                array_push($dates[$userID][$date],$seclogin);
                unset($intime);
              }
            }
          }
        }
        foreach($dates[$userID] as $key){
          $x = array_sum($key);
          $x = round($x/60/60,2);
          $k = array_search($key, $dates[$userID]);
          $data[$userID]['Days'][$k] = $x;
          $Dname = getDname($userID);
          $str = $Dname .",". $k .",". $x .",";
          array_push($data3, $str);
        }
        if(isset($data[$userID]['Days'])){
          $y = array_sum($data[$userID]['Days']);
        }else{
          $y = 0;
        }
        $Dname = getDname($userID);
        array_push($data3,"$Dname,,,$y");
        array_push($data3,"");
      }

    }



    $f = (new DateTime("@$from_timestamp"))->setTimeZone(new DateTimeZone($timezone))->format('d.m.Y');
    $t = (new DateTime("@$to_timestamp"))->setTimeZone(new DateTimeZone($timezone))->format('d.m.Y');
    $filename = $group_name . " " . $f . "-" . $t;
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv"');

    $fp = fopen('php://output', 'wb');
    fputs( $fp, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF) );
    foreach ( $data3 as $line ) {
        $val = str_getcsv($line, ",", "'");
        fputcsv($fp, $val);
    }
    fclose($fp);
    
}

function getDname($userID){
  $query = tc_query( "SELECT displayName FROM employees WHERE userID = '$userID'");
  if($query != FALSE){
    $result = mysqli_fetch_row($query);
  }
  if(!empty($result)){
    return $result[0];
  }
}




?>
<?php
require '../common.php';
tc_connect();

if ((isset($_GET['group'])) && (isset($_GET['from'])) && (isset($_GET['to'])) ){

  $group_name = $_GET['group'];
  $from_timestamp = $_GET['from'];
  $to_timestamp = $_GET['to'];
  if(isset($_GET['details']) && $_GET['details'] === '1'){
    $details = $_GET['details'];
  }

  $data = array();
  if(isset($details)){
    $data3 = array("Name,In/Out,Time,Date,Notes,Employee Totals");
  }else{
    $data3 = array("Name,Date,Daily Totals,Employee Totals");
  }

  if($group_name === "Kaikki ryhmäsi"){
    $query = "SELECT userID FROM info WHERE timestamp > $from_timestamp AND timestamp < $to_timestamp GROUP BY userID";
    $howManyUsers = mysqli_query($GLOBALS["___mysqli_ston"], $query);
  }else{
    $query = "SELECT info.userID FROM info NATURAL JOIN employees NATURAL JOIN groups WHERE timestamp > $from_timestamp AND timestamp < $to_timestamp  AND groups.groupName = '$group_name' GROUP BY userID";
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
          $date = date("d/m/Y" , $row['timestamp']);

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
          $time = date("h:i" , $row['timestamp']);
          $str = $userID .",". $row['inout'] .",". $time .",". $date ."," . $row['notes'];
          array_push($data3, $str);
        }
        foreach($dates[$userID] as $key){
          $x = array_sum($key);
          $x = round($x/60/60,2);
          $k = array_search($key, $dates[$userID]);
          $data[$userID]['Days'][$k] = $x;
        }
        $y = array_sum($data[$userID]['Days']);
        array_push($data3,"$userID,,,,,$y");
        array_push($data3,"");

      }else{
        while ($row = mysqli_fetch_array($secs)) {
          $date = date("d/m/Y" , $row['timestamp']);
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
        foreach($dates[$userID] as $key){
          $x = array_sum($key);
          $x = round($x/60/60,2);
          $k = array_search($key, $dates[$userID]);
          $data[$userID]['Days'][$k] = $x;
          $str = $userID .",". $k .",". $x .",";
          array_push($data3, $str);
        }
        $y = array_sum($data[$userID]['Days']);
        array_push($data3,"$userID,,,$y");

      }

    }
    $f = date("d/m/Y" , $from_timestamp);
    $t = date("d/m/Y" , $to_timestamp);
    $filename = $group_name . " " . $f . "-" . $t;
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv"');

    $fp = fopen('php://output', 'wb');
    foreach ( $data3 as $line ) {
        $val = explode(",", $line);
        fputcsv($fp, $val);
    }
    fclose($fp);
    
    
}





?>
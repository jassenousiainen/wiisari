<?php
require 'common.php';
tc_connect();

$count = 0;
$inout = "in";
$userID = "admin";
$timestamp = 1559347200;

while ($count < 1000) {

    $clockin = array("userID" => $userID, "inout" => $inout, "timestamp" => $timestamp, "notes" => "$count");
    tc_insert_strings("info", $clockin);

    if ($inout == "in") {
        $inout = "out";
    } else {
        $inout = "in";
    }
    $timestamp += 3600;
    $count += 1;
}

?>
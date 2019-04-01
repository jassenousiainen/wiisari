<?php
require 'common.php';
session_start();

if (isset($_SESSION['logged_in_user'])) {
  header("Location: mypage.php");
} else {
  header("Location: timeclock.php");
}

?>

<?php
session_start();

if (isset($_SESSION['valid_user'])) {
    unset($_SESSION['valid_user']);
}
if (isset($_SESSION['valid_reports_user'])) {
    unset($_SESSION['valid_reports_user']);
}
if (isset($_SESSION['time_admin_valid_user'])) {
    unset($_SESSION['time_admin_valid_user']);
}
if (isset($_SESSION['logged_in'])) {
    unset($_SESSION['logged_in']);
}
if (isset($_SESSION['logged_in_user'])) {
  unset($_SESSION['logged_in_user']);
}

session_destroy();

echo "<script type='text/javascript' language='javascript'> window.location.href = 'index.php';</script>";
?>

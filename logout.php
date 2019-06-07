<?php
session_start();

if (isset($_SESSION['logged_in_user'])) {
  unset($_SESSION['logged_in_user']);
}

session_destroy();

echo "<script type='text/javascript' language='javascript'> window.location.href = 'index.php';</script>";
?>

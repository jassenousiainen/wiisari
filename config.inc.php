<?php

/*
****************************************************************************
Be sure to set appropriate permissions on this file as it contains sensitive
username and password information!
****************************************************************************
*/


/* mysql info --- $db_hostname is the hostname for your mysql server, default is localhost.
              --- $db_username is the mysql username you created during the install.
              --- $db_password is the mysql password for the username you created during
                  the install.
              --- $db_name is the mysql database you created during the install. */

$db_hostname = "localhost";
$db_username = "wiisariuser";
$db_password = "clocktime";
$db_name = "tst-wiisari";


// Salt for password encryption
$salt = "s4iHfrxWJBsB7IiGdENR";


/* 
Timezone for displaying information (all times are saved in UTC)
See available timezones: www.php.net/manual/en/timezones.php 
*/
$timezone = "Europe/Helsinki";

?>

<?php
session_start();

require 'common.php';
include 'header.php';


    if (isset($_SESSION['valid_user'])) {
        $set_logout = "1";
    }

    include 'topmain.php';

    echo "<div class='loginAdmin'>
      <form name='auth' method='post' action='ownReports.php'>
        <h2>Kirjaudu omaan tuntinäkymään</h2>
        <br/>
        <div class='field'>
          <label>Käyttäjätunnus: </label>
          <input type='password' id='left_barcode' name='left_barcode' maxlength='250' size='17' value='' autocomplete='off' autofocus>
        </div>
        <br/>";

    echo "</form>\n";
    echo "</div>";


echo "<title>Omat Tunnit</title>\n";

?>

<?php
require 'common.php';
session_start();

include 'header.php';

echo "<title>WIISARI</title>\n";
include 'topmain.php';

if ( isset($_SESSION['logged_in']) ) {
  $logged_in_user = $_SESSION['logged_in'];
  $logged_in_barcode = tc_select_value("barcode", "employees", "empfullname = ?", $logged_in_user);
} else { $logged_in_barcode=""; }

    echo '
    <div class="lomake">
    <form name="timeclock" action="inout.php" autocomplete="off" method="post">
      <br/>
      <div class="barcodeBox">
        <label class="barcodeHeader" for="left-barcode">Käyttäjätunnus:</label>
        <div class="barcodeInput">
    	     <input type="password" id="left_barcode" name="left_barcode" maxlength="250" value="'.$logged_in_barcode.'" autocomplete="off" autofocus>
           <button type="submit" id="barcodeSubmit" class="fas fa-arrow-right"></button>
        </div>
      </div>
      <div id="notesBox">
        <textarea type="text" id="notes" name="notes" autocomplete="off" placeholder="Kirjoita viesti, jonka haluat liittää mukaan tähän kirjaukseen."></textarea>
      </div>
      <input type="button" id="showNotes" value="lisää viesti">
    </form>
	</div>
    ';

    echo '
    <div class="clockBox">
      <span id="theTime"></span>
    </div>';

    echo '<p class="maker">jasse.nousiainen@aalto.fi</p>';



?>

<?php
session_start();

require 'common.php';
include 'header.php';

if (!isset($_GET['printer_friendly'])) {

    if (isset($_SESSION['valid_user'])) {
        $set_logout = "1";
    }

    echo "<title>WIISARI</title>\n";
    include 'topmain.php';

    echo '
    <div class="lomake">
    <form name="timeclock" action="inout.php" autocomplete="off" method="post">
      <br/>
      <div class="usernameBox">
        <label class="usernameHeader" for="username">Käyttäjätunnus:</label>
        <div class="usernameInput">
    	     <input type="password" id="username" name="username" maxlength="250" value="" autocomplete="off" autofocus>
           <button type="submit" id="usernameSubmit" class="fas fa-arrow-right"></button>
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
}



?>

<?php

error_reporting(0);
ini_set('display_errors', 0);

require 'common.php';

echo "<head>
        <title>Sisään/Ulos</title>
        <meta http-equiv='refresh' content='3; URL=index.php'>
        <link rel='stylesheet' type='text/css' media='screen' href='css/default.css' />\n
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet'/>\n
      </head>";

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

if ($show_display_name == "yes") {
    $emp_name_field = "displayname";
} else {
    $emp_name_field = "empfullname";
}

if ($request == 'POST') {
    @$remember_me = $_POST['remember_me'];
    @$reset_cookie = $_POST['reset_cookie'];
    @$fullname = $_POST['left_fullname'];
    @$displayname = $_POST['left_displayname'];
    @$barcode = (yes_no_bool($barcode_clockin) ? strtoupper($_POST['left_barcode']) : "");
    if ((isset($remember_me)) && ($remember_me != '1')) {
        echo "Something is fishy here.\n";
        exit;
    }
    if ((isset($reset_cookie)) && ($reset_cookie != '1')) {
        echo "Something is fishy here.\n";
        exit;
    }

    // begin post validation //
    $errors = array();

    if (has_value($barcode)) {
        $tmp_name = tc_select_value($emp_name_field, "employees", "barcode = ?", $barcode);
        if (!has_value($tmp_name)) {
            $errors[] = "Invalid barcode '$barcode'";
        } elseif (isset($emp_name) and $emp_name != $tmp_name) {
            $errors[] = "Username / Barcode mismatch";
        } else {
            $emp_name = $tmp_name;
        }
    }

    $tmp_name = '';
    if (yes_no_bool($show_display_name)) {
        if (has_value($displayname)) {
            $tmp_name = tc_select_value($emp_name_field, "employees", "displayname = ?", $displayname);
            if (!has_value($tmp_name)) {
                $errors[] = "Invalid username '$displayname'";
            }
        }
    } else {
        if (has_value($fullname)) {
            $tmp_name = tc_select_value($emp_name_field, "employees", "empfullname = ?", $fullname);
            if (!has_value($tmp_name)) {
                $errors[] = "Invalid username '$fullname'";
            }
        }
    }

    if (has_value($tmp_name)) {
        if (isset($emp_name) and $emp_name != $tmp_name) {
            $errors[] = "Username / Barcode mismatch";
        } else {
            $emp_name = $tmp_name;
        }
    }

    // end post validation //

    if (empty($errors)) {
        if (isset($remember_me)) {
            setcookie("remember_me", $emp_name, time() + (60 * 60 * 24 * 365 * 2));
        } elseif (isset($reset_cookie)) {
            setcookie("remember_me", "", time() - 3600);
        }
    }

    ob_end_flush();
}

if ($request == 'POST') {

    // signin/signout data passed over from timeclock.php //

    $inout = isset($_POST['left_inout']) ? $_POST['left_inout'] : '';
    $notes = isset($_POST['left_notes']) ? preg_replace("[^a-zA-Z0-9 \,\.\?-]", "", strtolower($_POST['left_notes'])) : '';

    // begin post validation //

    # Trying to toggle, look up the "punchnext" toggle state:
    if (!has_value($inout) and has_value($emp_name)) {
        $result = tc_query(<<<QUERY
   SELECT p.punchnext
     FROM ${db_prefix}employees AS e
LEFT JOIN ${db_prefix}info      AS i ON (e.empfullname = i.fullname AND e.tstamp = i.timestamp)
LEFT JOIN ${db_prefix}punchlist AS p ON (i.inout = p.punchitems)
    WHERE e.$emp_name_field = ?
QUERY
        , $emp_name);
        while ($row = mysqli_fetch_array($result)) {
            $inout = $row[0];
        }
    }
    elseif (has_value($inout)) {
        $inout = tc_select_value("punchitems", "punchlist", "punchitems = ?", $inout);
        if (!has_value($inout)) {
            echo "In/Out Status is not in the database.\n";
            exit;
        }
    }

    // end post validation //

    if (!has_value($emp_name) && !has_value($inout)) {
        $errors[] = "<h1>Hups! En löytänyt sinua. Kokeile uudestaan.</h1><br/><h2>Sivu palautuu automaattisesti etusivulle</h2>";
    }
    elseif (!has_value($emp_name)) {
        $errors[] = "You have not chosen a username. Please try again.";
    }
    elseif (!has_value($inout)) {
        //$errors[] = "You have not chosen a status. Please try again.";
        $inout = 'in';
    }

    if (!empty($errors)) {
        echo "    <td align=left class=right_main scope=col>\n";
        echo "      <table width=100% height=100% border=0 cellpadding=10 cellspacing=1>\n";
        echo "        <tr class=right_main_text>\n";
        echo "          <td valign=top>\n";
        echo "<br />\n";
        echo implode("<br>\n", $errors);
        include 'footer.php';
        exit;
    }

    // configure timestamp to insert/update //
    $tz_stamp = time();

    if (has_value($barcode) or $use_passwd == "no") {

        if (!has_value($fullname)) {
            $fullname = tc_select_value("empfullname", "employees", "$emp_name_field = ?", $emp_name);
        }

        $clockin = array("fullname" => $fullname, "inout" => $inout, "timestamp" => $tz_stamp, "notes" => "$notes", "punchoffice" => "".@$_COOKIE['office_name']);

        tc_insert_strings("info", $clockin);
        tc_update_strings("employees", array("tstamp" => $tz_stamp), "empfullname = ?", $fullname);

    }
}



$viivakoodi = strtoupper($_POST['left_barcode']);


$kellovastaus = tc_query(<<<QUERY
SELECT tstamp
FROM employees
WHERE barcode = '$viivakoodi'
QUERY
);
while ($row = mysqli_fetch_array($kellovastaus)) {
    $kello = $row[0];
}


$nimivastaus = tc_query(<<<QUERY
SELECT displayname
FROM employees
WHERE barcode = '$viivakoodi'
QUERY
);
while ($row = mysqli_fetch_array($nimivastaus)) {
    $nimi = $row[0];
}


$fullnamevastaus = tc_query(<<<QUERY
SELECT empfullname
FROM employees
WHERE barcode = '$viivakoodi'
QUERY
);
while ($row = mysqli_fetch_array($fullnamevastaus)) {
    $fullname = $row[0];
}


$sisaanulosvastaus = tc_query(<<<QUERY
SELECT `inout`
FROM info
WHERE fullname = '$fullname'
ORDER BY newid DESC LIMIT 1
QUERY
);
while ($row = mysqli_fetch_array($sisaanulosvastaus)) {
    $sisaanulos = $row[0];
}

if ($sisaanulos == "out") {
  $sisaanulos = "<p class='kirjausUlos'>Ulos</p>";
}
else if ($sisaanulos == "in") {
  $sisaanulos = "<p class='kirjausSisaan'>Sisään</p>";
}


$dt = new DateTime("@$kello");
$dt->setTimeZone(new DateTimeZone('Europe/Helsinki'));

echo "<div class='kirjausLaatikko'>";
echo "<h2 class='kirjausNimi'>$nimi</h2>";
echo '<br>';
echo '<p class="kirjausAika">Kello: <b>';
echo $dt->format("H:i");
echo '</b></p>';
echo '<p class="kirjausPaiva">Päivä: <b>';
echo $dt->format("d.m.Y");
echo '</b></p>';
echo '<br>';
echo $sisaanulos;
echo '<p>Sivu siirtyy automaattisesti etusivulle</p>';
echo "</div>";

?>

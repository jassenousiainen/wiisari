<?php

// Remember to declare variable empfullname before including this code snippet

$adminusername = $_SESSION['logged_in_user']->username;

$accesstogroup = false;
$checkgroup = mysqli_fetch_row(tc_query("SELECT * FROM employees 
                                    WHERE empfullname = '$empfullname' AND groups IN (
                                      SELECT groupname
                                      FROM groups NATURAL JOIN supervises
                                      WHERE fullname = '$adminusername'
                                    )
                                    ORDER BY displayname ASC;"));
if (!empty($checkgroup)) {$accesstogroup = true;}
if ($_SESSION['logged_in_user']->admin == 1) {$accesstogroup = true;} // admin has permissions to every group

// Checks that current supervisor has permissions for this employee's group
if (!$accesstogroup) {
    echo '<h2>Virhe! Sinulla ei ole pääsyä tämän henkilön tietoihin</h2>';
    exit;
}

?>
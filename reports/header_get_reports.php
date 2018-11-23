<?php
include "header.reports.inc.php";

echo "<link rel='stylesheet' type='text/css' media='screen' href='../css/default.css' />\n";
echo "<link rel='stylesheet' type='text/css' media='screen' href='../css/local.css' />\n";
echo "<link rel='stylesheet' type='text/css' media='print' href='../css/print.css' />\n";
echo "<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet'/>\n";
echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">';
echo '<link rel="shortcut icon" href="images/icons/clock_title.png" type="image/x-icon"/>';
echo "<script language=\"javascript\" src=\"../scripts/CalendarPopup.js\"></script>\n";
echo "<script language=\"javascript\">document.write(getCalendarStyles());</script>\n";
echo "<script language=\"javascript\">var cal = new CalendarPopup('mydiv');</script>\n";
echo "<script language=\"javascript\" src=\"../scripts/pnguin.js\"></script>\n";
include '../scripts/dropdown_get_reports.php';
echo "</head>\n";

setTimeZone();

echo "<body onload='office_names();'>\n";
?>

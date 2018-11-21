<?php
include "header.reports.inc.php";

echo "<link rel='stylesheet' type='text/css' media='screen' href='../css/default.css' />\n";
echo "<link rel='stylesheet' type='text/css' media='screen' href='../css/local.css' />\n";
echo "<link rel='stylesheet' type='text/css' media='print' href='../css/print.css' />\n";
echo "<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet'/>\n";
echo '<link rel="shortcut icon" href="images/icons/clock_title.png" type="image/x-icon"/>';
echo "<script language=\"javascript\" src=\"../scripts/CalendarPopup.js\"></script>\n";
echo "<script language=\"javascript\">document.write(getCalendarStyles());</script>\n";
echo "<script language=\"javascript\">var cal = new CalendarPopup('mydiv');</script>\n";
echo "<script language=\"javascript\" src=\"../scripts/pnguin.js\"></script>\n";
include '../scripts/dropdown_post_reports.php';
echo "</head>\n";

setTimeZone();

echo "<body onload='office_names();'>\n";
?>

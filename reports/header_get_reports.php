<?php
include "header.reports.inc.php";

echo "<link rel='stylesheet' type='text/css' media='screen' href='../css/default.css' />\n";
echo "<link rel='stylesheet' type='text/css' media='print' href='../css/print.css' />\n";
echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">';
echo '<link rel="shortcut icon" href="../images/icons/wiisari_title.png" type="image/x-icon"/>';
echo '<script type="text/javascript" src="/scripts/jquery-3.1.1.min.js"></script>';
echo '<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>';
  /* wiisari tablesorter theme */
  echo '<link rel="stylesheet" href="/css/wiisari.tablesorter.css">';
  /* tablesorter plugin */
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.js"></script>';
  /* tablesorter widget file */
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.widgets.js"></script>';
  /* pager plugin */
  echo '<link rel="stylesheet" href="/scripts/tablesorter/jquery.tablesorter.pager.css">';
  echo '<script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.pager.js"></script>';

echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script src="../scripts/datepicker-fi.js"></script>

      <script>
      $( function() {

    var dateFormat = "mm/dd/yy";
      $( "#from" ).datepicker({
          changeMonth: true,
          numberOfMonths: 2,
          defaultDate: "-1m",
          minDate: "-2y",
          maxDate: -1,
          showAnim: "slide"
        });

      $( "#to" ).datepicker({
        changeMonth: true,
        numberOfMonths: 2,
        defaultDate: 0,
        minDate: "-2y",
        maxDate: 0,
        showAnim: "slide"
      });

    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }

      return date;
    }
  } );
  </script>';

echo "</head>\n";

setTimeZone();

?>

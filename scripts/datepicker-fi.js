/* Finnish initialisation for the jQuery UI date picker plugin. */
/* Written by Harri Kilpiö (harrikilpio@gmail.com). */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional.fi = {
	closeText: "Sulje",
	prevText: "&#xAB;Edellinen",
	nextText: "Seuraava&#xBB;",
	currentText: "Tänään",
	monthNames: [ "Tammikuu","Helmikuu","Maaliskuu","Huhtikuu","Toukokuu","Kesäkuu",
	"Heinäkuu","Elokuu","Syyskuu","Lokakuu","Marraskuu","Joulukuu" ],
	monthNamesShort: [ "Tammi","Helmi","Maalis","Huhti","Touko","Kesä",
	"Heinä","Elo","Syys","Loka","Marras","Joulu" ],
	dayNamesShort: [ "Su","Ma","Ti","Ke","To","Pe","La" ],
	dayNames: [ "Sunnuntai","Maanantai","Tiistai","Keskiviikko","Torstai","Perjantai","Lauantai" ],
	dayNamesMin: [ "Su","Ma","Ti","Ke","To","Pe","La" ],
	weekHeader: "Vk",
	dateFormat: "d.m.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.fi );

return datepicker.regional.fi;

} ) );


$( function() {
var dateFormat = "mm/dd/yy";
$( "#from" ).datepicker({
    changeMonth: true,
    numberOfMonths: 2,
    defaultDate: "-1m",
    minDate: "-2y",
    maxDate: 0,
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

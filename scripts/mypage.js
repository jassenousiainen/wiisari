var secs;
var seconds;
var days;
var hrs;
var mnts;
var daysFormat;
var hoursFormat;
var minutesFormat;
var secondsFormat;

function workTime() {
  secs += 1;
  seconds = secs;
  days = Math.floor(seconds / (3600*24));
  seconds  -= days*3600*24;
  hours   = Math.floor(seconds / 3600);
  seconds  -= hours*3600;
  minutes = Math.floor(seconds / 60);
  seconds  -= minutes*60;

  if (days > 0) {
    daysFormat = days+" päivää, ";
    minutes = 0;
  } else {
     daysFormat = "";
  }

  if (hours > 0) {
    hoursFormat = hours+" tuntia";
    seconds = 0;
  } else {
    hoursFormat = "";
  }

  if (minutes > 0 && hours == 0) {
    minutesFormat = minutes+" minuuttia";
  } else if (minutes > 0 && hours > 0) {
    minutesFormat = ", "+minutes+" minuuttia";
  } else {
    minutesFormat = ""
  }

  if (seconds > 0 && minutes == 0) {
    secondsFormat = seconds+" sekuntia";
  } else if (seconds > 0 && minutes > 0) {
    secondsFormat = ", "+seconds+" sekuntia";
  } else {
    secondsFormat = "";
  }

  document.getElementById('secs').innerHTML = ""+daysFormat+hoursFormat+minutesFormat+secondsFormat;
}

function startCounter() {
   clockID = setInterval(workTime, 1000);
}


window.onload=function() {
  if ($("#out").length > 0) {
    secs = parseInt($('#secs').text())
    startCounter();
  }

}


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

var clockID;
var tDate = new Date(new Date().getTime());
var in_hours = tDate.getHours()
var in_minutes=tDate.getMinutes();
var in_seconds= tDate.getSeconds();

function UpdateClock() {
    tDate = new Date(new Date().getTime());
    in_hours = tDate.getHours()
    in_minutes=tDate.getMinutes();
    in_seconds= tDate.getSeconds();

    if(in_minutes < 10)
        in_minutes = '0'+in_minutes;
    if(in_seconds<10)
        in_seconds = '0'+in_seconds;
    if(in_hours<10)
        in_hours = '0'+in_hours;

   document.getElementById('theTime').innerHTML = ""
                   + in_hours + ":"
                   + in_minutes + ":"
                   + in_seconds;

}
function StartClock() {
   clockID = setInterval(UpdateClock, 1000);
}

function KillClock() {
  clearTimeout(clockID);
}

window.onload=function() {
  StartClock();
  $("#showNotes").click(function() {
      $(this).hide();
      $("#notesBox").slideDown(1000, "easeOutElastic");
      $("#notes").focus();
  });
  $('#showNotes').click(function(event){
      event.stopPropagation();
  });
  $('#notesBox').click(function(event){
      event.stopPropagation();
  });
  $('#left_barcode').click(function(event){
      event.stopPropagation();
  });
  $('html').click(function() {
    $('#notesBox').slideUp();
    $("#showNotes").show();
  });
}

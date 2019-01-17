window.onload=function() {
$("#showNotes").click(function() {
    $(this).hide();
    $("#notesBox").slideDown();
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

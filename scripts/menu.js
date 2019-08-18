$(function () {

    $("#profileIcon").click(function() {
        $("#profileBox").toggleClass("open");
    });

    $('#profileIcon').click(function(event){
        event.stopPropagation();
    });

    $('html').click(function() {
        $("#profileBox").removeClass("open");
    });

    $(window).scroll(function(){
        $("#profileBox").removeClass("open");
    });
    
});
$(function () {

    $(".expand").click(function () {
        $("nav.sidemenu").toggleClass("open");
    });

    $('.expand').click(function(event){
        event.stopPropagation();
    });

    $("#profileIcon").click(function() {
        $("#profileBox").toggleClass("open");
    });

    $('#profileIcon').click(function(event){
        event.stopPropagation();
    });

    $('html').click(function() {
        $("#profileBox").removeClass("open");
        $("nav.sidemenu").removeClass("open");
    });

    $(window).scroll(function(){
        $("#profileBox").removeClass("open");
    });
    
});
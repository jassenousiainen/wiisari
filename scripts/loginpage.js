
window.onload=function() {
  $("#employee").click(function(){
    $("#chooseLogin").hide("slide", { direction: "left" }, 1000);
    $("#employeeSlideLogin").show("slide", { direction: "right" }, 1000).animate({ opacity: 1 });
    $("#employeeSlideBack").fadeIn(1000);
  });
  $("#employeeSlideBack").click(function(){
    $("#chooseLogin").show("slide", { direction: "left" }, 1000);
    $("#employeeSlideLogin").hide("slide", { direction: "right" }, 1000);
    $("#employeeSlideBack").fadeOut(500);
  });

  $("#admin").click(function(){
    $("#chooseLogin").hide("slide", { direction: "right" }, 1000);
    $("#adminSlideLogin").show("slide", { direction: "left" }, 1000).animate({ opacity: 1 });
    $("#adminSlideBack").fadeIn(1000);
  });
  $("#adminSlideBack").click(function(){
    $("#chooseLogin").show("slide", { direction: "right" }, 1000);
    $("#adminSlideLogin").hide("slide", { direction: "left" }, 1000);
    $("#adminSlideBack").fadeOut(500);
  });
}

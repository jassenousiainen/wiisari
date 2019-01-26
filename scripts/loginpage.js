
window.onload=function() {
  $("#employee").click(function(){
    $("#chooseLogin").hide("slide", { direction: "left" }, 800);
    $("#employeeSlideLogin").show("slide", { direction: "right" }, 800).animate({ opacity: 1 });
    $("#employeeSlideBack").fadeIn(800);
  });
  $("#employeeSlideBack").click(function(){
    $("#chooseLogin").show("slide", { direction: "left" }, 800);
    $("#employeeSlideLogin").hide("slide", { direction: "right" }, 800);
    $("#employeeSlideBack").fadeOut(500);
  });

  $("#admin").click(function(){
    $("#chooseLogin").hide("slide", { direction: "right" }, 800);
    $("#adminSlideLogin").show("slide", { direction: "left" }, 800).animate({ opacity: 1 });
    $("#adminSlideBack").fadeIn(800);
  });
  $("#adminSlideBack").click(function(){
    $("#chooseLogin").show("slide", { direction: "right" }, 800);
    $("#adminSlideLogin").hide("slide", { direction: "left" }, 800);
    $("#adminSlideBack").fadeOut(500);
  });
}

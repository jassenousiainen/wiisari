window.onload = function(){
document.getElementById('switch').onclick = function() {
  if (document.getElementById('theme').getAttribute("href") == "css/darkmode.css") {
    document.getElementById('theme').href = "css/default.css";
  } else {
    document.getElementById('theme').href = "css/darkmode.css";
  }
};
}

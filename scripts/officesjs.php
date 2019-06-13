<script language="JavaScript">

var x = 1
function addGroupInput(){
  var checked = document.getElementById("groups").checked;
  if(checked === true){
    var tr = document.createElement("TR");
    tr.setAttribute("id", "myGroup");
    var td1 = document.createElement("TD");
    var td2 = document.createElement("TD");
    var td3 = document.createElement("TD");

    var nametxt = document.createTextNode("Ryhmän nimi:");
    td1.appendChild(nametxt);

    var input = document.createElement("input");
    input.setAttribute("type", "text");
    var nametxtid = "input_group_name["+x+"]";
    input.setAttribute("name", nametxtid);
    td2.appendChild(input);


    td3.setAttribute("style", "color: grey; font-size: 13px;");
    var addGroupBtn = document.createElement("button");
    addGroupBtn.setAttribute("class", "btn");
    addGroupBtn.setAttribute("type", "button");
    addGroupBtn.setAttribute("onclick", "addGroup()");
    var btntext = document.createTextNode("lisää");
    addGroupBtn.appendChild(btntext);


    td3.appendChild(addGroupBtn);

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);

    document.getElementById("myGroups").appendChild(tr);
    x++;
  }else{
    while (x > 1){
    var element = document.getElementById("myGroup");
    element.parentNode.removeChild(element);
    x--;}
  }
}
function addGroup(){
  var tr = document.createElement("TR");
    tr.setAttribute("id", "myGroup");
    var td1 = document.createElement("TD");
    var td2 = document.createElement("TD");
    var td3 = document.createElement("TD");

    var nametxt = document.createTextNode("Ryhmän nimi:");
    td1.appendChild(nametxt);

    var input = document.createElement("input");
    input.setAttribute("type", "text");
    var nametxtid = "input_group_name["+x+"]";
    input.setAttribute("name", nametxtid);
    td2.appendChild(input);


    td3.setAttribute("style", "color: grey; font-size: 13px;");
    var addGroupBtn = document.createElement("button");
    addGroupBtn.setAttribute("class", "btn");
    addGroupBtn.setAttribute("type", "button");
    addGroupBtn.setAttribute("onclick", "addGroup()");
    var btntext = document.createTextNode("lisää");
    addGroupBtn.appendChild(btntext);


    td3.appendChild(addGroupBtn);

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);

    document.getElementById("myGroups").appendChild(tr);
    x++;
}
</script>

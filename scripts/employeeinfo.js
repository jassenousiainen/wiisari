window.onload=function() {
    if ($("#admin").is(":checked") || $("#reports").is(":checked") || $("#time").is(":checked")) {
        $("#password").show();
    } else {
        $("#password").hide();
    }

    if ($("#reports").is(":checked") || $("#time").is(":checked")) {
        $(".chooseGroups").show();
    } else {
        $(".chooseGroups").hide();
    }
}
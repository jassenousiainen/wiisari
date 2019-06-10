window.onload=function() {
    if ($("#admin").is(":checked") || $("#reports").is(":checked") || $("#editor").is(":checked")) {
        $("#password").show();
    } else {
        $("#password").hide();
    }

    if ($("#reports").is(":checked") || $("#editor").is(":checked")) {
        $(".chooseGroups").show();
    } else {
        $(".chooseGroups").hide();
    }
}

$(function () {
    $(".check").click(function () {
        if ($("#admin").is(":checked") || $("#reports").is(":checked") || $("#editor").is(":checked")) {
            $("#password").show();
        } else {
            $("#password").hide();
        }
    
        if ($("#reports").is(":checked") || $("#editor").is(":checked")) {
            $(".chooseGroups").show();
        } else {
            $(".chooseGroups").hide();
        }
    });
});
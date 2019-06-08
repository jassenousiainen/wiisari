$(function () {
    $(".check").click(function () {
        if ($("#admin").is(":checked") || $("#reports").is(":checked") || $("#editor").is(":checked")) {
            $("#password").show();
            $("#password td input").attr("required", "true");
        } else {
            $("#password").hide();
            $("#password td input").val('').removeAttr("required");
        }

        if ($("#reports").is(":checked") || $("#editor").is(":checked")) {
            $(".chooseGroups").show();
        } else {
            $(".chooseGroups").hide();
        }
    });
});
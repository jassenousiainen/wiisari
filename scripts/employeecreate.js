window.onload=function() {
    office_names();
}

$(function () {
    $(".check").click(function () {
        if ($("#admin").is(":checked") || $("#reports").is(":checked") || $("#time").is(":checked")) {
            $("#password").show();
        } else {
            $("#password").hide();
            $("#password td input").val('');
        }
    });
});
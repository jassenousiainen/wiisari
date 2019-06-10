<script type="text/javascript">

function gen_barcode(target, style) {
    //style = "code39";
    length = 10;
    var code = "";

if (style === "code39") {
    code = nonce("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789", length);
}
    document.getElementById(target.name).value = code;
}

function nonce(alphabet, length) {
    var n = "";
    for (var i = 0; i < length; i++) {
        n += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
    }
    return n;
}

</script>
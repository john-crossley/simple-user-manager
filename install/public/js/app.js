function createCookie(e, t, n) {
    if (n) {
        var r = new Date;
        r.setTime(r.getTime() + n * 24 * 60 * 60 * 1e3);
        var i = "; expires=" + r.toGMTString()
    } else var i = "";
    document.cookie = escape(e) + "=" + escape(t) + i + "; path=/"
}
function hasCookie(e) {
    var t = escape(e) + "=";
    var n = document.cookie.split(";");
    for (var r = 0; r < n.length; r++) {
        var i = n[r];
        while (i.charAt(0) == " ")i = i.substring(1, i.length);
        if (i.indexOf(t) == 0)return unescape(i.substring(t.length, i.length))
    }
    return null
}
function eraseCookie(e) {
    createCookie(e, "", -1)
}
$(".no-js").hide();

$(document).ready(function () {

    if (hasCookie('connection_success')) {
        $("#change-settings").show();
    }

    $("input[name='license']").on("change", function () {
        var e = $(this), t = $("#license-agreement-button");
        if (e.is(":checked")) {
            t.attr("disabled", false);
            createCookie("license_accepted", true);
        } else {
            t.attr("disabled", true);
            eraseCookie("license_accepted");
        }
    });

    $("#test-connection").on('click', function (e) {

        var $this = $(this);
        $this.text("Please wait...").attr('disabled', true);

        var siteUrl = $("#site-url").val(),
            emailAddress = $("#email-address").val(),
            dbHost = $("#db-host").val(),
            dbUsername = $("#db-username").val(),
            dbPassword = $("#db-password").val(),
            dbName = $("#db-name").val();

        if (siteUrl === "" || emailAddress === "" || dbHost === "" ||
            dbUsername === "" || dbPassword === "" || dbName === "") {
            alert("You must fill out all of the above field before continuing");
            $this.text("Test Connection").attr('disabled', false);
            return false
        }

        createCookie('site_url', siteUrl);
        createCookie('email_address', emailAddress);
        createCookie('db_host', dbHost);
        createCookie('db_username', dbUsername);
        createCookie('db_password', dbPassword);
        createCookie('db_name', dbName);

        $.post("../install/includes/db_connection_test.php", function(data) {
            if (data.error === true) {
                alert(data.message);
                $("#test-connection").text("Test Connection").attr('disabled', false);
            } else {
                createCookie("connection_success", true);
                $("#test-connection").hide();
                $("#install-button").attr("disabled", false);
                $("#change-settings").show();
            }
        }, 'json');

        e.preventDefault();
    });

    $("#change-settings a").on('click', function (e) {
        $(this).hide();
        eraseCookie('connection_success');
        alert("Settings have been deleted... Please enter them again.");
        $("#test-connection").attr("disabled", false);
        $("#install-button").attr("disabled", true);
        e.preventDefault();
    });

});
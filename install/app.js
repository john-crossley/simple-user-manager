function createCookie(e, t, n) {
    if (n) {
        var r = new Date;
        r.setTime(r.getTime() + n * 24 * 60 * 60 * 1e3);
        var i = "; expires=" + r.toGMTString()
    } else var i = "";
    document.cookie = escape(e) + "=" + escape(t) + i + "; path=/"
}
function readCookie(e) {
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
    $("#db-connection").on("submit", function (e) {
        var t = $(this), n = $("#db-host").val(), r = $("#db-username").val(), i = $("#db-password").val(), s = $("#db-name").val(), o = t.find("button");
        o.attr({disabled: true}).text("Please wait...");
        if (n === "" || r === "" || i === "" || s === "") {
            o.attr({disabled: false}).text("Test Connection");
            alert("You must fill out the following fields: \n" + (n === "" ? "Hostname\n" : "") + (r === "" ? "Username\n" : "") + (i === "" ? "Password\n" : "") + (s === "" ? "Database name\n" : ""));
            return false
        }
        $.post("index.php", {host: n, user: r, pass: i, dbname: s, task: "test_connection"}, function (e) {
            alert(e.message);
            if (e.error === false) {
                t.fadeOut();
                $("#start-installation").fadeIn()
            } else {
                o.attr({disabled: false}).text("Test Connection")
            }
        }, "json");
        e.preventDefault()
    });
    $("#db-connection input, #db-connection button").attr("disabled", true);
    $("input[name='license']").on("change", function () {
        var e = $(this), t = $("#license-agreement");
        createCookie("agree_license", true);
        if (e.is(":checked")) {
            t.removeClass("disabled")
        } else {
            t.addClass("disabled");
            eraseCookie("agree_license")
        }
    });
    $("#save-info").on("click", function (e) {
        var t = $(this), n = $("#about-you"), r = $("#site-url").val(), i = $("#fullname").val(), s = $("#email-address").val();
        if (r === "" || i === "" || s === "") {
            alert("You must fill out the following fields: \n" + (r === "" ? "URL\n" : "") + (i === "" ? "Fullname\n" : "") + (s === "" ? "Email\n" : ""));
            return false
        }
        $.post("index.php", {url: r, fullname: i, email: s, task: "save_user"}, function (e) {
            if (e.error === false) {
                alert("Your information has been saved. ");
                n.fadeOut();
                $("#license-info button").removeClass("disabled");
                $("#license-info input").attr("disabled", false)
            } else {
                alert(e.message);
                return false
            }
        }, "json");
        $("#license-info").on("submit", function (e) {
            var t = $(this), n = $("#license-key").val(), r = t.find("button");
            if (n === "") {
                alert("Please enter your license key!");
                r.attr({disabled: false}).text("Submit Key");
                return false
            }
            r.attr({disabled: true}).text("Please wait...");
            $.post("index.php", {code: n, task: "license"}, function (e) {
                alert("Server said: " + e.message);
                if (e.error === false) {
                    t.fadeOut();
                    $("#db-connection input, button").attr("disabled", false)
                } else {
                    r.attr({disabled: false}).text("Submit Key")
                }
            }, "json");
            e.preventDefault()
        });
        e.preventDefault()
    })
})

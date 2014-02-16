jQuery.fn.doesExist = function () {
    return jQuery(this).length > 0;
};

$(function () {

    $("button#how_to_protect_btn").on("click", function (e) {
        var $this = $(this),
            feedback = $("#how_to_protect"),
            role_id = $("select[name='role_id']").val(),
            path = $("input[name='user_group_access']").val();

        feedback.empty();

        if (role_id == '-- Select Group --') {
            $("<div>", {
                text: "Please select a role from the dropdown above.",
                class: "alert alert-danger"
            }).appendTo(feedback);
            return false;
        }

        $.post("../process.php", {
            task: "how_to_protect_user_group",
            role_id: role_id,
            path: path
        }, function (data) {
            // feedback.text(data.data);

            if (data.error === true) {
                feedback.text(data.message);
                return false;
            }

            $("<code>", {
                html: "&lt;?php<br>require_once '" + data.message + ";'<br>restrict_access('" + data.role_name + "');<br>?&gt;"
            }).appendTo(feedback);

        }, 'json');

        e.preventDefault();
    });

    $("a[href='#remove_page']").on('click', function (e) {

        var $this = $(this);

        if (confirm('Are you sure? Removing access means this user can \
      no longer access this page!')) {

            $.post('../process.php', {
                task: 'removeAccessToPage',
                id: $this.data('id')
            }, function (data) {

                if (data.error === false)
                    $this.parent().parent().fadeOut();
                else alert("Unable to remove this link! Please try again.");

            }, 'json');
        }

        e.preventDefault();
    });

    $("button#access_areas_btn").on("click", function (e) {

        var $this = $(this),
            directory_list = $("#directory_list form");

        directory_list.empty();

        // Right grab the URL we need to scan
        var jqxhr = $.post("../_partials/protect_files.php", {
            task: "directory_scan",
            user_id: $("input[name='user_id']").val(),
            csrf: $("input[name='csrf']").val(),
            path: $("input[name='access_area_path']").val()
        }, function (data) {
            directory_list.html(data);
        });

        e.preventDefault();
    });

    $("button#send_personal_message").on("click", function (e) {

        var $this = $(this),
            user_id = $("input[name='user_id']"),
            csrf = $("input[name='csrf']"),
            title = $("input[name='title']"),
            message = $("textarea[name='message']"),
            modal = $("#send-personal-message"),
            form = $(".modal-body .form-group");

        form.removeClass().addClass('form-group');
        form.find("small").remove();

        if (title.val() == "") {
            title.parent().addClass('has-error');
            $("<small>", {
                text: "Please enter a subject for the message",
                class: "help-block"
            }).appendTo(title.parent());
        }

        if (message.val() == "") {
            message.parent().addClass('has-error');
            $("<small>", {
                text: "Please enter a message to send!",
                class: "help-block"
            }).appendTo(message.parent());
        }

        $.post('./process.php', {
            task: 'sendPersonalMessage',
            user_id: user_id.val(),
            title: title.val(),
            message: message.val(),
            csrf: csrf.val(),
        }, function (data) {
            if (data.error === false) {
                form.removeClass().addClass('form-group');
                $this.attr("disabled", true).text("Message Sent!");
                ;
            } else {
                if (data.fatal_error === true) {
                    // We have an error.
                    form.empty();
                    $("<div>", {
                        text: data.message,
                        class: 'alert alert-danger'
                    }).appendTo($(".modal-body"));
                } else {
                    // Assuming we have a generic form error
                    if (data.title.length > 0) {
                        title.parent().addClass('has-error');
                        $("<small>", {
                            text: data.title,
                            class: "help-block"
                        }).appendTo(title.parent());
                    }

                    if (data.message.length > 0) {
                        message.parent().addClass('has-error');
                        $("<small>", {
                            text: data.message,
                            class: "help-block"
                        }).appendTo(message.parent());
                    }
                }
            }
        }, 'json');

        e.preventDefault();
    });

    $("a[href='#protection_modal']").on("click", function (e) {

        // $("#protection_modal").modal();
        var modal = $("#protection_modal"),
            $this = $(this);

        $.post('../process.php', {task: 'calculateRelPath', id: $this.data('id')}, function (data) {
            if (data.error === false) {
                modal.find('div.modal-body code span.insert').empty().text(data.snippet);
            }
        }, 'json');

        e.preventDefault();
    });

    // View message modal
    $("a[href='#view-message']").on("click", function (e) {

        var $this = $(this),
            message_id = $this.data('message'),
            modal = $("#view-message"),
            task = $this.data("task");

        $.post('./process.php', {task: task, message_id: message_id}, function (data) {

            modal.find("h4.modal-title").text(data.title);
            modal.find("div.modal-body").html(data.message);
            modal.find("div.modal-footer .help-block")
                .text("Sent by: " + data.username + " on the " + data.time_sent);

            // Update the unread to read
            if (data.read == 0) {
                $this.parent().parent().find("td span.label").text("Read").removeClass().addClass("label label-success");
            }

        }, 'json');

        modal.modal();
        e.preventDefault();
    });

    $("#request-forgot-password-btn").on("click", function (e) {

        var email = $("input[name='email']").val(),
            csrf = $("input[name='csrf']").val(),
            help_block = $("#forgot-password-help-block"),
            form = $(".modal-body .form-group");

        if (email === "" || csrf === "") {
            form.addClass("has-error");
            help_block.text("You must enter a valid email address!");
            return false;
        }

        var jqxhr = $.post("process.php", { email: email, csrf: csrf, task: 'forgotPassword' }, function (data) {
            form.removeClass().addClass("form-group has-" + (data.error ? 'error' : 'success'));
            help_block.text(data.message);
        }, "json");

        e.preventDefault();
    });

    $("select[name='roleId']").change(function () {
        var role_id = $(this).val();
        $('.permission').load('../_partials/user_group.php?role_id=' + role_id);
    });

    // Check to see if the redirect_to field is available

    if ($("textarea[name='template_data']").doesExist()) {
        $('#template_name').on('change', function () {
            var template = $(this).find(':selected').attr('value');
            $.post('../process.php', {task: 'findTemplateData', id: template}, function (data) {
                $('#template_subject').val(data.subject);
                $('#template_data').text(data.data);
                $('#placeholder').empty().append(data.fields);
            }, 'json');
        });
    }

    if ($('#redirect_to').doesExist()) {
        // FILL IT... FILL IT FILL IT!!!
        var member_url = $('#redirect_to'),
            redirect_to = $('.url');
        redirect_to.html(member_url.val());
    }


    $('#privilege-reminder').on('show', function () {
        // $('#modal-body').html('Loading...');
        var role_id = $('#default_group').find(":selected").attr('value');
        $('.modal-body').load('../_partials/permission_reminder.php?role_id=' + role_id);
    });

    $('.url').keyup(function () {
        var $this = $(this),
            path = $(".url_path");

        path.text($this.val());
    });


});

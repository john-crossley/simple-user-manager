<?php
require_once 'loader.php';
get_header('Register');

if (!empty($_POST)) {

    csrf_check('login.php');

    $v = new Validator;

    $rules = array(
        'username' => array('required', 'min:2', 'max:52', 'unique:user'),
        'password' => array('required', 'min:6'),
        'password_confirmation' => array('match:password'),
        'email' => array('required', 'valid_email', 'banned:' . get_banned_email_extensions(), 'unique:user')
    );

    $messages = array(
        'email.banned' => 'Banned email extension, please supply another.'
    );

    $v->make($_POST, $rules, $messages);

    if ($_SESSION['CAPTCHA']['ANSWER'] != $_POST['captcha']) {
        Flash::make('danger', CAPTCHA_FAILED);
        redirect('register.php');
    }

    if ($v->fails()) {
        Flash::make('danger', GENERIC_FORM_ERROR_MESSAGE);
        redirect('register.php');
    }

    $user = User::getInstance();
    $user->username = strip_tags($_POST['username']);
    $user->password = strip_tags($_POST['password']);
    $user->email = strip_tags($_POST['email']);
    $user->hash = md5(uniqid() . rand());
    $user->verified = 0;

    if ($id = $user->save()) {
        Role::insertUserRole($id, Settings::get('default_group'));

        $validateURL = URL . 'confirm.php?hash=' . $user->hash . '&amp;id=' . $id . '&amp;do=register';

        $template = DB::Table('template')->where('id', '=', 7)->grab(1)->get();
        $settings = get_settings(); // Get the settings

        if ($template) {
            $text = mini_parse($template->data, array(
                'username' => $user->username,
                'validate_url' => $validateURL,
                'system_name' => system_name(),
                'url' => URL,
                'year' => date('Y')
            ));

            $e = new Email;
            $e->to($user->email, fullname($user))
                ->from(system_email(), meta_author())
                ->subject($template->subject)
                ->template(TEMPLATE . 'generic_email_template.html', array(
                    'template' => nl2br($text),
                    'system_name' => system_name(),
                    'year' => date('Y'),
                    'url' => URL
                ))
                ->send();

            // Code can be moved out - this is a quick fix.
            if (isset($settings->email, $settings->meta_author) && $settings->email_on_register == 1) {
                $template = DB::Table('template')->where('id', '=', 8)->grab(1)->get();

                $text = mini_parse($template->data, array(
                    'username' => $user->username,
                    'admin_name' => meta_author(),
                    'system_name' => system_name(),
                    'year' => date('Y')
                ));

                $e = new Email;
                $e->to($settings->email)
                    ->from(system_email(), meta_author())
                    ->subject($template->subject)
                    ->template(TEMPLATE . 'generic_email_template.html', array(
                        'user' => $user->username,
                        'template' => nl2br($text),
                        'system_name' => system_name(),
                        'year' => date('Y')
                    ))
                    ->send();
            }

            Flash::make('success', _rd('username', $user->username, NEW_USER_REGISTERED));
            redirect('login.php');
        } else {
            Flash::make('danger', ERROR_OCCURRED_WHILE_PROCESSING_FORM);
            redirect('register.php');
        }
    }

}



?>
<body>
<?php echo get_menu('register'); ?>

<div class="container">

    <?php if (allow_registration()): ?>

        <form method="post" action="<?php echo root_path('register.php'); ?>" class="form-login-register" role="form">
            <h2 class="form-login-register-heading"><?php echo system_name(); ?> Registration</h2>
            <input type="hidden" name="task" value="login">
            <input type="hidden" name="csrf" value="<?php echo get_csrf_token(); ?>">

            <div class="form-group <?php echo form_has_error('username') ? 'has-error' : ''; ?>">
                <label for="username" class="control-label">Username</label>
                <input type="text" class="form-control" name="username" id="username"
                       placeholder="Username" value="<?= form_has_value('username') ?>" required>
                <small class="help-block"><?= form_has_message('username') ?></small>
            </div>

            <div class="form-group <?php echo form_has_error('email') ? 'has-error' : ''; ?>">
                <label for="email" class="control-label">Email address</label>
                <input type="email" class="form-control" name="email" id="email"
                       placeholder="Email address" value="<?= form_has_value('email') ?>" required>
                <small class="help-block"><?= form_has_message('email') ?></small>
            </div>

            <div class="form-group <?php echo form_has_error('password') ? 'has-error' : ''; ?>">
                <label for="password" class="control-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                       required>
                <small class="help-block"><?= form_has_message('password') ?></small>
            </div>

            <div class="form-group <?php echo form_has_error('password_confirmation') ? 'has-error' : ''; ?>">
                <label for="password_confirmation" class="control-label">Password confirmation</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                       placeholder="Password again" required>
                <small class="help-block"><?= form_has_message('password_confirmation') ?></small>
            </div>

            <div class="form-group <?php echo form_has_error('captcha') ? 'has-error' : ''; ?>">
                <label for="captcha" class="control-label">The sum of <?php echo get_captcha(); ?> = </label>
                <input type="text" class="form-control" name="captcha" id="captcha" placeholder="Whats the sum?"
                       required>
            </div>

            <button class="btn btn-success pull-left" type="submit">Register</button>
            <a href="<?php echo root_path('login'); ?>" data-toggle="modal" class="btn btn-link pull-right">Already have
                an account?</a>

        </form>

    <?php else: ?>
        <div class="alert alert-warning">
            <strong>Registration Closed!</strong><br>
            Our registration is currently closed due to maintenance, please come back soon!
        </div>
    <?php endif; ?>

</div>

<?php echo get_footer(); ?>
</body>
</html>

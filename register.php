<?php
require_once 'loader.php';
get_header('Register');

if (! empty($_POST)) {

    csrf_check('login.php');

    $v = new Validator;

    $rules = array(
        'username'       => array('required', 'min:2', 'max:52', 'unique:user'),
        'password'       => array('required', 'min:6'),
        'password_again' => array('required', 'match:password'),
        'email'          => array('required', 'valid_email', 'banned:'.get_banned_email_extensions(), 'unique:user'),
        'captcha'        => array('required')
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
    $user->email    = strip_tags($_POST['email']);
    $user->hash     = md5(uniqid().rand());
    $user->verified = 0;

    if ($id = $user->save()) {
        Role::insertUserRole($id, Settings::get('default_group'));

        $validateURL = URL . 'confirm.php?hash=' . $user->hash . '&amp;id=' . $id . '&amp;do=register';

        $template = DB::Table('template')->where('id', '=', 7)->grab(1)->get();

        if ($template) {
            $text = mini_parse($template->data, array(
                'username'     => $user->username,
                'validate_url' => $validateURL,
                'system_name'  => system_name(),
                'url'          => URL,
                'year'         => date('Y')
            ));

            $e = new Email;
            $e->to($user->email, fullname($user))
                ->from(system_email(), meta_author())
                ->subject($template->subject)
                ->template(TEMPLATE . 'generic_email_template.html', array(
                    'template'    => nl2br($text),
                    'system_name' => system_name(),
                    'year'        => date('Y'),
                    'url'         => URL
                  ))
                ->send();

            Flash::make('success', _rd('username', $user->username, NEW_USER_REGISTERED));
            redirect('login.php');
        } else {
            Flash::make('error', ERROR_OCCURRED_WHILE_PROCESSING_FORM);
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

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email address" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Password confirmation</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Password again" required>
            </div>

            <div class="form-group">
                <label for="captcha">The sum of <?php echo get_captcha(); ?> = </label>
                <input type="text" class="form-control" name="captcha" id="captcha" placeholder="Whats the sum?" required>
            </div>

            <button class="btn btn-success pull-left" type="submit">Register</button>
            <a href="<?php echo root_path('login'); ?>" data-toggle="modal" class="btn btn-link pull-right">Already have an account?</a>

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

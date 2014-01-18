<?php
require_once 'loader.php';
get_header('Register');

if (!empty($_POST)) {

  if (!_csrf()) {
    Flash::make('danger', CSRF_CHECK_FAILURE);
    redirect('register.php');
  } // end CSRF

  // Let the validation process begin
  $v = new Validator;

  // Prepare some rules.
  $rules = array(
    'username'       => array('min:2', 'max:52', 'unique:user'),
    'password'       => array('min:6'),
    'password_again' => array('match:password'),
    'email'          => array('valid_email', 'banned:'.get_banned_email_extensions(), 'unique:user'),
    'captcha'        => array('required')
  );

  $messages = array(
    'email.banned' => 'Banned email extension, please supply another!'
  );

  // Make the validator
  $v->make($_POST, $rules, $messages);

  // Now check captcha
  if ($_SESSION['CAPTCHA']['ANSWER'] != $_POST['captcha']) {
    Flash::make('danger', CAPTCHA_FAILED);
    redirect('register.php');
  }

  // Check if the validation fails.
  if ($v->fails()) {
    Flash::make('danger', GENERIC_FORM_ERROR_MESSAGE);
    redirect('register.php');
  }

  // Assume all went well and begin creating the user
  $user = User::getInstance();
  $user->username = $_POST['username'];
  $user->password = $_POST['password'];
  $user->email    = $_POST['email'];
  $user->hash     = md5(uniqid().rand());
  $user->verified = 0;

  if ($id = $user->save()) {

    // Assign this user to a default group
    Role::insertUserRole($id, Settings::get('default_group')); // Todo: Test this.

    // It's all lis, they're not even square
    $validateURL = URL . 'confirm.php?hash=' . $user->hash . '&amp;id=' . $id . '&amp;do=register';

    // Get the 'Welcome Email' template ready for parsing
    $template = DB::Table('template')->where('id', '=', 7)->grab(1)->get();

    if ($template) {
      // We have a template ladies :P
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
    }
    Flash::make('success', _rd('username', $user->username, NEW_USER_REGISTERED));
    redirect('login.php');
  } else {
    Flash::make('error', ERROR_OCCURRED_WHILE_PROCESSING_FORM);
    redirect('register.php');
  }
}

?>
<body>
    <?php echo get_menu('register'); ?>

        <div class="container">

            <?php if (allow_registration()): ?>

                <form method="post" action="<?php echo root_path('login.php'); ?>" class="form-login-register" role="form">
                    <h2 class="form-login-register-heading"><?php echo system_name(); ?></h2>
                    <input type="hidden" name="task" value="login">
                    <input type="hidden" name="csrf" value="<?php echo get_csrf_token(); ?>">

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="Email address" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Password" required>
                    </div>

                    <div class="form-group">
                        <label for="captcha">The sum of <?php echo get_captcha(); ?> = </label>
                        <input type="text" class="form-control" id="captcha" placeholder="Whats the sum?" required>
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

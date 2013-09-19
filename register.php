<?php
require_once 'bootstrap.php';
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

  <!-- Menu -->
  <?=get_menu('register')?>

  <div class="row">
    <div class="container">
      <?php if (allow_registration()): ?>
      <form method="post" action="<?=root_path("register.php")?>" class="form-register">
        <fieldset>
          <legend>Register to <?=system_name()?></legend>
        </fieldset>
        <div class="form-group has-<?=form_has_error('username')?>">
          <input type="hidden" name="task" value="register">
          <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
          <label for="username" class="control-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Enter a username" value="<?=form_has_value('username')?>">
          <small class="help-block"><?=form_has_message('username')?></small>
        </div><!--//.form-group-->
        <div class="form-group has-<?=form_has_error('email')?>">
          <label for="email" class="control-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" value="<?=form_has_value('email')?>">
          <small class="help-block"><?=form_has_message('email')?></small>
        </div><!--//.form-group-->
        <div class="form-group has-<?=form_has_error('password')?>">
          <label for="password" class="control-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter a password">
          <small class="help-block"><?=form_has_message('password')?></small>
        </div><!--//.form-group-->
        <div class="form-group has-<?=form_has_error('password_again')?>">
          <label for="password_again" class="control-label">Password again</label>
          <input type="password" class="form-control" id="password_again" name="password_again" placeholder="Password again">
          <small class="help-block"><?=form_has_message('password_again')?></small>
        </div><!--//.form-group-->
        <div class="form-group has-<?=form_has_error('captcha')?>">
          <label for="captcha" class="control-label">The sum of <?=get_captcha()?> = </label>
          <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Your answer">
          <small class="help-block"><?=form_has_message('captcha')?></small>
        </div><!--//.form-group-->
        <div class="form-group">
          <button class="btn btn-primary" type="submit">Register</button>
          <a href="<?=root_path('login.php')?>" class="btn btn-link pull-right">Already have account?</a>
        </div><!--//.form-group-->
      </form>
      <?php else: ?>
      <div class="alert alert-warning">
        <strong>Registration Closed!</strong><br>
        Our registration is currently closed due to maintenance, please come
        back soon!
      </div><!--//.alert alert-danger-->
      <?php endif; ?>
    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>

</body>
</html>

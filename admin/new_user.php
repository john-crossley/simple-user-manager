<?php
require_once '../loader.php';
get_header('New User'); // Get the header!
ensure_login(); // Ensure the user is logged in
$user = get_user(); // Get the logged in user.
check_user_access($user, 'accessAdminPanel', array('redirect' => 'admin/'));

if (!empty($_POST)) {

  if (isset($_POST['task']) && $_POST['task'] == 'newUserFromAdminPanel') {

    $email_user = false;

    if (!_csrf()) {
      Flash::make('danger', CSRF_CHECK_FAILURE);
      redirect('admin/new_user.php');
    } // end CSRF

    // Create a new instance of the validator
    $v = new Validator;

    $rules = array(
      'username'       => array('required', 'min:3', 'max:128', 'unique:user'),
      'email'          => array('required', 'valid_email', 'max:128', 'unique:user', 'banned:' . get_banned_email_extensions()),
      'password'       => array('required', 'min:6'),
      'password_again' => array('required', 'match:password'),
      'redirect_to'    => array('required')
    );

    $messages = array(
      'password_again.required' => 'The password again field is required',
      'redirect_to.required'     => 'The redirect to field is required'
    );

    $v->make($_POST, $rules, $messages);

    if ($v->fails()) {
      redirect('admin/new_user.php');
    }

    // All has gone well at this point
    $user = User::getInstance();

    if (isset($_POST['email_user']) && $_POST['email_user'] == 'on') {
      $email_user = true;
    }

    if (!empty($_POST['fullname'])) {
      $name = explode(' ', $_POST['fullname']);
      $user->firstname = $name[0];
      $user->lastname = (!empty($name[1])) ? $name[1] : '';
    }

    $user->username    = $_POST['username'];
    $user->email       = $_POST['email'];
    $user->password    = $_POST['password'];
    $user->redirect_to = $_POST['redirect_to'];

    $roleId = (int)$_POST['roleId'];

    if ($id = $user->save()) {
      // Save the role ID
      $roleId = (int)$_POST['roleId'];

      // What role has this user been assigned to?
      if ($roleId > 0) {
        Role::insertUserRole($id, $roleId);
        $user_group = Role::getRolePermissionData($roleId);
      }

      // Creator
      $creator = get_user();

      // a:5:{i:0;s:7:"creator";i:1;s:8:"username";i:2;s:8:"password";i:3;s:10:"user_email";i:4;s:10:"user_group";}

      // Do we need to email the user?
      if ($email_user) {
        if ($template = DB::table('template')->where('id', '=', 4)->grab(1)->get()) {
          $text = mini_parse($template->data, array(
            'creator'    => $creator->username,
            'username'   => $user->username,
            'password'   => $_POST['password'],
            'user_email' => $user->email,
            'user_group' => $user_group[0]->pretty_name
          ));
          $e = new Email;
          $e->to($user->email, fullname($user))
            ->from(system_email(), meta_author())
            ->subject($template->subject)
            ->template(TEMPLATE . 'generic_email_template.html', array(
                'template'     => nl2br($text),
                'system_name' => system_name(),
                'year'         => date('Y'),
                'url'          => URL
              ))
            ->send();
        }
      }
      Flash::make('success', 'Success, ' . $user->username . '\'s account has been created.');
      redirect('admin/new_user.php');
    }
  }
}

?>
<body>

  <!-- Menu -->
  <?=get_menu('home')?>

  <div class="row">
    <div class="container main">
      <div class="col-lg-3">
        <?=get_admin_sidebar('manage-users')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <h2>Create a new User</h2>
        <p>To create a new user simply use the form below!</p>

        <form method="post" action="<?=root_path("admin/new_user.php")?>" class="form-new-user">

          <div class="form-group has-<?=form_has_error('username')?>">
            <input type="hidden" name="task" value="newUserFromAdminPanel">
            <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
            <label for="username" class="control-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter a username" value="<?=form_has_value('username')?>">
            <small class="help-block"><?=form_has_message('username')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('fullname')?>">
            <label for="fullname" class="control-label">Full name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter the members full name" value="<?=form_has_value('fullname')?>">
            <small class="help-block"><?=form_has_message('fullname')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('email')?>">
            <label for="email" class="control-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter a valid email address" value="<?=form_has_value('email')?>">
            <small class="help-block"><?=form_has_message('email')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('password')?>">
            <label for="password" class="control-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter a password">
            <small class="help-block"><?=form_has_message('password')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('redirect_to')?>">
            <label for="redirect_to" class="control-label">Redirect</label>
            <input type="text" class="form-control" id="redirect_to" name="redirect_to" placeholder="Enter redirect location" value="<?=form_has_value('redirect_to')?>">
            <small class="help-block"><?=form_has_message('redirect_to', root_path() . '<span class="url"></span>')?></small>
          </div><!--//.form-group-->

          <div class="form-group">
            <label for="member-status" class="control-label">Assign to group</label>
            <select class="form-control" id="member-status" name="roleId">
              <option value="0">---</option>
              <?php foreach (Role::getSystemUserGroups() as $group): ?>
                <option value="<?=$group->role_id?>" <?=status(default_permission(), $group->role_id)?>><?=$group->role_name?></option>
              <?php endforeach; ?>
            </select>
          </div><!--//.form-group-->

          <div class="form-group">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="email_user" checked>
                Notify this user upon account creation?
              </label>
            </div><!--//.checkbox-->
          </div><!--//.form-group-->

          <div class="form-group">
            <button class="btn btn-primary pull-right">Create user</button>
            <a href="<?=root_path('admin/')?>" class="btn btn-danger" onclick="return confirm('Are you sure? Unsaved changes will be lost!');">Cancel</a>
          </div><!--//.form-group-->

        </form>

      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>

</body>
</html>

<?php
require_once '../loader.php';
ensure_login();
$user = get_user();
get_header("Viewing $user->username's Profile");
check_user_access($user, 'accessAdminPanel', array(
  'redirect' => 'member/'
));


if (!isset($_GET['user'])) {
  Flash::make('notice', UNABLE_TO_LOCATE_USER);
  redirect('admin/');
}

$user = User::findById((int)$_GET['user']);
if (!$user->username) redirect('admin/');

$role = $user->getCurrentUserRole($user->id, true);

// TODO: Has permission for this action?
if (isset($_POST['updateGroup']) && isset($_POST['roleId'])) {
  // Right we need to update this users group.
  if (Role::updateUserRole((int)$user->id, (int)$_POST['roleId'])) {
    Flash::make('success', 'The users group has been successfully updated!');
    redirect('admin/view.php?user='.$user->id);
  }
}

if (!empty($_POST)) {

  // Here we have an array of files.
  if (isset($_POST['protect']) && isset($_POST['user_id'])) {
    $user = User::findById((int)$_POST['user_id']);
    if (!$user) {
      Flash::make('danger', USER_PROFILE_NOT_FOUND);
      redirect('member/');
    }

    foreach ($_POST['protect'] as $file) {
      $file = split_file_path($file);
      // Todo: Don't loop and add the records.
      DB::table('private_pages')->insert(array(
        'user_id' => $user->id,
        'URL'     => $file
      ));
      Flash::make('success', USER_CAN_NOW_ACCESS_DIR);
      redirect('admin/view.php?user='.$user->id);
    }

  }

  if (isset($_POST['task']) && $_POST['task'] === "saveUserFromAdminPanel") {
    // CSRF check
    csrf_check();

    $data_changed = false;
    $email_user   = false;

    $v = new Validator;
    $rules = array();

    // Grab the user
    $user = User::findById((int)$_POST['user_id']);

    // Wait.. wut! No user?
    if (!$user) {
      Flash::make('danger', UNABLE_TO_LOCATE_USER);
      redirect('admin/view.php?user='.$user->id);
    }

    if (isset($_POST['email_user']) && $_POST['email_user'] == 'on')
      $email_user = true;

    if (isset($_POST['username']) && !empty($_POST['username'])) {
      $username = strip_tags($_POST['username']);
      if ($username != $user->username) {
        $data_changed = true; // Yes the data has changed.
        $rules['username'] = array('min:3', 'max:128', 'unique:user');
        $user->username = $username;
      }
    }

    // Account private?
    if (isset($_POST['account_private']) && $_POST['account_private'] == 'on') {
      // Do we need to even change it?
      $user->private = 1;
      $data_changed = true;
    } else {
      // It's off
      $user->private = 0;
      $data_changed = true;
    }

    if (isset($_POST['banned_from_sending_personal_messages']) && $_POST['banned_from_sending_personal_messages'] == 'on') {
      $user->banned_from_sending_personal_messages = 1;
    } else $user->banned_from_sending_personal_messages = 0;

    if (isset($_POST['fullname']) && !empty($_POST['fullname'])) {
      $names = explode(' ', $_POST['fullname']);
      $firstname = $names[0];
      $lastname  = (!empty($names[1])) ? $names[1] : '';
      if ($user->firstname != $firstname || $user->lastname != $lastname) {
        $data_changed = true;
        $user->firstname = $firstname;
        $user->lastname  = $lastname;
      }
    }

    if (isset($_POST['email']) && !empty($_POST['email'])) {
      $email = strip_tags($_POST['email']);
      if ($user->email != $email) {
        $data_changed = true;
        $rules['email'] = array('required', 'valid_email');
        $user->email = $email;
      }
    }

    if (isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['password_again']) && !empty($_POST['password_again'])) {

      $data_changed = true;

      $password = $_POST['password'];
      // Right so the password changed
      $rules['password']       = array('min:6');
      $rules['password_again'] = array('match:password');

      $user->password = $password;
      $passwordChange = $password;

    } else $passwordChange = 'No Change';

    if (isset($_POST['redirect_to']) && !empty($_POST['redirect_to'])) {
      if ($user->redirect_to != $_POST['redirect_to']) {
        $data_changed = true;
        $user->redirect_to = $_POST['redirect_to'];
      }
    }

    // Users BIO
    if (isset($_POST['bio']) && !empty($_POST['bio'])) {
      if ($user->bio != $_POST['bio']) {
        $data_changed = true;
        $user->bio = strip_tags($_POST['bio']); // Strip the tags
      }
    }

    // Users location
    if (isset($_POST['current_location']) && !empty($_POST['current_location'])) {
      if ($user->location != $_POST['current_location']) {
        $data_changed = true;
        $user->location = strip_tags($_POST['current_location']);
      }
    }

    if (isset($_POST['roleId']) && (int)$_POST['roleId'] > 0) {

      $current_user_group = get_role_raw($user);
      $roleId             = (int)$_POST['roleId'];

      // What role name has been selected?
      $selected_group = Role::getRoleNameFromRoleId($roleId);

      // Does this user even have a user group?
      if (!$current_user_group) {
        // User doesn't even have a group
        Role::insertUserRole($user->id, $roleId);
        $data_changed = true;
      }


      if ($selected_group != $current_user_group) {
        // Change!
        Role::updateUserRole($user->id, $roleId);
        $data_changed = true;
      }
    }

    if (isset($_POST['account_verification_status'])) {
      $status = (int)$_POST['account_verification_status'];

      if ($status != (int)$user->verified) {
        $data_changed = true;
        $user->verified = $status;
        $status_change_message = "<p><strong>Your account has now been activated.</strong></p>";
      } else $status_change_message = '';
    }

    $v->make($_POST, $rules);

    if ($v->fails()) {
      Flash::make('danger', GENERIC_FORM_ERROR_MESSAGE);
      redirect('admin/view.php?user='.$user->id);
    }

    // DEMO MODE BLOCK
    if (DEMO_MODE === true) {
      if ((int)$user->id === 1 || (int)$user->id === 2) {
        Flash::make('info', 'Your in demo mode and unable to change some user accounts.');
        redirect('admin/view.php?user='.$user->id);
      }
    }
    // DEMO MODE BLOCK

    if ($data_changed) {

      if ($user->save()) {

        if ($email_user) {

          $template = DB::table('template')->where('id', '=', 6)->grab(1)->get();
          if ($template) {

            $text = mini_parse($template->data, array(
              'username'              => $user->username,
              'fullname'              => fullname($user),
              'user_email'            => $user->email,
              'password'              => $passwordChange,
              'status_change_message' => $status_change_message,
              'user_group'            => $current_user_group,
              'account_private'       => ($user->private) ? 'Private Account' : 'Public Account',
              'bio'                   => $user->bio,
              'location'              => $user->location
            ));

            $e = new Email;
            $e->to($user->email, fullname($user))
              ->from(system_email(), meta_author())
              ->subject($template->subject)
              ->template(TEMPLATE . 'generic_email_template.html', array(
                  'template'    => nl2br($text),
                  'system_name' => system_name(),
                  'url'         => URL,
                  'year'        => date('Y'),
                ))
              ->send();
          } // template

        } // Email user.
        Flash::make('success', 'Success, ' . $user->username . '\'s account has been updated.');
        redirect('admin/view.php?user='.$user->id);
      }
      Flash::make('danger', UNABLE_TO_UPDATE_USER);
      redirect('admin/view.php?user='.$user->id);
    }

  }
}

if (isset($_POST['task']) && $_POST['task'] === 'delete_account' &&
  isset($_POST['user_id']) && !empty($_POST['user_id'])) {

  csrf_check('admin/view.php?user='.$user->id);

  // DEMO MODE BLOCK
  if (DEMO_MODE === true) {
    if ((int)$user->id === 1 || (int)$user->id === 2) {
      Flash::make('info', 'Your in demo mode and unable to delete some user accounts.');
      redirect('admin/view.php?user='.$user->id);
    }
  }
  // DEMO MODE BLOCK

  // Just a little check
  if ((int)$user->id === (int)$_POST['user_id']) {
    if (User::deleteUserById($user->id)) {
      Flash::make('success', DELETE_USER_SUCCESS);
      redirect('admin/view_users.php');
    }

  }
}
?>

<body>

  <!-- Menu -->
  <?=get_menu()?>

    <div class="row main">

      <div class="container">

        <h2>
          <img src="<?=get_gravatar($user->email, 50)?>" width="50" height="50"
                  class="gravatar" alt="<?=$user->username?>'s Gravatar Picture">
          <?=$user->username?> <?=fullname($user, true)?> <?=get_role($user)?>
        </h2>

        <div class="view-user-navigation">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#edit-account" data-toggle="tab">Edit Account</a></li>
            <li><a href="#access-areas" data-toggle="tab">Access Areas</a></li>
            <li><a href="#delete-account" data-toggle="tab">Delete Account</a></li>
          </ul>
        </div><!--//.view-user-navigation-->

        <div class="tab-content">

          <div class="active tab-pane" id="edit-account">

            <form method="post" action="<?=root_path('admin/view.php?user='.$user->id)?>">

              <div class="col-lg-6">

                <div class="form-group has-<?=form_has_error('username')?>">
                  <input type="hidden" name="task" value="saveUserFromAdminPanel">
                  <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
                  <input type="hidden" name="user_id" value="<?=$user->id?>">
                  <label for="username" class="control-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Enter a username" value="<?=$user->username?>">
                  <small class="help-block"><?=form_has_message('username')?></small>
                </div><!--//.form-group-->

                <div class="form-group has-<?=form_has_error('fullname')?>">
                  <label for="fullname" class="control-label">Full name</label>
                  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter the members full name" value="<?=fullname($user)?>">
                  <small class="help-block"><?=form_has_message('fullname')?></small>
                </div><!--//.form-group-->

                <div class="form-group has-<?=form_has_error('email')?>">
                  <label for="email" class="control-label">Email address</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter a valid email address" value="<?=$user->email?>">
                  <small class="help-block"><?=form_has_message('email')?></small>
                </div><!--//.form-group-->

                <div class="form-group has-<?=form_has_error('password')?>">
                  <label for="password" class="control-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter a password">
                  <small class="help-block"><?=form_has_message('password')?></small>
                </div><!--//.form-group-->

                <div class="form-group has-<?=form_has_error('password_again')?>">
                  <label for="password_again" class="control-label">Password again</label>
                  <input type="password" class="form-control" id="password_again" name="password_again" placeholder="Enter a password">
                  <small class="help-block"><?=form_has_message('password_again')?></small>
                </div><!--//.form-group-->

              </div><!--//.col-lg-6-->

              <div class="col-lg-6">

                <div class="form-group has-<?=form_has_error('bio')?>">
                  <label for="bio" class="control-label">Bio</label>
                  <textarea class="form-control" id="bio" name="bio" cols="0" rows="0"><?=$user->bio?></textarea>
                  <small class="help-block"><?=form_has_message('fullname')?></small>
                </div><!--//.form-group-->

                <div class="form-group has-<?=form_has_error('current_location')?>">
                  <label for="current_location" class="control-label">Current location</label>
                  <input type="text" class="form-control" id="current_location" name="current_location" placeholder="Enter the users location" value="<?=$user->location?>">
                  <small class="help-block"><?=form_has_message('current_location')?></small>
                </div><!--//.form-group-->

                <div class="form-group has-<?=form_has_error('redirect_to')?>">
                  <label for="redirect_to" class="control-label">Redirect to when logged in</label>
                  <input type="text" class="form-control" id="redirect_to" name="redirect_to" placeholder="Enter where the user should be redirect" value="<?=$user->redirect_to?>">
                  <small class="help-block"><?=form_has_message('redirect_to', root_path() . '<span class="url"></span>')?></small>
                </div><!--//.form-group-->

                <div class="form-group">
                  <label for="member-status" class="control-label">Assign to group</label>
                  <select class="form-control" id="member-status" name="roleId">
                    <option value="0">---</option>
                    <?php foreach (Role::getSystemUserGroups() as $group): ?>
                      <option value="<?=$group->role_id?>" <?=status($group->role_id, $role->role_id)?>><?=$group->role_name?></option>
                    <?php endforeach; ?>
                  </select>
                </div><!--//.form-group-->

                <hr>

                <div class="form-group">
                  <label for="account-active" class="control-label">Activation status</label>
                  <select class="form-control" name="account_verification_status" id="account-active">
                    <option value="1" <?=status(1, $user->verified)?>>Activated</option>
                    <option value="0" <?=status(0, $user->verified)?>>Deactivated</option>
                  </select>
                </div><!--//.form-group-->

                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="account_private" <?=is_checked($user->private)?>>
                      Make this account private?
                    </label>
                  </div><!--//.checkbox-->
                </div><!--//.form-group-->

                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="banned_from_sending_personal_messages" <?=is_checked($user->banned_from_sending_personal_messages)?>>
                      <strong>Ban</strong> this user from sending personal messages.
                    </label>
                  </div><!--//.checkbox-->
                </div><!--//.form-group-->

                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="email_user" checked>
                      Notify the user when their account is updated?
                    </label>
                  </div><!--//.checkbox-->
                </div><!--//.form-group-->

                <div class="form-group">
                  <button class="btn btn-primary pull-right">Update user</button>
                </div><!--//.form-group-->

              </div><!--//.col-lg-6-->

            </form>

          </div><!--//#access-areas-->

          <div class="tab-pane" id="access-areas">
            <h3>Access Areas</h3>
            <p>Sometimes you just want to lock down areas for members right? Well
            I have designed a system that allows you to do just that! The way it
            works is simple. Just use the field below to enter down a path local to this
            web application. <strong>Eg: member/any/path/to/files/</strong>. After
            the system has verfied the file may be locked down.</p>

            <div class="row main">

              <div class="col-lg-4">

                <ul class="list-group">
                  <?php $pages = $user->getPrivatePages(); ?>
                  <?php if (!$pages): ?>
                  <?php else: ?>
                    <li class="list-group-item">
                      <?=$user->username?>'s can access the following pages;
                    </li>
                    <?php foreach ($pages as $page): ?>
                    <li class="list-group-item">
                      <a href="<?=root_path($page->URL)?>"><?=$page->URL?></a>
                      <div class="pull-right">
                        <a href="#remove_page" data-id="<?=$page->id?>" class="label label-danger">Remove</a>
                        <a href="#protection_modal" data-toggle="modal" data-id="<?=$page->id?>" class="label label-success">How to protect</a>
                      </div>
                    </li>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </ul>

              </div><!--//.col-lg-4-->

              <div class="col-lg-8">
                <form method="POST" action="<?=root_path('admin/view.php?user='.$user->id)?>">
                  <div class="form-group has-<?=form_has_error('access_area_path')?>">
                    <div class="input-group">
                      <input type="hidden" name="user_id" value="<?=$user->id?>">
                      <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
                      <input type="text" class="form-control url" name="access_area_path" placeholder="Eg: /" value="/">
                      <span class="input-group-btn">
                        <button class="btn btn-success" id="access_areas_btn">Check</button>
                      </span>
                    </div><!-- /input-group -->
                    <small class="help-block"><?=root_path()?>member<span class="url_path"></span></small>
                  </div><!--//.form-group-->
                </form>

                <div id="directory_list">
                  <form method="POST" action="<?=root_path('admin/view.php?user='.$user->id)?>">
                  </form>
                </div><!--//#directory_list-->

              </div><!--//.col-lg-8-->

            </div><!--//.row main-->



          </div><!--//#access-areas-->

          <div class="tab-pane" id="delete-account">
            <p>If you remove the user from the system they will be unable to access
            their account and the information will be unretrievable.</p>

            <form method="POST" action="<?=root_path('admin/view.php?user='.$user->id)?>">
              <input type="hidden" name="task" value="delete_account">
              <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
              <input type="hidden" name="user_id" value="<?=$user->id?>">
              <button class="btn btn-danger" onclick="return confirm('You sure you want to delete this account?')">I understand, delete this account!</button>
            </form>
          </div><!--//#access-areas-->

        </div><!--//.tab-content-->

      </div><!--//.container-->

    </div><!--//.row-->

    <div class="modal fade" id="protection_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">How to protect this file</h4>
          </div>
          <div class="modal-body">
            <p>Almost done! Simply copy and paste the following code to the top of the file!</p>
            <code>
              &lt;?php<br>
              require_once&nbsp;'<span class="insert"></span>';<br>
              restrict_access();<br>
              ?&gt;
            </code>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">Okay, got it!</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

  <?=get_footer()?>

</body>
</html>

<?php
require_once '../loader.php';
get_header('Settings');
ensure_login();
$user = get_user();
check_user_access($user, 'accessSettingsPanel', array('redirect' => $user->redirect_to));
$settings = get_settings();

if (!empty($_POST) && isset($_POST['task']) && $_POST['task'] == 'saveSettingsFromAdminPanel') {
  // Do this
  csrf_check();

  // Start the validation process
  $v = new Validator;

  $rules = array(
    'system_name'      => array('required', 'max:128'),
    'system_email'     => array('required', 'max:128', 'valid_email'),
    'meta_description' => array('required'),
    'meta_author'      => array('required', 'max:128'),
    'system_url'       => array('required', 'valid_url')
  );

  $v->make($_POST, $rules);

  if ($v->fails()) {
    Flash::make('error', GENERIC_FORM_ERROR_MESSAGE);
    redirect('admin/settings.php');
  }

  // Prep some veggies
  if (isset($_POST['banned_extensions']) && !empty($_POST['banned_extensions'])) {
    $banned_extensions = strip_tags($_POST['banned_extensions']);
  }

  $system_name      = strip_tags($_POST['system_name']);
  $system_email     = strip_tags($_POST['system_email']);
  $meta_description = strip_tags($_POST['meta_description']);
  $meta_author      = strip_tags($_POST['meta_author']);
  $system_url       = strip_tags($_POST['system_url']);

  if (isset($_POST['registration_status']) && !empty($_POST['registration_status'])) {
    $registration_status = (int)$_POST['registration_status'];
  } else $registration_status = 1;

  if (isset($_POST['pm_disabled']) && !empty($_POST['pm_disabled'])) {
    $pm_disabled = (int)$_POST['pm_disabled'];
  } else $pm_disabled = null;

  if (isset($_POST['default_group']) && !empty($_POST['default_group'])) {
    $default_group = (int)$_POST['default_group'];
  } else $default_group = null; // Default to nothing

  // DEMO MODE BLOCK
  if (DEMO_MODE === true) {
    Flash::make('info', 'Your in demo mode and unable to change system settings.');
    redirect('admin/settings.php');
  }
  // DEMO MODE BLOCK

  // Right insert this
  $result = DB::table('setting')->where('id', '=', 1)->update(array(
              'name'                    => $system_name,
              'url'                     => $system_url,
              'meta_author'             => $meta_author,
              'meta_description'        => $meta_description,
              'banned_email_extensions' => $banned_extensions,
              'default_group'           => $default_group,
              'email'                   => $system_email,
              'allow_registration'      => (int)$_POST['registration_status'],
              'pm_disabled'             => $pm_disabled,
              'username_disabled'       => (int)$_POST['username_disabled']
            ));

  if ($result) {
    // Success!
    Flash::make('success', 'Settings have been updated successfully!');
    redirect('admin/settings.php');
  }

}
?>

<body>
  <?=get_menu()?>
  <div class="row">
    <div class="container main">
      <div class="col-lg-3">
        <?=get_admin_sidebar('options')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

      <h2>System Settings</h2>
      <p>Here you can modify some of the system settings.</p>

        <form action="<?=root_path('admin/settings.php')?>" method="post">

          <div class="form-group has-<?=form_has_error('system_name')?>">
            <input type="hidden" name="task" value="saveSettingsFromAdminPanel">
            <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
            <label for="system_name" class="control-label">System name</label>
            <input type="text" class="form-control" id="system_name" name="system_name" placeholder="The name of the system" value="<?=$settings->name?>">
            <small class="help-block"><?=form_has_message('system_name')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('system_email')?>">
            <label for="system_email" class="control-label">System email</label>
            <input type="email" class="form-control" id="system_email" name="system_email" placeholder="The system email address" value="<?=$settings->email?>">
            <small class="help-block"><?=form_has_message('system_email')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('meta_description')?>">
            <label for="bio" class="control-label">Meta description</label>
            <textarea class="form-control" id="meta_description" name="meta_description" cols="0" rows="0"><?=$settings->meta_description?></textarea>
            <small class="help-block"><?=form_has_message('meta_description')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('meta_author')?>">
            <label for="meta_author" class="control-label">Meta author</label>
            <input type="text" class="form-control" id="meta_author" name="meta_author" placeholder="The systems author" value="<?=$settings->meta_author?>">
            <small class="help-block"><?=form_has_message('meta_author')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('system_url')?>">
            <label for="system_url" class="control-label">System URL</label>
            <input type="text" class="form-control" id="system_url" name="system_url" placeholder="Eg: <?=root_path()?>" value="<?=$settings->url?>">
            <small class="help-block"><?=form_has_message('system_url')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('banned_extensions')?>">
            <label for="banned_extensions" class="control-label">Banned extensions</label>
            <input type="text" class="form-control" id="banned_extensions" name="banned_extensions" placeholder="The system email address" value="<?=$settings->banned_email_extensions?>">
            <small class="help-block"><?=form_has_message('banned_extensions', 'Separate email extensions by a space!')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('registration_status')?>">
            <label for="registration_status" class="control-label">Registration status</label>
            <select id="registration_status" class="form-control" name="registration_status">
              <option value="1" <?=status(1, $settings->allow_registration)?>>Open Registration</option>
              <option value="0" <?=status(0, $settings->allow_registration)?>>Close Registration</option>
            </select>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('pm_disabled')?>">
            <label for="pm_disabled" class="pm_disabled-label">Personal Message System</label>
            <select id="pm_disabled" class="form-control" name="pm_disabled">
              <option value="1" <?=status(1, $settings->pm_disabled)?>>Enabled</option>
              <option value="0" <?=status(0, $settings->pm_disabled)?>>Disabled</option>
            </select>
            <small class="help-block">This system allows members to send personal messages to each other</small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('default_group')?>">
            <label for="default_group" class="control-label">Default group</label>
            <select id="default_group" class="form-control" name="default_group">
              <?php foreach (Role::getSystemUserGroups() as $group): ?>
                <option value="<?=$group->role_id?>" <?=($settings->default_group==$group->role_id) ? 'selected':''?>><?=$group->role_name?></option>
              <?php endforeach; ?>
            </select>
            <small class="help-block">
              <a href="#privilege-reminder" role="button" data-toggle="modal">What privileges does this group have?</a>
            </small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('username_disabled')?>">
            <label for="username_disabled" class="username_disabled-label">Username disabled?</label>
            <select id="username_disabled" class="form-control" name="username_disabled">
              <option value="1" <?=status(1, $settings->username_disabled)?>>Yes (Disabled)</option>
              <option value="0" <?=status(0, $settings->username_disabled)?>>No (Enabled)</option>
            </select>
            <small class="help-block">
              Here you can disable the use of the username allowing the user to login with their email address.
            </small>
          </div><!--//.form-group-->

          <div class="form-group">
            <button type="submit" class="btn btn-primary btn-xs pull-right" name="save">Save changes</button>
          </div><!--//.form-group-->

        </form>
      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <div class="modal fade" id="privilege-reminder">
    <form action="<?=root_path('process.php')?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">The Permissions you Requested...</h4>
          </div>
          <div class="modal-body">
          </div><!--//.modal-body-->
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
          </div><!--//.modal-footer-->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <?=get_footer()?>
</body>
</html>

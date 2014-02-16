<?php
require_once '../loader.php';
get_header('Access Control List (ACL)');
ensure_login();
$user = get_user();
check_user_access($user, 'accessAdminPanel');

if (!empty($_POST)) {

    if (isset($_POST['new']) && isset($_POST['roleName'])) {
        // Oooo we have a new group being added.
        // Check to see the role name is not in use.
        if (Role::checkRoleNameIsNotInUse($_POST['roleName']) === true) {
            Flash::make('danger', 'Sorry but that group name has been taken. Select another one!');
            redirect('admin/user_group.php');
        }
        $roleName = strip_tags($_POST['roleName']);
        $rolePermissions = (array)$_POST['hasPermissons'];
        // All seems well proceed..
        if ($roleId = Role::insertRole($roleName)) {
            // GOOOD
            foreach ($rolePermissions as $permission)
                Role::insertPermission($roleId, $permission);

            Flash::make('success', USER_GROUP_SUCCESSFULLY_ADDED);
            redirect('admin/user_groups.php');
        }
    }
}

?>
<body>
<?php get_menu(); ?>

<div class="col-sm-3 col-md-2 sidebar">
    <?php get_admin_sidebar('create-group'); ?>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h2>System Settings</h2>

    <p>Here you can modify some of the system settings.</p>

    <form action="<?= root_path('admin/settings.php') ?>" method="post">

        <div class="form-group has-<?= form_has_error('system_name') ?>">
            <input type="hidden" name="task" value="saveSettingsFromAdminPanel">
            <input type="hidden" name="csrf" value="<?= get_csrf_token() ?>">
            <label for="system_name" class="control-label">System name</label>
            <input type="text" class="form-control" id="system_name" name="system_name"
                   placeholder="The name of the system" value="<?= $settings->name ?>">
            <small class="help-block"><?= form_has_message('system_name') ?></small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('system_email') ?>">
            <label for="system_email" class="control-label">System email</label>
            <input type="email" class="form-control" id="system_email" name="system_email"
                   placeholder="The system email address" value="<?= $settings->email ?>">
            <small class="help-block"><?= form_has_message('system_email') ?></small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('meta_description') ?>">
            <label for="bio" class="control-label">Meta description</label>
            <textarea class="form-control" id="meta_description" name="meta_description" cols="0"
                      rows="0"><?= $settings->meta_description ?></textarea>
            <small class="help-block"><?= form_has_message('meta_description') ?></small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('meta_author') ?>">
            <label for="meta_author" class="control-label">Meta author</label>
            <input type="text" class="form-control" id="meta_author" name="meta_author"
                   placeholder="The systems author" value="<?= $settings->meta_author ?>">
            <small class="help-block"><?= form_has_message('meta_author') ?></small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('system_url') ?>">
            <label for="system_url" class="control-label">System URL</label>
            <input type="text" class="form-control" id="system_url" name="system_url"
                   placeholder="Eg: <?= root_path() ?>" value="<?= $settings->url ?>">
            <small class="help-block"><?= form_has_message('system_url') ?></small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('banned_extensions') ?>">
            <label for="banned_extensions" class="control-label">Banned extensions</label>
            <input type="text" class="form-control" id="banned_extensions" name="banned_extensions"
                   placeholder="The system email address" value="<?= $settings->banned_email_extensions ?>">
            <small
                class="help-block"><?= form_has_message('banned_extensions', 'Separate email extensions by a space!') ?></small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('registration_status') ?>">
            <label for="registration_status" class="control-label">Registration status</label>
            <select id="registration_status" class="form-control" name="registration_status">
                <option value="1" <?= status(1, $settings->allow_registration) ?>>Open Registration</option>
                <option value="0" <?= status(0, $settings->allow_registration) ?>>Close Registration</option>
            </select>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('pm_disabled') ?>">
            <label for="pm_disabled" class="pm_disabled-label">Personal Message System</label>
            <select id="pm_disabled" class="form-control" name="pm_disabled">
                <option value="1" <?= status(1, $settings->pm_disabled) ?>>Enabled</option>
                <option value="0" <?= status(0, $settings->pm_disabled) ?>>Disabled</option>
            </select>
            <small class="help-block">This system allows members to send personal messages to each other</small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('default_group') ?>">
            <label for="default_group" class="control-label">Default group</label>
            <select id="default_group" class="form-control" name="default_group">
                <?php foreach (Role::getSystemUserGroups() as $group): ?>
                    <option
                        value="<?= $group->role_id ?>" <?= ($settings->default_group == $group->role_id) ? 'selected' : '' ?>><?= $group->role_name ?></option>
                <?php endforeach; ?>
            </select>
            <small class="help-block">
                <a href="#privilege-reminder" role="button" data-toggle="modal">What privileges does this group
                    have?</a>
            </small>
        </div>
        <!--//.form-group-->

        <div class="form-group has-<?= form_has_error('username_disabled') ?>">
            <label for="username_disabled" class="username_disabled-label">Username disabled?</label>
            <select id="username_disabled" class="form-control" name="username_disabled">
                <option value="1" <?= status(1, $settings->username_disabled) ?>>Yes (Disabled)</option>
                <option value="0" <?= status(0, $settings->username_disabled) ?>>No (Enabled)</option>
            </select>
            <small class="help-block">
                Here you can disable the use of the username allowing the user to login with their email
                address.
            </small>
        </div>
        <!--//.form-group-->

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-xs pull-right" name="save">Save changes</button>
        </div>
        <!--//.form-group-->

    </form>
</div>

<?php get_admin_footer(); ?>
</body>
</html>

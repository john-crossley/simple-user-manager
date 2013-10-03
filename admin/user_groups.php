<?php
require_once '../loader.php';
get_header('Access Control List (ACL)');
ensure_login();
$user = get_user();
check_user_access($user, 'accessAdminPanel');

if (!empty($_POST)) {

  // DEMO MODE BLOCK
  if (DEMO_MODE === true) {
    if ((int)$_POST['roleId'] === 1 || (int)$_POST['roleId'] === 2 || (int)$_POST['roleId'] === 3) {
      Flash::make('info', 'Your in demo mode and unable to change some permissions.');
      redirect('admin/user_groups.php');
    }
  }
  // DEMO MODE BLOCK

  /**
   * When a user wants to delete a user group.
   */
  if (isset($_POST['delete']) && isset($_POST['roleId'])) {
    // We need to delete a role
    if (Role::removeUserRoleFromDatabase($_POST['roleId'])) {
      Flash::make('success', SUCCESSFULLY_REMOVED_USER_GROUP);
      redirect('admin/user_groups.php');
    }
  }

  /**
   * When the user wants to update a current user group
   */
  if (isset($_POST['roleId'])) {
    // Right we need to save the updated permissions
    $roleId = $_POST['roleId'];
    $hasPermissons = (array)$_POST['hasPermissons'];

    // Delete the permissions associated with this role.
    if (Role::deleteOnlyRolePermissions($roleId)) {

      // Continue...
      foreach ($hasPermissons as $permission)
        Role::insertPermission($roleId, $permission);

      // All is well in the sleepy town of Googenbürg.
      Flash::make('success', SUCCESSFULLY_UPDATE_USER_GROUP);
      redirect('admin/user_groups.php');
    }
  }
}

?>
<body>
  <?=get_menu()?>
  <div class="row">
    <div class="container main">

      <div class="col-lg-3">
        <?=get_admin_sidebar('user-groups')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">
        <h2>Viewing User Groups</h2>
        <p>The <strong>ACL</strong> was built to give you full control over what
          your members can do. There are <strong>3</strong> default groups I have prepared for you.
          <br>The groups are <strong>Administrator</strong>, <strong>Member</strong> and <strong>Banned</strong>.</p>

        <form method="post" action="<?=root_path('admin/user_groups.php')?>">
          <div class="form-group">
            <label for="role-id" class="control-label">User groups</label>
            <select name="roleId" id="role-id" class="form-control">
              <option>-- Select Group --</option>
              <?php foreach (get_user_groups() as $group): ?>
                <option value="<?=$group->role_id?>"><?=$group->role_name?></option>
              <?php endforeach; ?>
            </select>
            <small class="help-block">
              <button type="submit" name="delete" onclick="return confirm('Delete this Role?');" class="btn btn-danger btn-xs pull-right">Delete Group</button>
            </small>
          </div><!--//.form-group-->

          <div class="permission"></div>

        </form>

      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->
  <?=get_footer()?>
</body>
</html>

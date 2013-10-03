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
      Flash::make('error', 'Sorry but that group name has been taken. Select another one!');
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
  <?=get_menu()?>
  <div class="row">
    <div class="container main">

      <div class="col-lg-3">
        <?=get_admin_sidebar('user-groups')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <h2>New User Group</h2>
        <p>Don't like the default groups provided? Then make your own. Just choose an alpha-numeric name
        and then select what permissions that newly created group has. Affects will take place immediately.</p>

        <form method="post" action="<?=root_path('admin/new_user_group.php')?>">
          <div class="form-group has-<?=form_has_error('roleName')?>">
            <label for="role_name" class="control-label">Role name</label>
            <input type="text" class="form-control" id="role_name" name="roleName" placeholder="Enter the name of the new role" value="<?=form_has_value('roleName')?>">
            <small class="help-block"><?=form_has_message('roleName', 'Only alpha-numeric characters allowed')?></small>
          </div><!--//.form-group-->

          <table class="table table-hover table-hover">
            <thead>
              <tr>
                <th>Enabled</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach (Role::getAvailablePermissions() as $systemPermission): ?>
              <tr>
                <td>
                  <input type="checkbox" id="<?=$systemPermission->description?>"
                    name="hasPermissons[<?=$systemPermission->id?>]" value="<?=$systemPermission->id?>">
                </td>
                <td>
                  <label for="<?=$systemPermission->description?>"><?=$systemPermission->pretty_name?></label>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>

          <div class="form-group">
            <button type="submit" name="new" class="btn btn-primary btn-xs pull-right">Add Group</button>
          </div><!--//.form-group-->

        </form>

      </div><!--//.col-lg-3-->

    </div><!--//.container-->
  </div><!--//.row-->
  <?=get_footer()?>
</body>
</html>

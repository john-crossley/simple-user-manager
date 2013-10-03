<?php
require_once '../loader.php';

if (isset($_GET['role_id'])) {
  $role_id = (int)$_GET['role_id'];

  $user_perms = Role::getRolePermissionData($role_id);

  $system_perms = Role::getAvailablePermissions();

  // if (!$user_perms) die('no permissions found');
  if (!$user_perms) $user_perms = array();

  $isChecked = false; // Default love.

}

if ($role_id === 1) { ?>
  <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Warning!</strong> please ensure you do not lock your self out! as you yourself are
    an administrator.
  </div><!--//alert-->
<?php } $isChecked = false; ?>
<table class="table table-condensed table-hover">
  <thead>
    <tr>
      <th>Permission enabled</th>
      <th>Permission name</th>
    </tr>
  </thead>
  <tbody>
  <?php
  foreach ($system_perms as $perm1) {
    foreach ($user_perms as $perm2) {
      if ($perm1->id == $perm2->id) $isChecked = true;
    } ?>
    <tr>
      <td>
        <input type="checkbox" id="<?=$perm1->description?>" name="hasPermissons[<?=$perm1->id?>]" value="<?=$perm1->id?>" <?=($isChecked)?'checked':null?>>
      </td>
      <td>
        <label class="checkbox" for="<?=$perm1->description?>"><?=$perm1->pretty_name?></label>
      </td>
    </tr>
  <?php $isChecked = false; } ?>
  </tbody>
</table>

<div class="form-group">
  <button class="btn btn-success pull-right">Save</button>
</div><!--//.form-group-->

<?php
require_once '../loader.php';

if (isset($_GET['role_id'])) {
  $roleId = (int)$_GET['role_id'];
  $permissions = Role::getRolePermissionData($roleId)
  ?>

  <table class="table table-condensed">
    <thead>
      <tr>
        <th>Permission Data</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($permissions)): ?>
        <tr class="danger">
          <td><small>No permissions...</small></td>
        </tr>
      <?php else: ?>
        <?php foreach ($permissions as $permission): ?>
          <tr class="success">
            <td><small><?=$permission->pretty_name?></small></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <?php
}

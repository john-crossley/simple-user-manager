<?php
$user_count = DB::table('user')->count();
$group_count = DB::table('role')->count();
?>
<ul class="nav nav-pills nav-stacked">
  <li <?=_is_active($active, 'admin-home')?>>
    <a href="<?=root_path('admin/')?>">
      <i class="glyphicon glyphicon-home"></i>
      Home
    </a>
  </li>
  <li class="dropdown <?=_is_active($active, 'manage-users', true)?>">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <i class="glyphicon glyphicon-user"></i>
      <span class="badge pull-right"><?=$user_count->count?></span>
      Manage Users
    </a>
    <ul class="dropdown-menu">
      <li><a href="<?=root_path('admin/view_users.php')?>">View Users</a></li>
      <li><a href="<?=root_path('admin/new_user.php')?>">Create User</a></li>
    </ul>
  </li>
  <li class="<?=_is_active($active, 'user-groups', true)?>">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <i class="glyphicon glyphicon-list"></i>
      <span class="badge pull-right"><?=$group_count->count?></span>
      Manage Groups (ACL)
    </a>
    <ul class="dropdown-menu">
      <li><a href="<?=root_path('admin/user_groups.php')?>">View Groups</a></li>
      <li><a href="<?=root_path('admin/new_user_group.php')?>">Create Group</a></li>
      <li><a href="<?=root_path('admin/user_group_access.php')?>">Access Areas</a></li>
    </ul>
  </li>
  <li class="dropdown <?=_is_active($active, 'options', true)?>">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <i class="glyphicon glyphicon-cog"></i>
      Options
    </a>
    <ul class="dropdown-menu">
      <li><a href="<?=root_path('admin/settings.php')?>">System Settings</a></li>
      <li><a href="<?=root_path('admin/templates.php')?>">Templates</a></li>
    </ul>
  </li>
  <li>
    <a href="<?=root_path('help/')?>">
    <i class="glyphicon glyphicon-question-sign"></i>
      Help
    </a>
  </li>
</ul>

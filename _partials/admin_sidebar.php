<ul class="nav nav-sidebar">
    <li <?php echo _is_active($active, 'admin-home'); ?>>
        <a href="<?php echo root_path('admin/'); ?>">Admin Home</a>
    </li>
</ul>
<ul class="nav nav-sidebar">
    <li class="disabled">
        <a href="#">Manage Users</a>
    </li>
    <li <?php echo _is_active($active, 'view-users') ?>>
        <a href="<?php echo root_path('admin/view_users.php'); ?>">View Users</a>
    </li>
    <li <?php echo _is_active($active, 'new-user') ?>>
        <a href="<?php echo root_path('admin/new_user.php'); ?>">Create User</a>
    </li>
</ul>
<ul class="nav nav-sidebar">
    <li class="disabled">
        <a href="#">Access Control List</a>
    </li>
    <li <?php echo _is_active($active, 'view-groups'); ?>>
        <a href="<?= root_path('admin/user_groups.php') ?>">View Groups</a>
    </li>
    <li <?php echo _is_active($active, 'create-group'); ?>>
        <a href="<?= root_path('admin/new_user_group.php') ?>">Create Group</a>
    </li>
    <li <?php echo _is_active($active, 'group-access'); ?>>
        <a href="<?= root_path('admin/user_group_access.php') ?>">Access Areas</a>
    </li>
</ul>
<ul class="nav nav-sidebar">
    <li class="disabled">
        <a href="#">System Options</a>
    </li>
    <li <?php echo _is_active($active, 'settings'); ?>>
        <a href="<?= root_path('admin/settings.php') ?>">Settings</a>
    </li>
    <li <?php echo _is_active($active, 'templates'); ?>>
        <a href="<?= root_path('admin/templates.php') ?>">Templates</a>
    </li>
</ul>
<ul class="nav nav-sidebar">
    <li class="disabled">
        <a href="#">Version <?php echo system_version(); ?></a>
    </li>
</ul>
<?php
require_once '../loader.php';
get_header('Access Control List (ACL)');
ensure_login();
$user = get_user();
check_user_access($user, 'editUserGroupAccessAreas', array(
    'redirect' => 'admin/'
));
?>
<body>
<?php get_menu(); ?>

<div class="col-sm-3 col-md-2 sidebar">
    <?php get_admin_sidebar('group-access'); ?>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h2>User Group Access Areas</h2>

    <p>Use the form below by selecting the user group and the path you would
        like only that user group to protect. The system will then tell you how
        you may go about securing that page.</p>

    <form method="POST" action="<?= root_path('admin/user_group_access.php') ?>">
        <div class="form-group">
            <label for="role-id" class="control-label">User groups</label>
            <select name="role_id" id="role-id" class="form-control">
                <option>-- Select Group --</option>
                <?php foreach (get_user_groups() as $group): ?>
                    <option value="<?php echo $group->role_id; ?>"><?php echo $group->role_name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!--//.form-group-->

        <div class="form-group">
            <div class="input-group">
                <input type="hidden" name="csrf" value="<?= get_csrf_token() ?>">
                <input type="text" class="form-control url" name="user_group_access" placeholder="Eg: /"
                       value="/">
              <span class="input-group-btn">
                <button class="btn btn-success" id="how_to_protect_btn">How to Protect</button>
              </span>
            </div>
            <!-- /input-group -->
            <small class="help-block"><?php echo root_path(); ?>member<span class="url_path"></span></small>
        </div>
        <!--//.form-group-->
    </form>

    <div id="how_to_protect"></div>

</div>

<?php get_admin_footer(); ?>
</body>
</html>

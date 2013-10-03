<?php
require_once '../loader.php';
get_header('Access Control List (ACL)');
ensure_login();
$user = get_user();
check_user_access($user, 'accessAdminPanel');
?>
<body>
  <?=get_menu()?>
  <div class="row">
    <div class="container main">

      <div class="col-lg-3">
        <?=get_admin_sidebar('user-groups')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <h2>User Group Access Areas</h2>

        <p>Use the form below by selecting the user group and the path you would
        like only that user group to protect. The system will then tell you how
        you may go about securing that page.</p>

        <form method="POST" action="<?=root_path('admin/user_group_access.php')?>">
          <div class="form-group">
            <label for="role-id" class="control-label">User groups</label>
            <select name="role_id" id="role-id" class="form-control">
              <option>-- Select Group --</option>
              <?php foreach (get_user_groups() as $group): ?>
                <option value="<?=$group->role_id?>"><?=$group->role_name?></option>
              <?php endforeach; ?>
            </select>
          </div><!--//.form-group-->

          <div class="form-group">
            <div class="input-group">
              <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
              <input type="text" class="form-control url" name="user_group_access" placeholder="Eg: /" value="/">
              <span class="input-group-btn">
                <button class="btn btn-success" id="how_to_protect_btn">How to Protect</button>
              </span>
            </div><!-- /input-group -->
            <small class="help-block"><?=root_path()?>member<span class="url_path"></span></small>
          </div><!--//.form-group-->
        </form>

        <div id="how_to_protect"></div>

      </div><!--//.col-lg-3-->

    </div><!--//.container-->
  </div><!--//.row-->
  <?=get_footer()?>
</body>
</html>

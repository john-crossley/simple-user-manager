<?php
require_once '../loader.php';
get_header('Admin Panel');
ensure_login();
$user = get_user();
check_user_access($user, 'accessAdminPanel', array('redirect' => 'member/'));
?>
<body>
  <?php echo get_menu('home'); ?>

  <div class="row">
    <div class="container main">
      <div class="col-lg-3">
        <?php echo get_admin_sidebar('admin-home'); ?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <div class="panel panel-default">

          <div class="panel-heading">
            <strong>Welcome back, <?php echo $user->username; ?></strong>
          </div><!--//panel-heading-->

          <div class="panel-body">
            <p>Welcome to the <strong class="text-danger">Administrator</strong> panel. To navigate around the
            application use the menu to <strong class="text-success">left</strong>. Should you require any
            help using the application then please consult the documentation. If the documentation does not assist you
            then you may contact <strong class="text-info">John Crossley (Me)</strong> directly.
            <a href="mailto:hello@phpcodemonkey.com?subject=HELP!">hello@phpcodemonkey.com</a></p>
          </div><!--//panel-body-->

        </div><!--//panel panel-default-->

        <p class="text-info">
          If you'd like to suggest a feature for the application then please contact me.
          If I think the feature is worth adding then I will try and add it to the next release.
        </p>

      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?php echo get_footer(); ?>
</body>
</html>

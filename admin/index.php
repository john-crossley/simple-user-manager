<?php
require_once '../bootstrap.php';
get_header('Admin Panel');

// Make sure that we have a user logged in
// before they can view this page.
ensure_login();

// Now grab the logged in user
$user = get_user();

// Check to see if the user has access to this section of the website.
check_user_access($user, 'accessAdminPanel', array('redirect' => 'member/'));

// Count the users messages.
// $messageCount = $user->countMessages();

?>
<body>
  <?=get_menu('home')?>

  <div class="row">
    <div class="container main">
      <div class="col-lg-3">
        <?=get_admin_sidebar('admin-home')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <div class="panel">
          <div class="panel-heading">
            <h3 class="panel-title">Welcome back, <?=$user->username?></h3>
          </div><!--//.panel-heading-->
          <p>Welcome to the <strong class="text-danger">Administrator</strong> panel. To navigate around the
          application use the menu to <strong class="text-success">left</strong>. Should you require any
          help using the application then please consult the documentation. If the documentation does not assist you
          then you may contact <strong class="text-info">John Crossley (Me)</strong> directly.
          <a href="mailto:hello@phpcodemonkey.com?subject=HELP!">hello@phpcodemonkey.com</a></p>
        </div><!--//.panel-->

        <p class="text-info">
          If you'd like to suggest a feature for the application then please contact me.
          If I think the feature is worth adding then I will try and add it to the next release.
        </p>

      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>
</body>
</html>

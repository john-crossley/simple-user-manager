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

$news = get_news_from_phpcodemonkey();
?>
<body>
  <?=get_menu('home')?>

  <div class="row">
    <div class="container main">
      <div class="col-lg-3">
        <?=get_admin_sidebar('admin-home')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <ul class="nav nav-tabs nav-justified">
          <li class="active"><a href="#admin-home-panel" data-toggle="tab">Admin Home Panel</a></li>
          <li><a href="#notification-centre" data-toggle="tab">Notification Centre</a></li>
        </ul>

        <div class="tab-content">

          <div class="active tab-pane" id="admin-home-panel">
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>Welcome back, <?=$user->username?></strong>
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
          </div><!--//active tab-pane-->

          <div class="tab-pane" id="notification-centre">
            <p>Hello and welcome to the <strong class="text-info">Notification Centre</strong> here
            is where I'll keep you all up to date with important information relating
            to the <strong class="text-danger">Advanced User Management System</strong>. Please ensure
            you check this tab on a regular basis so you can stay informed.</p>
            <hr>

            <div class="update-container">
              <?php if (!empty($news)): ?>
              <ul>
                <?php foreach($news as $news_item): ?>
                  <li>
                    <strong class="text-<?=$news_item->type?>"><?=$news_item->title?></strong><br>
                    <p><?=$news_item->description?></p>
                    <p><small><strong>Created at: </strong> <?=date(TIME_FORMAT, strtotime($news_item->created_at))?></small></p>
                    <?php if (!empty($news_item->download_link)): ?>
                      <p><a href="<?=$news_item->download_link?>">Link from description</a></p>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
              <?php else: ?>
                <strong class="text-info">There is no news at the moment.</strong>
              <?php endif; ?>
            </div><!--//update-container-->

          </div><!--//active tab-pane-->

        </div><!--//tab-content-->

      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>
</body>
</html>

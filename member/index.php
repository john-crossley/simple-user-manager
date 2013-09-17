<?php
require_once '../bootstrap.php';
get_header('Member Area');
ensure_login();
$user = get_user();

?>
<body>
  <?=get_menu()?>

  <div class="row">
    <div class="container main">

      <div class="col-lg-3">
        <div class="list-group">
          <a href="<?=root_path('member/')?>" class="list-group-item active">Member Panel</a>
          <a href="<?=root_path('profile.php')?>" class="list-group-item">Profile</a>
          <?php if (pm_system_enabled()): ?>
          <a href="<?=root_path('messages.php')?>" class="list-group-item">
            <span class="badge"><?=count_messages($user->id)?></span>
            Messages
          </a>
          <?php endif; ?>
          <a href="<?=root_path('logout.php')?>" class="list-group-item">Logout <?=$user->username?></a>
        </div><!--//.nav nav-pills nav-stacked-->
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <div class="panel">
          <!-- Default panel contents -->
          <div class="panel-heading">Welcome <strong><?=$user->username?></strong></div>
          <?php $pages = $user->getPrivatePages(); ?>
          <?php if (!$pages): ?>
            <span class="text-danger">Your account does not have access to any
              internal links. If you feel this is a mistake then please contact
              <a href="mailto:<?=system_email()?>"><?=system_email()?></a></span>
          <?php else: ?>

            <p>Below are a list of pages that your account may access.</p>

            <ul class="list-group">
            <?php if ($pages): ?>
              <?php foreach ($pages as $page): ?>
              <li class="list-group-item">
                <a href="<?=root_path($page->URL)?>"><?=$page->URL?></a>
              </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <?php endif; ?>
        </div><!--//.panel-->

      </div><!--//.col-lg-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>
</body>
</html>

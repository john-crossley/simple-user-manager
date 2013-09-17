  <div class="navbar navbar navbar-fixed-top">
    <div class="container">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="<?=system_url()?>" class="navbar-brand">
          <?=system_name()?> <br><small>Management System</small>
        </a>
        <div class="nav-collapse collapse pull-right">
          <ul class="nav main-menu">
            <?php
            if (_logged_in()):
              $user = get_user();
            ?>
              <li class="dropdown">
                <a href="<?=root_path('index.php')?>" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?=get_gravatar($user->email, 25)?>" width="25" height="25"
                    class="gravatar" alt="<?=$user->username?>'s Gravatar Picture">
                  <?=$user->username?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <?php if ($user->checkPermission('accessAdminPanel')): ?>
                    <li><a href="<?=root_path('admin/')?>"><i class="glyphicon glyphicon-home"></i> Admin Panel</a></li>
                  <?php endif; ?>
                  <li><a href="<?=root_path('member/')?>"><i class="glyphicon glyphicon-list-alt"></i> Member Panel</a></li>
                  <li><a href="<?=root_path('profile.php')?>"><i class="glyphicon glyphicon-user"></i> Profile</a></li>
                  <?php if (pm_system_enabled()): ?>
                    <?php if ($user->receive_personal_messages > 0): ?>
                      <li><a href="<?=root_path('messages.php')?>"><i class="glyphicon glyphicon-envelope"></i> Messages
                      <span class="badge badge-important"><?=count_messages($user->id)?></span></a></li>
                    <?php endif; ?>
                  <?php endif; ?>
                  <?php if ($user->checkPermission('accessSettingsPanel')): ?>
                  <li>
                    <a href="<?=root_path('admin/settings.php')?>"><i class="glyphicon glyphicon-cog"></i> Settings</a>
                  </li>
                  <?php endif; ?>
                  <li class="divider"></li>
                  <li><a href="<?=root_path('logout.php')?>">
                    <i class="glyphicon glyphicon-lock"></i> Logout <?=$user->username?></a>
                  </li>
                </ul>
              </li><!--//dropdown-->
            <?php else: ?>
              <div class="not-logged-in">
                <li><a href="<?=root_path('login.php')?>">Sign in</a></li>
                <li><a href="<?=root_path('register.php')?>">Register</a></li>
              </div><!--//.not-logged-in-->
            <?php endif; ?>
          </ul>
        </div><!--nav-collapse collapse-->
      </div><!--container-->
  </div><!--navbar navbar-inverse navbar-fixed-top-->

  <?php if ( $message = Flash::show() ): ?>
    <div class="alert alert-<?=$message['type']?>">
      <?=$message['msg']?>
    </div><!--alert-->
  <?php endif; ?>

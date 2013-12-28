    <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<?php echo system_url(); ?>" class="navbar-brand">
                    <?php echo system_name(); ?>
                </a>
            </div>

            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <?php if (_logged_in()):
                        $user = get_user();
                    ?>
                    <li class="dropdown">
                        <a href="<?php echo root_path('index.php'); ?>" class="dropdown-toggle" data-toggle="dropdown">
                          <img src="<?php echo get_gravatar($user->email, 25); ?>" width="25" height="25"
                            class="gravatar" alt="<?php echo $user->username?>'s Gravatar Picture">
                          <?php echo $user->username; ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($user->checkPermission('accessAdminPanel')): ?>
                                <li><a href="<?php echo root_path('admin/'); ?>"><i class="glyphicon glyphicon-home"></i> Admin Panel</a></li>
                            <?php endif; ?>
                                <li><a href="<?php echo root_path('member/'); ?>"><i class="glyphicon glyphicon-list-alt"></i> Member Panel</a></li>
                                <li><a href="<?php echo root_path('profile.php'); ?>"><i class="glyphicon glyphicon-user"></i> Profile</a></li>
                            <?php if (pm_system_enabled()): ?>
                                <?php if ($user->receive_personal_messages > 0): ?>
                                    <li><a href="<?php echo root_path('messages.php'); ?>"><i class="glyphicon glyphicon-envelope"></i> Messages
                                    <span class="badge badge-important"><?php echo count_messages($user->id); ?></span></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($user->checkPermission('accessSettingsPanel')): ?>
                                <li>
                                    <a href="<?php echo root_path('admin/settings.php'); ?>"><i class="glyphicon glyphicon-cog"></i> Settings</a>
                                </li>
                            <?php endif; ?>
                            <li class="divider"></li>
                            <li><a href="<?php echo root_path('logout.php'); ?>"><i class="glyphicon glyphicon-lock"></i> Logout <?php echo $user->username?></a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                        <li><a href="<?php echo root_path('login.php'); ?>">Login</a></li>
                        <li><a href="<?php echo root_path('register.php'); ?>">Register</a></
                    <?php endif; ?>
                </ul>
            </div><!--/.nav-collapse -->

        </div>
    </div>

    <?php if ($message = Flash::show()): ?>
        <div class="container">
            <div class="alert alert-<?php echo $message['type']; ?> alert-dismissable">
                <button type="button" class="close" data-dismissable="alert" aria-hidden="true">&times;</button>
                <strong><?php echo ucfirst($message['type']); ?>!</strong> <?php echo $message['msg']; ?>
            </div>
        </div>
    <?php endif; ?>
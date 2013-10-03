<?php
require_once 'loader.php';

if (isset($_GET['username'])) {
  // Fetch the user - If there is one.
  $username = strip_tags($_GET['username']);
  $user = User::findByUsername($username);
  if (!$user->id) {
    Flash::make('info', USER_PROFILE_NOT_FOUND);
    redirect('index.php', true);
  }
} else {
  Flash::make('info', USER_PROFILE_NOT_FOUND);
  redirect('index.php');
}

get_header('Viewing: ' . fullname($user) . '\'s profile');
?>
<body>

  <?=get_menu()?>

    <div class="row">
      <div class="container">

        <div class="col-lg-3 profile-block">
          <img src="<?=get_gravatar($user->email, 240)?>" width="240" height="240" class="gravatar" alt="<?=$user->username?>'s Gravatar Picture">
          <h3><?=fullname($user)?></h3>
          <h4><?=$user->username?> <?=get_role($user)?></h4>
          <ul>
            <li><i class="glyphicon glyphicon-user"></i> <?=fullname($user)?></li>
            <?php if (!$user->private): ?>
            <li><i class="glyphicon glyphicon-envelope"></i> <a href="mailto:<?=$user->email?>"><?=$user->email?></a></li>
            <li><i class="glyphicon glyphicon-map-marker"></i> <?=$user->location?></li>
            <li><i class="glyphicon glyphicon-time"></i> Joined on: <?=date(TIME_FORMAT, strtotime($user->created_at))?></li>
            <?php endif; ?>
          </ul>
        </div><!--//.col-lg-3-->

        <div class="col-lg-9 main">
          <?php if (!$user->private): ?>
          <h2><?=fullname($user)?>'s Bio</h2>
          <p><?=$user->bio?></p>
        <?php else: ?>
          <p><strong><?=fullname($user)?>'s account is private</strong></p>
        <?php endif; ?>

        <hr>

        <?php if (pm_system_enabled()): ?>
          <?php if (_logged_in()): ?>
            <?php $tmp_user = get_user(); ?>
            <?php if (($tmp_user->id == $user->id) || ($tmp_user->banned_from_sending_personal_messages > 0)): ?>
              <button type="button" class="btn btn-success" disabled="disabled">Unable to send a message</button>
            <?php else: ?>
              <?php if ($tmp_user->receive_personal_messages > 0): ?>
                <a href="#send-personal-message" data-toggle="modal" class="btn btn-success">Send Message</a>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <p><a href="<?=root_path('login.php')?>">Login</a> to send this person a message.</p>
          <?php endif; ?>
        <?php endif; ?>

        <?php if (me_logged_in($_GET['username'])): ?>
          <a href="<?=root_path('profile.php')?>" class="btn btn-danger">Edit Account</a>
        <?php endif; ?>
        </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <div class="modal fade" id="send-personal-message">
    <form method="POST" action="<?=root_path('view.php')?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Send <strong><?=$user->username?></strong> a Message</h4>
          </div>
          <div class="modal-body">
            <div class="form-group" id="subject-block">
              <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
              <input type="hidden" name="user_id" value="<?=$user->id?>">
              <label for="title" class="control-label">Subject</label>
              <input type="text" class="form-control" id="title" name="title" placeholder="I have found the meaning of life!">
            </div><!--//.form-group-->

            <div class="form-group">
              <label for="message" class="control-label">Message</label>
              <textarea class="form-control high-textarea" id="message" name="message" cols="1" rows="1" placeholder="On second thoughts... No I have not"></textarea>
              <small class="help-block"></small>
            </div><!--//.form-group-->
          </div><!--//.modal-body-->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" id="send_personal_message">Send Message</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <?=get_footer()?>

</body>
</html>

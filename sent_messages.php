<?php
require_once 'loader.php';
get_header('Sent Messages');
ensure_login();
$user = get_user();
messages_enabled($user);
?>
<body>

  <!-- Menu -->
  <?=get_menu()?>

  <div class="row">

    <div class="container main">

      <div class="col-lg-3">
        <?=get_messages_sidebar('sent-messages')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <?php if (has_sent_messages($user->id)): ?>
        <table class="table table-hover table-condensed">
          <thead>
            <tr>
              <th>Title</th>
              <th>Message</th>
              <th>Date Sent</th>
              <th>To</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach(get_sent_messages($user->id) as $message): ?>
            <tr <?=($message->read == 0) ? "class=''":''?>>
              <td><?=substr($message->title, 0, 15)?>...</td>
              <td><?=substr($message->message, 0, 35)?>...</td>
              <td><?=date(TIME_FORMAT, strtotime($message->date_sent));?></td>
              <td><?=username_from_id($message->sent_to_id)?></td>
              <td><a href="#view-message" data-task="view_sent_message" data-message="<?=$message->id?>" class="btn btn-success btn-xs">View</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <p>You have not yet sent any messages why not <a href="<?=root_path('compose_message.php')?>">compose one</a>?</p>
        <?php endif; ?>

      </div><!--//.col-lg-9-->

    </div><!--//.container-->

  </div><!--//.row-->

  <?=get_footer()?>

  <?=get_view_message_modal()?>

</body>
</html>

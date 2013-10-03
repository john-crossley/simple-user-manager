<?php
require_once 'loader.php';
get_header('Inbox');
ensure_login();
$user = get_user();
messages_enabled($user);

if (!empty($_POST) && isset($_POST['delete_message']) && !empty($_POST['delete_message'])) {

  $result = DB::table('message')->where_in('id', $_POST['delete_message'])->where('sent_to_id', '=', $user->id)->update(array(
    'show_to_receiver' => 0
  ));

  if ($result) {
    Flash::make('success', _rd('messages', count($_POST['delete_message']) > 1 ? 'messages' : 'message', SUCCESSFULLY_DELETED_A_MESSAGE));
    redirect('messages.php');
  }

}
?>
<body>

  <!-- Menu -->
  <?=get_menu()?>

  <div class="row">

    <div class="container main">

      <div class="col-lg-3">
        <?=get_messages_sidebar('messages')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <?php if (has_inbox_messages($user->id)): ?>
          <form method="POST" action="<?=root_path('messages.php')?>">
            <table class="table table-hover table-condensed">
              <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th>Title</th>
                  <th>Message</th>
                  <th>Date Received</th>
                  <th>From</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach(get_received_messages($user->id) as $message): ?>
                <tr <?=($message->read == 0) ? "class=''":''?>>
                  <td><input type="checkbox" name="delete_message[]" value="<?=$message->id?>"></td>
                  <td><span class="label label-<?=($message->read==0)?'danger':'success'?>"><?=($message->read==0)?'Unread':'Read'?></span></td>
                  <td><?=substr($message->title, 0, 15)?>...</td>
                  <td><?=substr($message->message, 0, 35)?>...</td>
                  <td><?=date(TIME_FORMAT, strtotime($message->date_sent));?></td>
                  <td><?=username_from_id($message->sent_from_id)?></td>
                  <td>
                    <a href="#view-message" data-task="view_message_inbox" data-message="<?=$message->id?>" class="btn btn-success btn-xs">View</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <div class="form-group">
              <button class="btn btn-danger btn-xs pull-right" onclick="return confirm('Sure you want to delete these messages?');">Delete Selected</button>
            </div><!--//.form-group-->
          </form>
        <?php else: ?>
          <p>Your inbox is currently empty, would you like to <a href="<?=root_path('compose_message.php')?>">compose one</coma>?</p>
        <?php endif; ?>

      </div><!--//.col-lg-9-->

    </div><!--//.container-->

  </div><!--//.row-->

  <?=get_footer()?>

  <?=get_view_message_modal()?>

</body>
</html>

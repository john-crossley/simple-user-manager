<?php
require_once 'loader.php';
get_header('Compose a new Message');
ensure_login();
$user = get_user();
messages_enabled($user);

if ((isset($_POST['task']) && $_POST['task'] == 'sendMessage') &&
    (isset($_POST['title']) && !empty($_POST['title'])) &&
    (isset($_POST['message'])) && !empty($_POST['message']) &&
    (isset($_POST['recipients_username'])) && !empty($_POST['recipients_username'])) {

  csrf_check('compose_message.php');

  // Get the current logged in user
  $user = get_user();

  // Create and setup the validator
  $v = new Validator;
  $rules = array(
    'recipients_username' => array('required'),
    'title'               => array('min:3', 'max:128'),
    'message'             => array('required', 'max:600', 'min:3')
  );
  $v->make($_POST, $rules);

  if ($v->fails()) {
    Flash::make('danger', GENERIC_FORM_ERROR_MESSAGE);
    redirect('compose_message.php');
  }


  $recipient = User::findByUsername($_POST['recipients_username']);

  if (!$recipient->id) {
    Flash::make('danger', UNABLE_TO_LOCATE_USER);
    redirect('compose_message.php');
  }

  if ($recipient->username === $user->username) {
    Flash::make('danger', 'You cannot send yourself a personal message');
    redirect('compose_message.php');
  }

  if ((int)$recipient->receive_personal_messages < 1) {
    // User has personal messages disabled!
    Flash::make('danger', USER_HAS_PERSONAL_MESSAGES_DISABLED);
    redirect('compose_message.php');
  }

  $title   = strip_tags($_POST['title']);
  $personalMessage = strip_tags($_POST['message']);

  // The data to be inserted
  $data = array(
    'title'        => $title,
    'message'      => $personalMessage,
    'date_sent'    => date(DATABASE_DATETIME_FORMAT),
    'sent_from_id' => $user->id,
    'sent_to_id'   => $recipient->id
  );

  $message = new Message;

  if ($message->insertMessageIntoUsersInbox($recipient->id, $data)) {

    if ((int)$recipient->notify_me_personal_message > 0) {
      // Ok email the person
      // Prep the template. Start by grabbing the 'New Personal Message' one.
      $template = DB::table('template')->where('id', '=', 2)->grab(1)->get();

      if ($template) {

        $text = mini_parse($template->data, array(
          'username' => $user->username,
          'sender'   => $recipient->username,
          'title'    => $title,
          'message'  => $personalMessage
        ));

        $e = new Email;
        $e->to($recipient->email, fullname($recipient))
          ->from($user->email, fullname($user))
          ->subject($template->subject)
          ->template(TEMPLATE . 'generic_email_template.html', array(
              'template' => nl2br($text),
              'system_name' => system_name(),
              'year'        => date('Y'),
              'url'         => URL
            ))
          ->send();
      }
    }

    // Right now redirect the user
    Flash::make('success', SUCCESSFULLY_SENT_A_MESSAGE);
    redirect('compose_message.php');
  }

}

?>
<body>

  <!-- Menu -->
  <?=get_menu()?>

  <div class="row">

    <div class="container main">

      <div class="col-lg-3">
        <?=get_messages_sidebar('compose-message')?>
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">
        <h2>Compose a Message</h2>
        <?php if ($user->banned_from_sending_personal_messages > 0): ?>
          <p class="text-danger">Your account has been banned from sending
            personal messages. If you feel this is a mistake then please
            contact the system administrator <a href="mailto:<?=system_email()?>"><?=system_email()?></a></p>
        <?php else: ?>
          <form method="POST" action="<?=root_path('compose_message.php')?>">

            <div class="form-group has-<?=form_has_error('recipients_username')?>">
              <input type="hidden" name="task" value="sendMessage">
              <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
              <input type="hidden" name="user_id" value="<?=$user->id?>">
              <label for="recipients_username" class="control-label">Recipients username</label>
              <input type="text" class="form-control" id="recipients_username" name="recipients_username" placeholder="john.doe">
              <small class="help-block"><?=form_has_message('recipients_username')?></small>
            </div><!--//.form-group-->

            <div class="form-group has-<?=form_has_error('title')?>">
              <label for="title" class="control-label">Subject</label>
              <input type="text" class="form-control" id="title" name="title" placeholder="I have found the meaning of life!">
              <small class="help-block"><?=form_has_message('title')?></small>
            </div><!--//.form-group-->

            <div class="form-group has-<?=form_has_error('message')?>">
              <label for="message" class="control-label">Message</label>
              <textarea class="form-control high-textarea" id="message" name="message" cols="1" rows="1" placeholder="On second thoughts... No I have not"></textarea>
              <small class="help-block"><?=form_has_message('message')?></small>
            </div><!--//.form-group-->

            <div class="form-group">
              <button class="btn btn-success">Send Message</button>
            </div><!--//.form-group-->
          </form>
        <?php endif; ?>
      </div><!--//.col-lg-9-->

    </div><!--//.container-->

  </div><!--//.row-->

  <?=get_footer()?>

</body>
</html>

<?php
require_once 'bootstrap.php';
/**
 * process.php
 *
 * The process handles all of the POST input
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package advanced-user-manager
 * @version 1.0
 */

if (isset($_POST['task']) && $_POST['task'] == 'how_to_protect_user_group'
  && isset($_POST['role_id']) && !empty($_POST['role_id'])) {

  $role = Role::getRoleNameFromRoleId((int)$_POST['role_id']);
  if (!$role) die(json_encode(array('error' => true, 'message' => 'Unable to locate role')));

  $lockdown = new LockdownBuilder($_POST['path'], ROOT . 'member');
  if ($lockdown->path_not_found()) {
    die(json_encode(array('error' => true, 'message' => PATH_NOT_FOUND)));
  }

  $path = get_rel_path(ROOT . strip_tags($_POST['path']), ROOT . 'bootstrap.php');

  die(json_encode(array(
    'error' => false,
    'message' => $path,
    'role_name' => $role
  )));

  exit;
}

if (isset($_POST['task']) && $_POST['task'] == 'calculateRelPath' &&
  isset($_POST['id']) && !empty($_POST['id'])) {

  $id = (int)$_POST['id'];
  $result = DB::table('private_pages')
            ->where('id', '=', $id)
            ->limit(1)
            ->get();

  if (!$result) die(json_encode(array('error' => true)));

  $path_to_bootstrap = get_rel_path(ROOT . $result->URL, ROOT . 'bootstrap.php');

  die(json_encode(array(
    'error'   => false,
    'snippet' => $path_to_bootstrap
  )));
  exit;

}

if (isset($_POST['task']) && $_POST['task'] == 'removeAccessToPage'
  && isset($_POST['id']) && !empty($_POST['id'])) {

  $id = (int)$_POST['id'];

  $result = DB::table('private_pages')->where('id', '=', $id)->delete();

  if ($result)
    die(json_encode(array('error' => false)));

  exit;

}

// Called when the user sends a message from the profile page
if (isset($_POST['task']) && $_POST['task'] == 'sendPersonalMessage'
  && isset($_POST['user_id']) && !empty($_POST['user_id'])
  && isset($_POST['title']) && !empty($_POST['title'])
  && isset($_POST['message']) && !empty($_POST['message']) ) {

  if (!_csrf()) {
    die(json_encode(array(
      'fatal_error'   => true,
      'message' => CSRF_CHECK_FAILURE
    )));
  }

  // Get the current loggd in user
  $user = get_user();

  // Create and setup the validator
  $v = new Validator;
  $rules = array(
    'title'            => array('min:3', 'max:128'),
    'message'          => array('min:10', 'max:600')
  );
  $v->make($_POST, $rules);

  if ($v->fails()) {
    die(json_encode(array(
      'error' => true,
      'title' => $v->hasMessage('title'),
      'message' => $v->hasMessage('message')
    )));
  }

  $recipient = User::findById($_POST['user_id']);

  if (!$recipient) {
    die(json_encode(array(
      'fatal_error'   => true,
      'message' => UNABLE_TO_LOCATE_USER
    )));
  }

  if ($recipient->receive_personal_messages == 0) {
    // User has personal messages disabled
    die(json_encode(array(
      'fatal_error'   => true,
      'message' => USER_HAS_PERSONAL_MESSAGES_DISABLED
    )));
  }

  if ($recipient->email === $user->email) {
    die(json_encode(array(
      'fatal_error'   => true,
      'message' => CANNOT_SEND_YOURSELF_A_PERSONAL_MESSAGE
    )));

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

    // Does the user want to be notified?
    if ($recipient->notify_me_personal_message) {
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

    die(json_encode(array(
      'error' => false,
      'message' => SUCCESSFULLY_SENT_A_MESSAGE
    )));
  }


}

if (isset($_POST['task']) && $_POST['task'] == 'findTemplateData'
  && isset($_POST['id']) && !empty($_POST['id'])) {

  $result = DB::table('template')
            ->where('id', '=', (int)$_POST['id'])
            ->grab(1)
            ->get();

  if (!$result) {
    echo json_encode(array('error' => true, 'data' => 'Unable to load template data.'));
    exit;
  }


  $result->fields = unserialize($result->fields);

  $tmp = "";
  $tmp .= "<h5>Placeholders</h5>";
  $tmp .= "<p>Below are the placeholders that may be used in the adjacent
  text area to help construct a more personalised email.</p>";
  foreach ($result->fields as $field) {
    $tmp .= "<span class='label label-danger label-block placeholder'>{{{$field}}}</span><br>";
  }

  $result->fields = $tmp;

  echo json_encode($result);

  exit;
}

if (isset($_POST['task']) && $_POST['task'] == 'deleteUserMessagesFromUserPanel'
  && isset($_POST['delete_message']) && !empty($_POST['delete_message'])) {

  if (!_csrf()) {
    Flash::make('error', CSRF_CHECK_FAILURE);
    redirect('admin/messages.php');
  }

  if (!_logged_in()) {
    Flash::make('error', USER_LOGGED_IN);
    redirect('login.php');
  }

  $user = get_user(); // We have a user logged in

  $message = new Message();
  if ($message->deleteMessages($_POST['delete_message'], $user->id)) {
    Flash::make('success', _rd('messages', (count($_POST['delete_message']) > 1)
      ? 'messages' : 'message', SUCCESSFULLY_DELETED_A_MESSAGE));
    redirect('admin/messages.php');
  }

  exit;
}

if (isset($_POST['task']) && $_POST['task'] == 'getMessageData'
    && isset($_POST['message_id']) && !empty($_POST['message_id'])) {

  $messageId = (int)$_POST['message_id'];

  $message = DB::table('message AS m')
                  ->left_join('user AS u', 'm.sent_from_id', '=', 'u.id')
                  ->where('m.id', '=', $messageId)
                  ->grab(1)
                  ->get(array('m.title', 'u.email'));

  if (!$message) {
    die('error');
  }

  echo json_encode(['title' => 'Re: '. $message->title, 'email' => $message->email]);
  exit;
}

if (isset($_POST['task']) && !empty($_POST['task'])
  && isset($_POST['message_id']) && !empty($_POST['message_id']) ) {

  $user = get_user(); // Ensure we have a user logged in

  if (!$user) die(json_encode(array('message' => 'Unable to load message')));

  $message_id = (int)$_POST['message_id'];

  if ($_POST['task'] === 'view_message_inbox') {
    $message = DB::table('message AS m')
              ->left_join('user AS u', 'm.sent_to_id', '=', 'u.id')
              ->where('m.id', '=', $message_id)
              ->where('u.id', '=', $user->id)
              ->grab(1)
              ->get(array('m.*', 'u.username', 'u.firstname', 'u.lastname'));
    if ((int)$message->read === 0) {
      DB::table('message')
        ->where('id', '=', $message_id)
        ->update(array(
            'read'      => true,
            'date_read' => date(DATABASE_DATETIME_FORMAT)
          ));
    }
  } else if ($_POST['task'] === 'view_sent_message') {
    $message = DB::table('message AS m')
              ->left_join('user AS u', 'm.sent_from_id', '=', 'u.id')
              ->where('m.id', '=', $message_id)
              ->grab(1)
              ->get(array('m.*', 'u.username', 'u.firstname', 'u.lastname'));
  }

  $message->time_sent = date(TIME_FORMAT, strtotime($message->date_sent));
  $message->message = nl2br($message->message);

  die(json_encode($message));
}

// Called when a member tries to send another member a message
if ((isset($_POST['task']) && $_POST['task'] === 'sendMessage') &&
    (isset($_POST['title']) && !empty($_POST['title'])) &&
    (isset($_POST['message'])) && !empty($_POST['message']) &&
    (isset($_POST['recipients_email'])) && !empty($_POST['recipients_email'])) {

  // Check CSRF Token
  if (!_csrf()) {
    Flash::make('error', CSRF_CHECK_FAILURE);
    redirect('admin/messages.php');
    exit;
  }

  // Get the current logged in user
  $user = get_user();

  // Create and setup the validator
  $v = new Validator();
  $rules = array(
    'recipients_email' => array('required', 'valid_email'),
    'title'            => array('min:3', 'max:128'),
    'message'          => array('required', 'max:600')
  );
  $v->make($_POST, $rules);

  if ($v->fails()) {
    Flash::make('error', GENERIC_FORM_ERROR_MESSAGE);
    redirect('admin/messages.php');
  }

  $recipient = User::findByEmail($_POST['recipients_email']);

  if (!$recipient) {
    Flash::make('error', UNABLE_TO_LOCATE_USER);
    redirect('admin/messages.php');
  }

  if ($recipient->email === $user->email) {
    Flash::make('error', 'You cannot send yourself a personal message');
    redirect('admin/messages.php');
    exit;
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

  $message = new Message();

  if ($message->insertMessageIntoUsersInbox($recipient->id, $data)) {

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

    // Right now redirect the user
    Flash::make('success', SUCCESSFULLY_SENT_A_MESSAGE);
    redirect('admin/messages.php');
  }

}

// Triggered when a user tries to request a template to edit (From admin panel)
if (isset($_POST['task']) && $_POST['task'] == 'getTemplate') {
  echo file_get_contents(TEMPLATE . $_POST['template']);
}


// Triggered when the user saves some details from the admin panel
if (isset($_POST['task']) && $_POST['task'] == 'saveUserFromAdminPanel'
  && isset($_POST['save']) && isset($_POST['user_id'])) {

  // Ensure that this request is from the allowed destination
  if (!_csrf()) {
    Flash::make('error', CSRF_CHECK_FAILURE);
    redirect('admin/');
  }

  // Assume no data has been changed.
    $dataChanged = false;
    $emailUser   = false;

    $v = new Validator(); // CREATE A NEW VALIDATOOOORRR
    $rules = array(); // Create an empty rule set

    // Grab the user
    $user = User::findById((int)$_POST['user_id']);

    if (!$user) {
      // Nothing has been found so redirect the user
      Flash::make('error', UNABLE_TO_LOCATE_USER);
      redirect('admin/');
    }

    // Should we notify the user on any change?
    if (isset($_POST['email_user']) && $_POST['email_user'] == 'on')
      $emailUser = true;

    if (isset($_POST['username']) && !empty($_POST['username'])) {
      $username = strip_tags($_POST['username']);
      if ($username != $user->username) {
        $dataChanged = true; // Yes the data has changed.
        $rules['username'] = array('min:3', 'max:128', 'unique:user');
        $user->username = $username;
      }
    }

    // Account private?
    if (isset($_POST['account_private']) && $_POST['account_private'] == 'on') {
      // Do we need to even change it?
      $user->private = true;
      $dataChanged = true;
    } else {
      // It's off
      $user->private = false;
      $dataChanged = true;
    }

    if (isset($_POST['fullname']) && !empty($_POST['fullname'])) {
      $names = explode(' ', $_POST['fullname']);
      $firstname = $names[0];
      $lastname  = (!empty($names[1])) ? $names[1] : '';
      if ($user->firstname != $firstname || $user->lastname != $lastname) {
        $dataChanged = true;
        $user->firstname = $firstname;
        $user->lastname  = $lastname;
      }
    }

    if (isset($_POST['email']) && !empty($_POST['email'])) {
      $email = strip_tags($_POST['email']);
      if ($user->email != $email) {
        $dataChanged = true;
        $rules['email'] = array('required', 'valid_email');
        $user->email = $email;
      }
    }

    if (isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['password_again']) && !empty($_POST['password_again'])) {

      $dataChanged = true;

      $password = $_POST['password'];
      // Right so the password changed
      $rules['password']       = array('min:6');
      $rules['password_again'] = array('match:password');

      $user->password = $password;
      $passwordChange = $password;

    } else $passwordChange = 'No Change';

    if (isset($_POST['redirect_to']) && !empty($_POST['redirect_to'])) {
      if ($user->redirect_to != $_POST['redirect_to']) {
        $dataChanged = true;
        $user->redirect_to = $_POST['redirect_to'];
      }
    }

    // Users BIO
    if (isset($_POST['bio']) && !empty($_POST['bio'])) {
      if ($user->bio != $_POST['bio']) {
        $dataChanged = true;
        $user->bio = strip_tags($_POST['bio']); // Strip the tags
      }
    }

    // Users location
    if (isset($_POST['current_location']) && !empty($_POST['current_location'])) {
      if ($user->location != $_POST['current_location']) {
        $dataChanged = true;
        $user->location = strip_tags($_POST['current_location']);
      }
    }

    if (isset($_POST['roleId']) && (int)$_POST['roleId'] > 0) {

      $current_user_group = get_role_raw($user);
      $roleId             = (int)$_POST['roleId'];

      // What role name has been selected?
      $selected_group = Role::getRoleNameFromRoleId($roleId);

      // Does this user even have a user group?
      if (!$current_user_group) {
        // User doesn't even have a group
        Role::insertUserRole($user->id, $roleId);
        $dataChanged = true;
      }


      if ($selected_group != $current_user_group) {
        // Change!
        Role::updateUserRole($user->id, $roleId);https://beta.manning.com/dashboard/
        $dataChanged = true;
      }
    }

    if (isset($_POST['account_verification_status'])) {
      $status = (int)$_POST['account_verification_status'];

      if ($status != (int)$user->verified) {
        $dataChanged = true;
        $user->verified = $status;
        $status_change_message = "<p><strong>Your account has now been activated.</strong></p>";
      } else $status_change_message = '';
    }

    $v->make($_POST, $rules);

    if ($v->fails()) {
      Flash::make('error', GENERIC_FORM_ERROR_MESSAGE);
      redirect('admin/view.php?user='.$user->id);
    }

    if ($dataChanged) {
      if ($user->save()) {
        if ($emailUser) {

          // a:6:{i:0;s:8:"username";i:1;s:8:"fullname";i:2;s:10:"user_email";i:3;s:8:"password";i:4;s:10:"user_group";i:5;s:21:"status_change_message";}

          $template = DB::table('template')->where('id', '=', 6)->grab(1)->get();
          if ($template) {

            $text = mini_parse($template->data, array(
              'username'              => $user->username,
              'fullname'              => fullname($user),
              'user_email'            => $user->email,
              'password'              => $passwordChange,
              'status_change_message' => $status_change_message,
              'user_group'            => $current_user_group,
              'account_private'       => ($user->private) ? 'Private Account' : 'Public Account',
              'bio'                   => $user->bio,
              'location'              => $user->location
            ));

            $e = new Email;
            $e->to($user->email, fullname($user))
              ->from(system_email(), meta_author())
              ->subject($template->subject)
              ->template(TEMPLATE . 'generic_email_template.html', array(
                  'template'    => nl2br($text),
                  'system_name' => system_name(),
                  'url'         => URL,
                  'year'        => date('Y'),
                ))
              ->send();
          } // template

        } // Email user.
      }
      Flash::make('success', 'Success, ' . $user->username . '\'s account has been updated.');
      redirect('admin/view.php?user='.$user->id);
    }

}

/**
 * AJAX REQUEST
 *
 * Triggered when the user forgets their password.
 */
if (isset($_POST['email']) && !empty($_POST['email']) &&
    isset($_POST['csrf']) && !empty($_POST['csrf']) &&
    isset($_POST['task']) && $_POST['task'] == 'forgotPassword') {

  if (!_csrf()) {
    die(json_encode(array('error' => true, 'message' => CSRF_CHECK_FAILURE)));
  }

  // Clean the email (QUIIICK)
  $email = strip_tags($_POST['email']);
  // Find the user
  $user = User::findByEmail($email);

  // Does the user exist?
  if (!$user->id) {
    echo json_encode(array(
      'error' => true,
      'message' => _rd('email', $email, ACCOUNT_NOT_FOUND_BY_EMAIL)
    ));
    exit;
  }

  $hash = md5(uniqid().rand());
  $user->hash = $hash;
  $id = $user->id;

  // Build the reset URL
  $newPasswordUrl = URL . 'confirm.php?hash=' . $hash . '&amp;id=' . $id . '&amp;do=new_password';

  // Save the user
  if ($user->save()) {

    $template = DB::table('template')->where('id', '=', 1)->grab(1)->get();
    // a:2:{i:0;s:8:"username";i:1;s:16:"new_password_url";}
    if ($template) {

      $text = mini_parse($template->data, array(
        'username'          => $user->username,
        'new_password_url'  => $newPasswordUrl
      ));

      $e = new Email;
      $e->to($user->email, fullname($user))
        ->from(system_email(), meta_author())
        ->subject($template->subject)
        ->template(TEMPLATE . 'generic_email_template.html', array(
          'template'    => nl2br($text),
          'system_name' => system_name(),
          'year'        => date('Y'),
          'url'         => URL
        ))
        ->send();

      echo json_encode(array(
        'error' => false,
        'message' => _rd('username', $user->username, FORGOT_EMAIL_SENT)
      ));
      exit;
    }
  }

  // Something went wrong, basically this shits hit the fan.
  echo json_encode(array(
    'error' => true,
    'message' => GENERIC_FORM_ERROR_MESSAGE
  ));

  exit;
}

/**
 *
 * Triggered when a new user registers using the /register.php page
 *
 */

// Triggered when the user registers.
if (isset($_POST['username']) && isset($_POST['email']) &&
    isset($_POST['password']) && isset($_POST['password_again']) &&
    isset($_POST['captcha']) && isset($_POST['task']) && $_POST['task'] == 'register') {

  if (!_csrf()) {
    Flash::make('error', CSRF_CHECK_FAILURE);
    redirect('register.php');
  }

  // LET THE VALIDATION PROCESS BEGIN!
  $v = new Validator;

  // Out validation rules
  $rules = array(
    'username'       => array('required', 'unique:user', 'min:2', 'max:52'),
    'password'       => array('required', 'min:6'),
    'password_again' => array('required', 'match:password'),
    'email'          => array('required', 'valid_email', 'banned:' . get_banned_email_extensions(), 'unique:user')
  );

  // Custom validation messages
  $messages = array(
    'password_again.required' => 'The password again field is required.',
    'captcha.match'           => 'Incorect sum entered, please try again',
    'email.banned'            => 'Banned email extension, please supply another'
  );

  // Make the validation
  $v->make($_POST, $rules, $messages);

  if ($_SESSION['CAPTCHA']['ANSWER'] !== $_POST['captcha']) {
    Flash::make('error', CAPTCHA_FAILED);
    redirect('register.php');
  }

  if ($v->fails()) {
    Flash::make('error', GENERIC_FORM_ERROR_MESSAGE);
    redirect('register.php');
  }

  $username = strip_tags($_POST['username']);
  $password = strip_tags($_POST['password']);
  $email    = strip_tags($_POST['email']);

  // Ok we can only assume all went well
  $hash = md5(uniqid().rand());
  $user = new User;
  $user->username = $username;
  $user->password = $password;
  $user->email    = $email;
  $user->hash     = $hash;
  $user->verified = 0;

  if ($id = $user->save()) {
    // It's all lies, they're not even square
    $validateURL = URL . 'confirm.php?hash=' . $hash . '&amp;id=' . $id . '&amp;do=register';

    // Get the 'Welcome Email' template ready for parsing
    $template = DB::table('template')->where('id', '=', 7)->grab(1)->get();

    if ($template) {
      // We can continue with the sending of the email.

      $text = mini_parse($template->data, array(
        'username'     => $username,
        'validate_url' => $validateURL,
        'system_name'  => system_name(),
        'url'          => URL,
        'year'         => date('Y')
      ));

      $e = new Email;
      $e->to($email)
        ->from(system_email(), meta_author())
        ->subject($template->subject)
        ->template(TEMPLATE . 'generic_email_template.html', array(
            'template'    => nl2br($text),
            'system_name' => system_name(),
            'year'        => date('Y'),
            'url'         => URL
          ))
        ->send();

    }

    Flash::make('success', _rd('username', $username, NEW_USER_REGISTERED));
    redirect('register.php');

  } else {
    Flash::make('error', ERROR_OCCURRED_WHILE_PROCESSING_FORM);
    redirect('register.php');
  }

} // register



// Nothing found...
Flash::make('info', 'Unable to complete your resquest. Please try again.');
redirect('index.php');

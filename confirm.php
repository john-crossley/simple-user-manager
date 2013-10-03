<?php
require_once 'loader.php';

// Check to see if the values are set and not empty
if (isset($_GET['hash']) && isset($_GET['id']) && isset($_GET['do'])
  && !empty($_GET['hash']) && !empty($_GET['id']) && !empty($_GET['do'])) {

  // Dirty clean
  $hash = strip_tags($_GET['hash']);
  $id = (int)$_GET['id'];
  $do = strip_tags($_GET['do']);

  // Check to see if the values match the user.
  $user = User::findById($id);

  if ($user === false) {
    redirect('login.php');
    exit;
  }

  // Right now check to see if the user matches up.

  if ($user->hash === $hash) {

    $system_name = system_name();
    $system_email = system_email();

    // 1. What task needs to be performed?
    switch (strtolower($do)) {
      case 'register':
        // 1.1 Reset their hash
        $user->hash = null;
        // 1.2 Set their account to verified
        $user->verified = true;
        if ($user->save()) {

          $template = DB::table('template')->where('id', '=', 5)->grab(1)->get();

          if ($template) {
            // Yeah mother f**ker!
            // a:2:{i:0;s:8:"username";i:1;s:9:"login_url";}
            $text = mini_parse($template->data, array(
              'username'  => $user->username,
              'login_url' => URL . 'login.php'
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
          }
          Flash::make('success', _rd('username', $user->username, ACCOUNT_VERIFIED));
          redirect('login.php');
        }
        break;
      case 'new_password':

        // Generate a new password.
        $password = random_password();

        $user->hash = null; // NULL their hash man
        $user->password = $password;

        // 42a51d9eaa20ff2a163f6f11380d725c

        if ($user->save()) {
          // a:2:{i:0;s:8:"username";i:1;s:8:"password";}
          $template = DB::table('template')->where('id', '=', 3)->grab(1)->get();

          if ($template) {
            $text = mini_parse($template->data, array(
              'username' => $user->username,
              'password' => $password
            ));

            $e = new Email;
            $e->to($user->email, fullname($user))
              ->from(system_email(), system_name())
              ->subject($template->subject)
              ->template(TEMPLATE . 'generic_email_template.html', array(
                  'template'    => nl2br($text),
                  'system_name' => system_name(),
                  'url'         => URL,
                  'year'        => date('Y'),
                ))
              ->send();

            Flash::make('success', _rd('username', $user->username, NEW_PASSWORD_GENERATED));
            redirect('login.php');
          }
        }
        break;

      default:
        die('Fuck knows what you want matey!');
        break;
    }


  } else {
    Flash::make('info', INCORRECT_VERIFICATION_DETAILS);
    redirect('login.php');
  }
} else {
  Flash::make('info', 'Oops wondering about gets you lost!');
  redirect('login.php');
}

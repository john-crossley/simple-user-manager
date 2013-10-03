<?php
require_once 'loader.php';

// Is this user logged in?
if (_logged_in()) {

  $user = get_user();

  if ($user) {
    // Do this...
    $user = User::findById($user->id);


    DB::table('remember_me')
        ->where('user_id', '=', $user->id)
        ->delete();

    setcookie('unid', '', time()-3600); // Expire the cookie man.

    $user->last_login = date('Y-m-d H:i:s');
    $user->save();


    if ($user->logout()) {
      Flash::make('info', _rd('username', $user->username, LOGGED_OUT));
      redirect('login.php');
      exit;
    }

  }

}

redirect('index.php');

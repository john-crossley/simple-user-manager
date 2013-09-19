<?php if (!defined('ACCESS')) die('No direct script access allowed!');

//                                 _/|__
//             _,-------,        _/ -|  \_     /~>.
//          _-~ __--~~/\ |      (  \   /  )   | / |
//       _-~__--    //   \\      \ 0   0 /   / | ||
//    _-~_--       //     ||      \     /   | /  /|
//   ~ ~~~~-_     //       \\     |( " )|  / | || /
//           \   //         ||    | VVV | | /  ///
//     |\     | //           \\ _/      |/ | ./ |
//     | |    |// __         _-~         \// |  /
//    /  /   //_-~  ~~--_ _-~  /          |\// /
//   |  |   /-~        _-~    (     /   |/ / /
//  /   /           _-~  __    |   |____|/
// |   |__         / _-~  ~-_  (_______  `\
// |      ~~--__--~ /  _     \        __\)))
//  \               _-~       |     ./  \
//   ~~--__        /         /    _/     |
//         ~~--___/       _-_____/      /
//          _____/     _-_____/      _-~
//       /^<  ___       -____         -____
//          ~~   ~~--__      ``\--__       ``\
//                     ~~--\)\)\)   ~~--\)\)\)


//
// ADD YOUR DATABASE CONNECTION INFORMATION BELOW
//
DB::connect(array(
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'advanced_user_manager'
));

/**
 * Set the default timezone of your location.
 */
date_default_timezone_set('Europe/London');

/**
 * Set the format you would like displayed to the users.
 *
 * For more information on this visit:
 * http://php.net/manual/en/function.date.php
 *
 * Current one looks like: 19th May, 2013 12:43pm
 */
define('TIME_FORMAT', 'dS M, Y h:ia');
define('DATABASE_DATETIME_FORMAT', 'Y-m-d H:i:s');

define('DEMO_MODE', false);

define('CHECK_AND_RUN_INSTALL', false);

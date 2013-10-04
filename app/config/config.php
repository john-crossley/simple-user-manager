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
  'database' => 'simple_user_manager'
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

/**
 * The default database time format.
 */
define('DATABASE_DATETIME_FORMAT', 'Y-m-d H:i:s');

/**
 * To put the system into demo mode and limited usage
 * then change this value to true.
 */
define('DEMO_MODE', false);

/**
 * If you want the system to run the installation
 * then set this to true.
 */
define('CHECK_AND_RUN_INSTALL', false);

/**
 * Do not change this, because you could break the
 * application. This is just the product number assigned
 * to AUM when I submitted it to Codecanyon.
 */
define('PRODUCT_NUMBER', 5366263);

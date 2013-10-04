<?php if (!defined('ACCESS')) die('No direct script access allowed!');

function getElapsedTime($eventTime)
{
  $totaldelay = time() - strtotime($eventTime);
  if($totaldelay <= 0) {
    return '';
  } else {
    if($days = floor($totaldelay/86400)) {
      $totaldelay = $totaldelay % 86400;
      return $days.' days ago.';
    }
    if($hours = floor($totaldelay/3600)) {
      $totaldelay = $totaldelay % 3600;
      return $hours.' hours ago.';
    }
    if($minutes = floor($totaldelay/60)) {
      $totaldelay = $totaldelay % 60;
      return $minutes.' minutes ago.';
    }
    if($seconds = floor($totaldelay/1)) {
      $totaldelay = $totaldelay % 1;
      return $seconds.' seconds ago.';
    }
  }
}

function get_news_from_phpcodemonkey() {

  // Todo Some form of caching...?
  $data = Core::getInstance()->getNewsFromPhpCodemonkey();
  if (!empty($data))
    return $data;
  else
    return false;

}

/**
 * functions.php
 *
 * This file provides a set of useful pre-built functions.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package user-manager
 */

function in_array_r($needle, $haystack) {

  if (!is_array($haystack)) return false;

  foreach ($haystack as $h) {
    if (in_array($needle, (array)$h)) return true;
  }
  return false;
}

/**
 * Gets the current users received messages.
 * @return array
 */
function get_received_messages($id) {
  $message = new Message;
  return $message->getMessagesSentToId($id);
}

function get_sent_messages($id) {
  $message = new Message;
  return $message->getMessagesSentById($id);
}

function has_inbox_messages($id) {
  $message = new Message;
  return $message->hasInboxMessages($id);
}
function has_sent_messages($id) {
  $message = new Message;
  return $message->hasSentMessages($id);
}

function count_messages($id) {
  $message = new Message;
  return $message->countMessages($id);
}

function restrict_access()
{

  $groups = array();
  $groups = func_get_args();

  // Make sure the user is logged in!
  ensure_login();

  // Right now check to see if this user has access to this page
  $user = get_user();

  // Is admin so allow access.
  if ($user->checkPermission('accessAdminPanel')) return true;

  if (!empty($groups)) {
    // Loop through the groups checking if the user
    // belongs to any of them, if they do grant them access.
    foreach ($groups as $group) {
      if ($user->_roleName === $group) return true;
    }
  }

  // Does this user have access to this page?
  $current = split_file_path($_SERVER['SCRIPT_FILENAME']);

  // Check to see if this user has access to this page
  $data = DB::table('private_pages')
              ->where('user_id', '=', $user->id)
              ->where('URL', '=', "'$current'")
              ->limit(1)
              ->get();

  if (!$data) {
    // User does not have access to this page.
    Flash::make('danger', UNABLE_TO_ACCESS_THIS_AREA);
    redirect($user->redirect_to);
  }

}

// Splits the path
function split_file_path($file, $delimiter = 'member') {
  $position = strpos($file, $delimiter);
  return $delimiter . substr($file, $position+strlen($delimiter), strlen($file)-1);
}

function messages_enabled(User $user) {
  if ($user->receive_personal_messages == 0 || !pm_system_enabled()) {
    Flash::make('notice', MESSAGE_FEATURE_HAS_BEEN_DISABLED);
    redirect('profile.php');
  }
}

function csrf_check($url = 'member/') {
  if (!_csrf()) {
    Flash::make('danger', CSRF_CHECK_FAILURE);
    redirect($url);
  }
}

/**
 * Check to see if the user is logged in
 * @return NULL
 */
function ensure_login() {
  if (!_logged_in()) {
    Flash::make('error', MUST_BE_LOGGED_IN);
    redirect('login.php');
  }
}

/**
 * Checks to see if the user has access to various sections
 * of the application.
 * @param  string $permission The permission to check
 * @param  array  $options    Any options you wish to specify EG: array('error' => '?', ..
 * refer to documentation for options.
 * @return null
 */
function check_user_access(User $user, $permission, array $options = array()) {
  // For now.
  if (!isset($options['error'])) $options['error'] = 'error';
  if (!isset($options['message'])) $options['message'] = UNABLE_TO_ACCESS_THIS_AREA;
  if (!isset($options['redirect'])) $options['redirect'] = 'member/';

  if (!$user->checkPermission($permission)) {
    Flash::make($options['error'], $options['message']);
    redirect($options['redirect']);
    exit;
  }
}

/**
 * Gets the list of templates from the database.
 * @return
 */
function get_template_list() {
  return DB::table('template')->get();
}

function get_view_message_modal() {
  require ROOT . '_partials/view_message.php';
}

function username_from_id($id) {
  $username = DB::table('user')
              ->where('id', '=', $id)
              ->grab(1)
              ->get(array('username'));
  if ($username)
    return ucfirst($username->username);

  return 'Unknown';
}

function array_flatten($arr) {
  $arr = array_values($arr);
  while (list($k, $v) = each($arr)) {
    if (is_array($v)) {
      array_splice($arr, $k, 1, $v);
      next($arr);
    }
  }
  return $arr;
}

function pp($data) {
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}

/**
 * Used to check if a checkbox should be checked or not.
 * @param  boolean  $boolean The current checked status
 */
function is_checked($boolean) {
  if (!!$boolean === true) return 'checked';
}

/**
 * Checks to see if the current form has an error.
 * @param  string $attribute The name of the attribute to check
 */
function form_has_error($attribute) {
  return Validator::hasErrorSession($attribute);
}

/**
 * Checks to see if the current input has a value.
 * @param  string $attribute The name of the attribute to check
 */
function form_has_value($attribute, $default = '') {
  $value = Validator::hasValueSession($attribute);
  if (empty($value))
    return $default = '';
  return $value;
}

/**
 * Checks to see if the current input has a message associated with it.
 * @param  string $attribute The attribute to check.
 * @param  string $default   The default attribute.
 */
function form_has_message($attribute, $default = '') {
  $message = Validator::hasMessageSession($attribute);
  if (empty($message))
    return $default;

  return $message;
}

/**
 * Get a list of user groups from the database.
 * @return [type] [description]
 */
function get_user_groups() {
  return DB::table('role')->get();
}

function get_role(User $user, $raw = false) {
  $role = $user->getCurrentUserRole($user->id);

  if ($raw) return $user->_roleName;

  if ($role) {
    $class = ($user->checkPermission('bannedMember')) ? 'danger' : 'success';
    return "<span class='label label-$class'>$user->_roleName</span>";
  }
  return false;
}

function get_role_raw(User $user) {
  return get_role($user, true);
}

function is_verified($value, $verified) {
  $verified = (int)$verified;
  if ($verified === $value)
    return 'selected';
}

function get_user() {
  if (isset($_SESSION['USER']))
    return unserialize($_SESSION['USER']);

  return false;
}

function save_user(User $user) {
  // Todo: Clean this up we are duplicating functionality here
  // in the user::auth method.
  $user->getCurrentUserRole($user->id);
  $_SESSION['USER'] = serialize($user);
}

/**
 * Simple ole redirect function, redirects the browser
 * to some other location in the site.
 * @param  string  $page      The page to redirect to. IE: index.php, login.php
 * @param  boolean $permanent If this is a permanent redirect the pass true.
 * @return null
 */
function redirect($page = 'index.php', $permanent = false) {
  if ($permanent) {
    header('HTTP/1.1 301 Moved Permanently');
  }
  header('Location: ' . URL . $page);
  exit;
}

function get_captcha() {
  $sum1 = mt_rand(1, 9);
  $sum2 = mt_rand(1, 9);
  $_SESSION['CAPTCHA']['ANSWER'] = $sum1+$sum2;
  return $sum1 . ' + ' . $sum2;
}

function status($status, $actual) {
  if ($status == $actual) {
    return 'selected';
  }
  return false;
}

/**
 * Grabs all of the system settings.
 * @return object The settings
 */
function get_settings() {
  return Settings::getAllSettings();
}

/**
 * Creates the users fullname
 */
function fullname(User $user, $brackets = false) {
  $name = $user->username;
  if ($user->firstname || $user->lastname)
    $name = $user->firstname . ' ' . $user->lastname;
  return ($brackets) ? '(' . trim($name) . ')' : $name;
}

function grab_all_users($limit = 0, $offset = 10, $orderBy = 'DESC') {
  return User::getAllUsers($limit, $offset, $orderBy);
}

function root_path($file = '') {
  $path = URL;
  if (!empty($file)) {
    $path .= $file;
  }
  return $path;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
  $url = 'http://www.gravatar.com/avatar/';
  $url .= md5( strtolower( trim( $email ) ) );
  $url .= "?s=$s&amp;d=$d&amp;r=$r";
  if ( $img ) {
    $url = '<img src="' . $url . '"';
    foreach ( $atts as $key => $val )
      $url .= ' ' . $key . '="' . $val . '"';
    $url .= ' />';
  }
  return $url;
}

/**
 * Checks to see if a user is logged in
 * @return bool Returns true when logged in.
 */
function _logged_in() {
  if (isset($_SESSION['USER'])) {
    return true;
  }
  return false;
}

function me_logged_in($username) {
  $user = get_user();
  if ($user) {
    if ($user->username == $username)
      return true;
  }
  return false;
}

/**
 * Returns the user object if a user is logged in.
 * @return object The user object providing they are logged in.
 */
// function get_logged_in_user() {
//   return true;
// }

/**
 * Generates a secure random password.
 * @param  integer $len The length of characters in the new password.
 * @return string       The new password.
 */
function random_password($len = 6) {
  $pass = "";
  $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  $maxlen = strlen($chars);
  if ( $len > $maxlen )
    $len = $maxlen;
  $i = 0;
  while ($i < $len) {
    $rdm_char = substr($chars, mt_rand(0, $maxlen-1), 1);
    if (!strstr($pass, $rdm_char))
      $pass .= $rdm_char;
    $i++;
  }
  return $pass;
}

function stylesheets_path($file = '') {
  $path = URL . 'stylesheets/';
  if (!empty($file)) {
    $path .= $file . '.css';
  }
  return $path;
}

function javascripts_path($file = '') {
  $path = URL . 'javascripts/';
  if (!empty($file)) {
    $path .= $file . '.js';
  }
  return $path;
}

function images_path($image) {
  $path = URL . 'images/';
  if (!empty($image)) {
    $path .= $image;
  }
  return $path;
}

/**
 * Gets a list of banned email extensions
 * @return array The banned email extensions.
 */
function get_banned_email_extensions() {
  // $banned = array();
  // $banned = explode(' ', Settings::get('banned_email_extensions'));
  // return $banned;
  return Settings::get('banned_email_extensions');
}

/**
 * Returns the value for checking to see if
 * registration is open
 * @return boolean
 */
function allow_registration() {
  return Settings::get('allow_registration');
}

/**
 * Validates a CSRF request.
 */
function _csrf() {

  if ($_SESSION['CSRF_TOKEN'] === $_POST['csrf']) {
    return true;
  } else {
    return false;
  }

  if (isset($_POST['CSRF_TOKEN']) && $_POST['csrf'] === $_SESSION['CSRF_TOKEN']) {
    return true; // Is valid.
  }
  return false; // Typical snake behavior.
}

function die_message($bool, $message) {
  return message($bool, $message, null, true);
}

/**
 * Returns a JSON encoded message.
 */
function message($bool, $message, $redirect = null, $die = false) {
  $message = array(
    'error' => (bool)$bool,
    'message' => $message
  );

  (!is_null($redirect)) ? $message['redirect'] = $redirect : null;

  if ($die === false)
    return json_encode($message);
  else
    return die(json_encode($message));
}

/**
 * Replaces the placeholders in the
 * users custom error/success messages.
 */
function _rd($key, $with, $define) {
  return preg_replace("/{{{$key}}}/", $with, $define);
}

/**
 * Get the application menu
 */
function get_menu() {
  require_once ROOT . '_partials/menu.php';
}

function get_admin_sidebar($active) {
  require_once ROOT . '_partials/admin_sidebar.php';
}

function get_messages_sidebar($active) {
  require_once ROOT . '_partials/messages_sidebar.php';
}

function get_header($title = 'Home') {
  require_once ROOT . '_partials/header.php';
}

function get_footer() {
  include ROOT . '_partials/footer.php';
}

/**
 * Check to see if an item in the menu is active.
 * If it is then this function will mark the li
 * with a class='active' attribute.
 */
function _is_active($page, $current, $justClass = false) {
  $page = strtolower($page);
  $current = strtolower($current);

  if ($page === $current && $justClass) return 'active';

  return ($page===$current) ? 'class="active"' : '';
}

/**
 * Get the CSRF token, useful for securing forms
 * against Cross Site Request Forgery
 */
function get_csrf_token() {
  return Core::getInstance()->getToken();
}

/**
 * Get the version of the system.
 */
function system_version() {
  return Settings::get('version');
}

/**
 * Get the default permission for a new member
 */
function default_permission() {
  return Settings::get('default_group');
}

/**
 * Gets the name of the system
 */
function system_name() {
  return Settings::get('name');
}

/**
 * Gets the system email address.
 */
function system_email() {
  return Settings::get('email');
}

function pm_system_enabled() {
  return (!!Settings::get('pm_disabled') === true) ? true : false;
}

/**
 * Get the URL of the system.
 */
function system_url($page = null) {
  return ($page !== null) ? Settings::get('url') . $page : Settings::get('url');
}

/**
 * Gets the meta description
 */
function meta_description() {
  return Settings::get('meta_description');
}

/**
 * Gets the author of the website.
 */
function meta_author() {
  return Settings::get('meta_author');
}

/**
 * Little parser, replaces {{placeholders}} with text.
 * @param  string $text The text to loop through and replace
 * @param  array  $data The data to be replaced
 * @return string
 */
function mini_parse($text, array $data) {
  foreach ($data as $key => $value)
    $text = preg_replace("/{{{$key}}}/", $value, $text);
  return $text;
}

function get_rel_path($from, $to)
{
  // Make sure directories have trailing slashes
  if (is_dir($from)) {
      $from = rtrim($from, '\/') . '/';
  }
  if (is_dir($to)) {
      $from = rtrim($from, '\/') . '/';
  }

  // Convert Windows slashes
  $from = str_replace('\\', '/', $from);
  $to = str_replace('\\', '/', $to);

  $from     = explode('/', $from);
  $to       = explode('/', $to);
  $relPath  = $to;

  foreach($from as $depth => $dir) {
      // find first non-matching dir
      if($dir === $to[$depth]) {
          // ignore this directory
          array_shift($relPath);
      } else {
          // get number of remaining dirs to $from
          $remaining = count($from) - $depth;
          if($remaining > 1) {
              // add traversals up to first matching dir
              $padLength = (count($relPath) + $remaining - 1) * -1;
              $relPath = array_pad($relPath, $padLength, '..');
              break;
          } else {
              $relPath[0] = './' . $relPath[0];
          }
      }
  }
  return implode('/', $relPath);
}

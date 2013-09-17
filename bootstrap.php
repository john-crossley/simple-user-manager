<?php
if (empty($_SESSION)) {
  session_regenerate_id();
  session_start();
}

error_reporting(0);
ini_set("display_errors", 0);

/**
 * This block of code gets the applications ROOT folder and
 * the URL of the application.
 */
define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');
$path1 = explode('/', str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])));
$path2 = explode('/', substr(ROOT, 0, -1));
$path3 = explode('/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));
for ($i = count($path2); $i < count($path1); $i++) array_pop($path3);
$url = $_SERVER['HTTP_HOST'] . implode('/', $path3);
// Fixed made by KRauer
($url{strlen($url) -1} == '/') ? define('URL', 'http://' . $url ) : define('URL', 'http://' . $url . '/');

define('TEMPLATE', ROOT . 'templates/');

/**
 * Autoload our classes man
 */
spl_autoload_register(function($class) {
  require_once ROOT . 'app/library/' . $class . '.php';
});

/**
 * So we can access certain files.
 */
define('ACCESS', true);

/**
 * Require some needed files.
 */
require_once ROOT . 'app/config/config.php';
require_once ROOT . 'app/config/custom_messages.php';
require_once ROOT . 'app/helper/functions.php';

// Check if the installation exists
if (is_dir(ROOT . 'install/')) {
  header("Location: " . URL . 'install/');
  exit;
}

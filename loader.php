<?php
session_start();
if (! isset($_SESSION['init'])) {
    session_regenerate_id(true);
    $_SESSION['init'] = true;
}

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');
$path1 = explode('/', str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])));
$path2 = explode('/', substr(ROOT, 0, -1));
$path3 = explode('/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));
for ($i = count($path2); $i < count($path1); $i++) array_pop($path3);
$url = $_SERVER['HTTP_HOST'] . implode('/', $path3);
($url{strlen($url) - 1} == '/') ? define('URL', 'http://' . $url) : define('URL', 'http://' . $url . '/');

// The path to the templates folder.
define('TEMPLATE', ROOT . 'public/templates/');

// Whether or not the browser has direct access to a file.
define('ACCESS', true);

/**
 * Add the ability to autoload our classes
 * @see http://www.php.net/manual/en/function.spl-autoload-register.php
 */
spl_autoload_register(
    function ($class) {
        require_once ROOT . 'app/library/' . $class . '.php';
    }
);


/**
 * Require some needed files.
 */
require_once ROOT . 'app/config/config.php';
require_once ROOT . 'app/config/custom_messages.php';
require_once ROOT . 'app/helper/functions.php';

if (CHECK_AND_RUN_INSTALL) {
    if (is_dir(ROOT . 'install/')) {
        redirect('install');
    }
}
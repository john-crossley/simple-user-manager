<?php
/**
 * Quick bash together script not intended to be used in production.
 * Once you have finished installing your system please delete this folder.
 */
require_once 'includes/functions.php';
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
($url{strlen($url) - 1} == '/') ? define('URL', 'http://' . $url . '/') : define('URL', 'http://' . $url);
?>

<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Installation &raquo; Simple User Manager</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="public/js/app.js"></script>

    <style>
        #change-settings {
            display: none;
        }

    </style>

</head>
<body>

<div class="container">

    <div class="page-header">
        <h1>Simple User Manager</h1>
        <p class="lead">a quick and easy, simple installer.</p>
    </div>

    <div class="row">

        <div class="col-md-4">
            <ul class="nav nav-pills nav-stacked">
                <li <?php echo is_active(1); ?>><a href="?step=1">Getting Started</a></li>
                <li <?php echo is_active(2); ?>><a href="?step=2">Requirements</a></li>
                <li <?php echo is_active(3); ?>><a href="?step=3">License Agreement</a></li>
                <li <?php echo is_active(4); ?>><a href="?step=4">Install</a></li>
                <li><br></li>
                <li><a href="http://johncrossley.io/support">Help</a></li>
            </ul>
        </div>

        <div class="col-md-8">
            <?php if (!isset($_GET['step']) OR (isset($_GET['step']) && $_GET['step'] === '1')): ?>
                <p>
                    Before you continue with the installation, I'd just like to thank you
                    for downloading my <strong>simple user manager</strong>. If you require
                    any assistance then don't hesitate to contact me using the
                    <a href="http://johncrossley.io/support">contact form</a> on my website.
                </p>
                <div class="btn-group pull-right">
                    <a href="?step=2" class="btn btn-default">Continue &raquo;</a>
                </div>
            <?php elseif(isset($_GET['step']) && $_GET['step'] === '2'): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Requirement</th>
                            <th>Suitable</th>
                            <th>Help</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PHP minimum version <strong>5.3.x</strong></td>
                            <td class="<?php echo(check_version(true) ? 'success' : 'danger'); ?>"><?php echo check_version(); ?>.x</td>
                            <td style="width: 10%;">
                                <a class="btn btn btn-info btn-link"
                                    href="http://php.net/downloads.php#5.5" target="_blank">More</a>
                            </td>
                        </tr>
                        <tr>
                            <td>PHP short tags enabled &lt;?=?&gt;</td>
                            <td class="<?php echo (@ini_get('short_open_tag') == 1) ? 'success' : 'danger'; ?>">
                                <?php echo (@ini_get('short_open_tag') == 1) ? 'Yes' : 'No'; ?>
                            </td>
                            <td style="width: 10%;">
                                <a class="btn btn btn-info btn-link"
                                    href="http://stackoverflow.com/questions/2185320/how-to-enable-php-short-tags" target="_blank">More</a>
                            </td>
                        </tr>
                        <tr>
                            <td>PDO extension enabled</td>
                            <td class="<?php echo (extension_loaded('pdo')) ? 'success' : 'danger'; ?>">
                                <?php echo (extension_loaded('pdo')) ? 'Yes' : 'No'; ?>
                            </td>
                            <td style="width: 10%;">
                                <a class="btn btn btn-info btn-link"
                                    href="http://php.net/manual/en/book.pdo.php" target="_blank">More</a>
                            </td>
                        </tr>
                        <tr>
                            <td>PDO MySQL drive enabled</td>
                            <td class="<?php echo (extension_loaded('pdo_mysql')) ? 'success' : 'danger'; ?>">
                                <?php echo (extension_loaded('pdo_mysql')) ? 'Yes' : 'No'; ?>
                            </td>
                            <td style="width: 10%;">
                                <a class="btn btn btn-info btn-link"
                                    href="http://lmgtfy.com/?q=How+to+enable+pdo+mysql+driver" target="_blank">More</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>
                    If your system does not meet the above requirements I will not stop you from continuing with the
                    installation. Keep in mind that the system <strong>could fail</strong> shout it not meet any of the above.
                </p>
                <div class="btn-group pull-right">
                    <a href="index.php" class="btn btn-default">&laquo; Back</a>
                    <a href="?step=3" class="btn btn-default">Continue &raquo;</a>
                </div>
            <?php elseif (isset($_GET['step']) && $_GET['step'] === '3'): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>License Type</th>
                            <th>Link</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Regular License</td>
                            <td><a href="http://codecanyon.net/licenses/regular"
                                   target="_blank">http://codecanyon.net/licenses/regular</a></td>
                        </tr>
                        <tr>
                            <td>Extended License</td>
                            <td><a href="http://codecanyon.net/licenses/extended" target="_blank">http://codecanyon.net/licenses/extended</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>
                    Please ensure you read the license which applies to you and you situation. If you refuse to acknowledge
                    the terms of these licenses then I will refuse support. Once you're happy with either of the following
                    licenses then you may proceed with the installation.
                </p>

                <div class="checkbox pull-left">
                    <label>
                        <input type="checkbox" id="license-agreement" name="license"
                            <?php echo (isset($_COOKIE['license_accepted']) ? 'checked' : ''); ?>>
                        I agree to accept the license (Regular or Extended).
                    </label>
                </div>
                <div class="btn-group pull-right">
                    <a href="?step=2" class="btn btn-default">&laquo; Back</a>
                    <a href="?step=4" class="btn btn-default" id="license-agreement-button"
                        <?php echo (isset($_COOKIE['license_accepted']) ? '' : 'disabled'); ?>>Continue &raquo;</a>
                </div>
            <?php elseif (isset($_GET['step']) && $_GET['step'] === '4' && isset($_COOKIE['license_accepted'])): ?>

                <form action="index.php" method="POST">
                    <div class="form-group">
                        <label for="site-url">URL to simple user manager (base)</label>
                        <input type="text" class="form-control" id="site-url"
                            placeholder="Eg: http://johncrossley.io" value="<?php echo preg_replace('/install/', '', URL); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email-address">Email address</label>
                        <input type="email" class="form-control" id="email-address" placeholder="Eg: john.doe@example.com">
                    </div>

                    <div class="form-group">
                        <label for="db-host">Database hostname</label>
                        <input type="text" class="form-control" id="db-host" placeholder="Eg: localhost">
                        <small class="help-block">
                            The hostname of the database, this is typically <strong>localhost</strong>.
                            If you are unsure then contact your host.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="db-username">Database username</label>
                        <input type="text" class="form-control" id="db-username" placeholder="Eg: john_crossley">
                        <small class="help-block">
                            The username of the database, again if you're unsure contact your host.
                        </small>
                    </div>
                    <!--//form-group-->

                    <div class="form-group">
                        <label for="db-password">Database password</label>
                        <input type="password" class="form-control" id="db-password" placeholder="Eg: ...">
                        <small class="help-block">
                            The password of the database, again if you're unsure contact your host.
                        </small>
                    </div>
                    <!--//form-group-->

                    <div class="form-group">
                        <label for="db-name">Database name</label>
                        <input type="text" class="form-control" id="db-name" placeholder="Eg: simple_user_manager">
                        <small class="help-block">
                            The name of the database you'd like to use for the application. Please ensure you have created this
                            otherwise the connection will fail. <strong class="text-danger"><i>Please note that all data in this
                                    database will be deleted!</i></strong>
                        </small>
                    </div>

                    <div class="pull-left" id="change-settings">
                        <a href="#" class="btn btn-link">Delete Settings</a>
                    </div>

                    <div class="btn-group pull-right" style="margin-bottom: 4em;">
                        <a href="?step=3" class="btn btn-default">&laquo; Back</a>
                        <a href="#" id="test-connection" class="btn btn-default"
                            <?php echo (isset($_COOKIE['connection_success']) ? 'disabled' : ''); ?>>Test Connection</a>
                        <a href="?step=install" class="btn btn-default" id="install-button"
                            <?php echo (isset($_COOKIE['connection_success']) ? '' : 'disabled'); ?>>Install</a>
                    </div>

                </form>
            <?php elseif (isset($_GET['step']) && $_GET['step'] === 'install' && isset($_COOKIE['license_accepted'])): ?>

                <p>1) Starting installation...</p>

                <?php
                $conn = new mysqli($_COOKIE['db_host'], $_COOKIE['db_username'],
                    $_COOKIE['db_password'], $_COOKIE['db_name']);
                if ($conn->connect_error) {
                    echo "<p>2) Connection failed... <a href='index.php?step=4'>Go back?</a></p>";
                    exit;
                }
                echo "<p>2) Successfully connected...</p>";

                if ($conn->query("DROP DATABASE {$_COOKIE['db_name']}")) {
                    echo "<p>3) Dropping database {$_COOKIE['db_name']}...</p>";
                }

                if ($conn->query("CREATE DATABASE {$_COOKIE['db_name']}")) {
                    echo "<p>4) Creating database {$_COOKIE['db_name']}...</p>";
                }

                // Load the SQL file.
                $query = file_get_contents(ROOT . 'data/simple-user-manager.sql');

                // Reconnect
                $conn = new mysqli($_COOKIE['db_host'], $_COOKIE['db_username'],
                    $_COOKIE['db_password'], $_COOKIE['db_name']);

                if (mysqli_multi_query($conn, $query)) {
                    echo "<p>5) Installing SQL dump...</p>";
                }

                echo "<p>6) Done... add the following information to your <strong>app/config/config.php</strong></p>";

                echo "<p class='text-danger'>Delete or rename the installation folder.</p>";

                echo "<code>
                    DB::connect(array(
                    <br>&nbsp;&nbsp;'host'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_host']}',
                    <br>&nbsp;&nbsp;'username'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_username']}',
                    <br>&nbsp;&nbsp;'password'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_password']}',
                    <br>&nbsp;&nbsp;'database'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_name']}'
                    <br>));
                    </code>";
                ?>
                <script>
                    eraseCookie('db_host');
                    eraseCookie('db_username');
                    eraseCookie('db_password');
                    eraseCookie('db_name');
                    eraseCookie('db_name');
                    eraseCookie('license_accepted');
                    eraseCookie('email_address');
                    eraseCookie('site_url');
                </script>

            <?php else: ?>
                <p>Have you <a href="index.php?step=3">accepted the license agreement?</a></p>
            <?php endif; ?>
        </div>

    </div>

</div>

</body>
</html>
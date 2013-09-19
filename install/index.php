<?php
require_once '../app/library/SingletonAbstract.php';
require_once '../app/library/Flash.php';
require_once 'functions.php';

if (empty($_SESSION)) {
  session_regenerate_id();
  session_start();
}

if (isset($_POST['task']) && $_POST['task'] === 'test_connection') {

  // Save the connection information
  $host   = (isset($_POST['host']) ? strip_tags($_POST['host']) : '');
  $user   = (isset($_POST['user']) ? strip_tags($_POST['user']) : '');
  $pass   = (isset($_POST['pass']) ? strip_tags($_POST['pass']) : '');
  $dbname = (isset($_POST['dbname']) ? strip_tags($_POST['dbname']) : '');

  // Ok test the connection
  $link = @mysqli_connect($host, $user, $pass, $dbname)
    or die(json_encode(array('error' => true, 'message' => 'MYSQL Said: ' . mysqli_connect_error())));


  $_SESSION['DB']['HOST'] = $host;
  $_SESSION['DB']['USER'] = $user;
  $_SESSION['DB']['PASS'] = $pass;
  $_SESSION['DB']['DBNAME'] = $dbname;

  echo json_encode(array(
    'error' => false,
    'message' => 'Successfully connected to the database! Ready to rock and roll?'
  ));


  exit;
}

if (isset($_POST['task']) && $_POST['task'] === 'license') {

  if (empty($_POST['code'])) {
    echo json_encode(array('error' => true, 'message' => 'You must enter your license key!'));
    exit;
  }

  $url = "http://phpcodemonkey.com/api/v1/license";

  $fields = array(
    'code' => urlencode($_POST['code']),
    'fullname' => urlencode($_SESSION['USER_INFORMATION']['FULLNAME']),
    'email' => urlencode($_SESSION['USER_INFORMATION']['EMAIL']),
    'url' => urlencode($_SESSION['USER_INFORMATION']['URL'])
  );

  $string = "";
  foreach ($fields as $key => $value)
    $string .= $key . '=' . $value . '&';

  rtrim($string, '&');

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, count($fields));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($ch);

  curl_close($ch);

  echo $result;
  exit;

}

if (isset($_POST['task']) && $_POST['task'] === 'save_user') {
  // Right save the information in a session... for now!
  $url      = $_SESSION['USER_INFORMATION']['URL']      = strip_tags($_POST['url']);
  $fullname = $_SESSION['USER_INFORMATION']['FULLNAME'] = strip_tags($_POST['fullname']);
  $email    = $_SESSION['USER_INFORMATION']['EMAIL']    = strip_tags($_POST['email']);

  // Cheeky validation
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array('error' => true, 'message' => 'Please enter a valid email address.'));
    exit;
  }

  echo json_encode(array('error' => false, 'message' => 'Information has been saved!'));
  exit;
}

define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');
$path1 = explode('/', str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])));
$path2 = explode('/', substr(ROOT, 0, -1));
$path3 = explode('/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));
for ($i = count($path2); $i < count($path1); $i++) array_pop($path3);
$url = $_SERVER['HTTP_HOST'] . implode('/', $path3);
($url{strlen($url) -1} == '/') ? define('URL', 'http://' . $url . '/') : define('URL', 'http://' . $url);

?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Installation &raquo; Advanced User Management System</title>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">

  <style>
    body {
      width: 80%;
      margin: 4em auto;
    }

    h1 {
      font-weight: normal;
    }

    .row {
      margin-top: 1.5em;
    }

    #start-installation {
      display: none;
    }

  </style>

</head>
<body>

  <div class="no-js">
    <div class="alert alert-danger">
      <strong>Warning: </strong> you must enable JavaScript to proceed with the installation!
    </div><!--//.alert alert-danger-->
  </div><!--//.no-js-->


  <?php if ( $message = Flash::show() ): ?>
    <div class="alert alert-<?=$message['type']?>">
      <?=$message['msg']?>
    </div><!--alert-->
  <?php endif; ?>

  <div class="row">
    <h1>Welcome to Advanced User Manager Installation</h1>
    <p>
      Hello and welcome to the <strong>advanced user manager</strong>installation. The
      installation script will attempt to install the system for you and help offer advice
      should something go wrong. Before we continue you will need to provide some information
      in order to continue.
    </p>
  </div><!--//.row-->

  <div class="row">

    <div class="col-md-4">
      <ul class="nav nav-pills nav-stacked">
        <li <?php is_active(1); ?>><a href="?step=1">Getting Started</a></li>
        <li <?php is_active(2); ?>><a href="?step=2">Requirements</a></li>
        <li <?php is_active(3); ?>><a href="?step=3">License Agreement</a></li>
        <li <?php is_active(4); ?>><a href="?step=4">Install</a></li>
        <li><br></li>
        <li><a href="../help/" target="_blank">Help</a></li>
      </ul>
    </div><!--//.col-md-4-->

    <div class="col-md-8">

      <?php if ((isset($_GET['step']) && $_GET['step'] == 4) && check_license() && isset($_GET['start_installation'])): ?>

        <?php if (!isset($_SESSION['DB']) OR !isset($_SESSION['USER_INFORMATION'])) {
          Flash::make('danger', 'You must provide database and user information.');
          header('Location: index.php');
          exit;
        }
        ?>

        <p>1) Starting installation...</p>

        <?php
        $connection = $_SESSION['DB'];
        $conn = new mysqli($connection['HOST'], $connection['USER'], $connection['PASS'], $connection['DBNAME']);
        if ($conn->connect_error) {
          echo "<p>2) Connection failed... <a href='index.php?step=4'>Go back?</a></p>";
          exit;
        }
        echo "<p>2) Successfully connected...</p>";

        if ($conn->query("DROP DATABASE {$connection['DBNAME']}")) {
          echo "<p>3) Dropping database {$connection['DBNAME']}...</p>";
        }

        if ($conn->query("CREATE DATABASE {$connection['DBNAME']}")) {
          echo "<p>4) Creating database {$connection['DBNAME']}...</p>";
        }

        // Load the SQL file.
        $query = file_get_contents(ROOT . 'advanced_user_manager18-01.sql');

        // Reconnect
        $conn = new mysqli($connection['HOST'], $connection['USER'], $connection['PASS'], $connection['DBNAME']);

        if (mysqli_multi_query($conn, $query)) {
          echo "<p>5) Installing SQL dump...</p>";
        }

        echo "<p>6) Done... add the following information to your <strong>app/config/config.php</strong></p>";

        echo "<p class='text-danger'><strong>DELETE OR RENAME THE INSTALLATION FOLDER</strong></p>";

        echo "<code>
        DB::connect(array(
        <br>&nbsp;&nbsp;'host'&nbsp;=&gt;&nbsp;'{$connection['HOST']}',
        <br>&nbsp;&nbsp;'username'&nbsp;=&gt;&nbsp;'{$connection['USER']}',
        <br>&nbsp;&nbsp;'password'&nbsp;=&gt;&nbsp;'{$connection['PASS']}',
        <br>&nbsp;&nbsp;'database'&nbsp;=&gt;&nbsp;'{$connection['DBNAME']}'
        <br>));
        </code>";


        ?>





      <?php elseif ((isset($_GET['step']) && $_GET['step'] == 4) && check_license()): ?>

        <div class="row">
          <form role="form" id="about-you">
            <div class="form-group">
              <label for="site-url">URL to Advanced User Manager</label>
              <input type="text" class="form-control" id="site-url" placeholder="Eg: http://phpcodemonkey.com/aum"
                value="<?php echo preg_replace('/install/', '', URL);?>">
              <small class="help-block">
                URL to the advanced user management system.
              </small>
            </div><!--//form-group-->


            <div class="form-group">
              <label for="fullname">Full name</label>
              <input type="text" class="form-control" id="fullname" placeholder="Eg: John Doe">
            </div><!--//form-group-->

            <div class="form-group">
              <label for="email-address">Email Address</label>
              <input type="email" class="form-control" id="email-address" placeholder="Eg: john.doe@example.com">
            </div><!--//form-group-->

            <button id="save-info" class="btn btn-success pull-right btn-sm">Save</button>
          </form>
        </div><!--//.row-->

        <div class="row">
          <form role="form" id="license-info">
            <div class="form-group">
              <label for="license-key">License Key</label>
              <input type="text" class="form-control" id="license-key" placeholder="Eg: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" disabled>
              <small class="help-block">
                The license key envato emailed you when you purchased this product.
                <i><strong>If you don't provide your license information you will be refused support.</strong></i>
              </small>
            </div><!--//form-group-->
            <button class="btn btn-success pull-right btn-sm disabled">Submit Key</button>
          </form>
        </div><!--//.row-->

        <div class="row">
          <form role="form" id="db-connection">
            <div class="form-group">
              <label for="db-host">Database Hostname</label>
              <input type="text" class="form-control" id="db-host" placeholder="Eg: localhost">
              <small class="help-block">
                The hostname of the database, this is typically <strong>localhost</strong>.
                If you are unsure then contact your host.
              </small>
            </div><!--//form-group-->

            <div class="form-group">
              <label for="db-username">Database Username</label>
              <input type="text" class="form-control" id="db-username" placeholder="Eg: john_crossley">
              <small class="help-block">
                The username of the database, again if you're unsure contact your host.
              </small>
            </div><!--//form-group-->

            <div class="form-group">
              <label for="db-password">Database Password</label>
              <input type="password" class="form-control" id="db-password" placeholder="Eg: ...">
              <small class="help-block">
                The password of the database, again if you're unsure contact your host.
              </small>
            </div><!--//form-group-->

            <div class="form-group">
              <label for="db-name">Database Name</label>
              <input type="text" class="form-control" id="db-name" placeholder="Eg: advanced_user_manager">
              <small class="help-block">
                The name of the database you'd like to use for the application. Please ensure you have created this
                otherwise the connection will fail. <strong class="text-danger"><i>Please note that all data in this database will be deleted!</i></strong>
              </small>
            </div><!--//form-group-->

            <button class="btn btn-success pull-right btn-sm">Test Connection</button>
          </form>
        </div><!--//.row-->

        <div class="row" id="start-installation">
          <a class="btn btn-warning btn-lg btn-block" href="index.php?step=4&amp;start_installation">Start Installation</a>
        </div><!--//.row-->

      <?php elseif (isset($_GET['step']) && $_GET['step'] == 3): ?>

        <p>By installing this application you are accepting the following
        license agreements.</p>

        <table class="table table-bordered">
          <thead>
            <tr>
              <th>License</th>
              <th>Link</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Regular License</td>
              <td><a href="http://codecanyon.net/licenses/regular" target="_blank">http://codecanyon.net/licenses/regular</a></td>
            </tr>
            <tr>
              <td>Extended License</td>
              <td><a href="http://codecanyon.net/licenses/extended" target="_blank">http://codecanyon.net/licenses/extended</a></td>
            </tr>
          </tbody>
        </table>

        <p>Failing to adhere to any of the following <strong>phpcodemonkey</strong> has the right
        to blacklist your purchase code and refuse support should you require it.</p>

        <div class="checkbox pull-left">
          <label>
            <input type="checkbox" name="license">
              I agree to accept the license (Regular or Extended).
          </label>
        </div><!--//.checkbox-->
        <a class="btn btn-success pull-right disabled" id="license-agreement" href="index.php?step=4">Next &raquo;</a>

      <?php elseif (isset($_GET['step']) && $_GET['step'] == 2): ?>

        <p>Below is a table of requirements to help determine whether or not the
        system will work on your current setup. To find out more information about
        each item, select the help <strong>help</strong> button. For the application
        to work as intended you <strong>MUST</strong> meet the requirements.</p>

        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Requirement</th>
              <th>Suitable</th>
              <th>More</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>PHP minimum version <strong>5.3.x</strong></td>
              <td class="<?php echo (check_version(true) ? 'success' : 'danger'); ?>"><?php echo check_version();?>.x</td>
              <td style="width: 10%;">
                <a class="btn btn-danger btn-xs" href="http://php.net/downloads.php#5.5" target="_blank">Help ?</a>
              </td>
            </tr>
            <tr>
              <td>PHP short tags enabled &lt;?=?&gt;</td>
              <td class="<?php echo (@ini_get('short_open_tag')==1) ? 'success': 'danger'; ?>">
                <?php echo (@ini_get('short_open_tag')==1) ? 'Yes': 'No'; ?>
              </td>
              <td style="width: 10%;">
                <a class="btn btn-danger btn-xs" href="http://stackoverflow.com/questions/2185320/how-to-enable-php-short-tags" target="_blank">Help ?</a>
              </td>
            </tr>
            <tr>
              <td>PDO extension enabled</td>
              <td class="<?php echo (extension_loaded('pdo')) ? 'success' : 'danger' ;?>">
                <?php echo (extension_loaded('pdo')) ? 'Yes' : 'No';?>
              </td>
              <td style="width: 10%;">
                <a class="btn btn-danger btn-xs" href="http://php.net/manual/en/book.pdo.php" target="_blank">Help ?</a>
              </td>
            </tr>
            <tr>
              <td>PDO MySQL drive enabled</td>
              <td class="<?php echo (extension_loaded('pdo_mysql')) ? 'success' : 'danger' ;?>">
                <?php echo (extension_loaded('pdo_mysql')) ? 'Yes' : 'No';?>
              </td>
              <td style="width: 10%;">
                <a class="btn btn-danger btn-xs" href="http://lmgtfy.com/?q=How+to+enable+pdo+mysql+driver" target="_blank">Help ?</a>
              </td>
            </tr>
            <tr>
              <td>cURL enabled</td>
              <td class="<?php echo (function_exists('curl_version')) ? 'success' : 'danger';?>">
                <?php echo (function_exists('curl_version')) ? 'Yes' : 'No';?>
              </td>
              <td style="width: 10%;">
                <a class="btn btn-danger btn-xs" href="http://www.tomjepson.co.uk/enabling-curl-in-php-php-ini-wamp-xamp-ubuntu/" target="_blank">Help ?</a>
              </td>
            </tr>
          </tbody>
        </table>
        <p>If you're happy to continue with the installation hit the button below. Please note that all the above
        requirements are needed for the application to work correctly.</p>

        <a class="btn btn-success pull-right" href="index.php?step=3">Next &raquo;</a>

      <?php else: ?>
        <p>
          Hi, first of all thank you downloading <strong>advanced user manager</strong>. This
          installation shouldn't take more than 2 minutes to install. When your ready to start hit
          the next button to continue. If anything goes wrong during the installation then the system
          will try and guide you on how to resolve any issues.
        </p>

        <a class="btn btn-success pull-right" href="index.php?step=2">Next &raquo;</a>
      <?php endif; ?>

    </div><!--//.col-md-8-->

  </div><!--//.row-->

  <!-- Latest compiled and minified JavaScript -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
  <script src="app.js"></script>
</body>
</html>

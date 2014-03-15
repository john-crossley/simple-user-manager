<?php
define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');
if (! isset($_COOKIE['license_accepted'], $_COOKIE['db_username'], $_COOKIE['db_password'], $_COOKIE['db_name'])) {
    die("<p>Please go back and start the installation again. <a href='index.php'>Go Back</a></p>");
}
?>
<p>1) Establishing a connection to the database...</p>
<?php
$conn = new mysqli($_COOKIE['db_host'], $_COOKIE['db_username'], $_COOKIE['db_password'], $_COOKIE['db_name']);
if ($conn->connect_error) {
    echo "<p>2) Connection failed did you change your settings...? <a href='index.php?step=4'>Go back?</a></p>";
    exit;
}
?>
<p>2) Connected...</p>
<?php
if ($conn->query("DROP DATABASE {$_COOKIE['db_name']}")) {
    echo "<p>3) Dropping database {$_COOKIE['db_name']}...</p>";
}
if ($conn->query("CREATE DATABASE {$_COOKIE['db_name']}")) {
    echo "<p>4) Creating database {$_COOKIE['db_name']}...</p>";
}
if (! file_exists(ROOT . 'data/simple-user-manager.sql')) {
    echo "<p>5) Unable to find <strong>data/simple-user-manager.sql</strong></p>";
    exit;
}
$query = file_get_contents(ROOT . 'data/simple-user-manager.sql');
if (mysqli_multi_query($conn, $query)) {
    echo "<p>5) Installing SQL dump...</p>";
}
echo "<p>6) Done... add the following information to your <strong>app/config/config.php</strong></p>";
echo "<code>
    DB::connect(array(
    <br>&nbsp;&nbsp;'host'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_host']}',
    <br>&nbsp;&nbsp;'username'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_username']}',
    <br>&nbsp;&nbsp;'password'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_password']}',
    <br>&nbsp;&nbsp;'database'&nbsp;=&gt;&nbsp;'{$_COOKIE['db_name']}'
    <br>));
    </code>";
?>
<br>
<br>
<p>7) Cleaning up...</p>
<p>Please ensure you delete or rename the installation folder.</p>
<?php
$_COOKIE['db_host'] = null;
$_COOKIE['db_username'] = null;
$_COOKIE['db_password'] = null;
$_COOKIE['db_name'] = null;
?>
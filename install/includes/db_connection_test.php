<?php
header('Content-type: application/json');

if (isset($_COOKIE['db_host'], $_COOKIE['db_username'], $_COOKIE['db_password'], $_COOKIE['db_name'])) {

    $host = $_COOKIE['db_host'];
    $username = $_COOKIE['db_username'];
    $password = $_COOKIE['db_password'];
    $dbname = $_COOKIE['db_name'];

    @mysqli_connect($host, $username, $password, $dbname) or
        die(
            json_encode(
                array(
                    'error' => true,
                    'message' => 'Failed to connect to the database. Check your settings.'
                )
            )
        );
    die(json_encode(array('error' => false, 'message' => 'Success, connection has been established.')));
}

die(json_encode(array('error' => true, 'message' => 'You must enter all of the following information.')));
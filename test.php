<?php
require_once 'loader.php';

$user = get_user();

if ($user->custom_image) {
    echo "custom image";
}
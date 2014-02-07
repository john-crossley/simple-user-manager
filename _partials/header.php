<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <title> <?php echo $title; ?> &raquo; <?php echo system_name(); ?> </title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?php echo stylesheets_path('bootstrap.min'); ?>">
    <link rel="stylesheet" href="<?php echo stylesheets_path('bootstrap-theme.min'); ?>">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="<?php echo stylesheets_path('main'); ?>">

    <!-- Meta Information -->
    <meta name="description" content="<?php echo meta_description(); ?>">
    <meta name="author" content="<?php echo meta_author(); ?>">

</head>
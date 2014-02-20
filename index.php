<?php
require_once 'loader.php';
get_header('Home');
?>
<body>

<!-- Menu -->
<?php echo get_menu('home'); ?>

<div class="jumbotron">
    <div class="container">
        <h1>Hello, Everyone!</h1>

        <p>Welcome to my <strong>super awesome</strong> simple user management system. I listened to what you
            guys wanted and produced this amazing piece of php machinery and it's <em>Frequently updated!</em></p>

        <p><a class="btn btn-primary btn-lg" href="<?php echo root_path('login.php'); ?>" role="button">Get started &raquo;</a>
        </p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <h3>Latest Creation</h3>

            <p>Hello and thanks for choosing to look at my latest creation! As
                promised a new and improved user management system for you to use
                with extensive documentation. This latest app in the series is
                so much more easier and advanced than its predecessors.</p>
            <a href="<?php echo root_path('register.php'); ?>" class="btn btn-success">Get Started &raquo;</a>
        </div>

        <div class="col-md-4">
            <h3>Hello Bootstrap</h3>

            <p>Yes it's using the latest version of bootstrap! That means you can
                use all of bootstraps awesome features. All additional styles are separate
                from bootstrap. So if you want you can completly redesign the system to better
                suit your needs.</p>
            <a href="http://getbootstrap.com/" target="_blank" class="btn btn-success">Visit Bootstrap &raquo;</a>
        </div>

        <div class="col-md-4">
            <h3>Great Documentation</h3>

            <p>I have provided great documentation for you on how to use this
                wonderful system. I will also provide videos on how to accomplish
                certain tasks to make your use of advanced user manager that little
                bit easier.</p>
            <a href="<?php echo root_path('help'); ?>" target="_blank" class="btn btn-success">View Docs &raquo;</a>
        </div>
    </div>
</div>

<?php echo get_footer(); ?>

</body>
</html>

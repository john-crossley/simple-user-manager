<?php
require_once 'loader.php';
get_header('Login');


if (! empty($_POST)) {
    if (isset($_POST['username']) || isset($_POST['email']) && isset($_POST['password'])) {
        csrf_check('login.php');
        $username = isset($_POST['username']) ? $_POST['username'] : $_POST['email'];
        if (empty($username) || empty($_POST['password'])) {
            Flash::make('danger', LOGIN_FORM_DATA_NOT_SUPPLIED);
            redirect('login.php');
        }
        User::auth($username, $_POST['password'], isset($_POST['email']) ? true : false);
    }
}
?>

<body>
    <?php echo get_menu('login'); ?>

    <div class="container">

        <form method="post" action="<?php echo root_path('login.php'); ?>" class="form-login-register" role="form">
            <h2 class="form-login-register-heading"><?php echo system_name(); ?> Login</h2>
            <input type="hidden" name="task" value="login">
            <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
            <?php if (username_disabled()): ?>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email address" required autofocus>
            <?php else: ?>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
            <?php endif; ?>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <button class="btn btn-success pull-left" type="submit">Login</button>
            <a href="#forgot-password-modal" data-toggle="modal" class="btn btn-link pull-right">Forgot password</a>
        </form>

    </div>

    <div class="modal fade" id="forgot-password-modal">
        <form action="<?php echo root_path('process.php'); ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Forgotten your login information?</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="task" value="login">
                            <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
                            <label for="username" class="control-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                            <small class="help-block" id="forgot-password-help-block">Please enter an email address...</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="request-forgot-password-btn">Get Information</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->

    <?php echo get_footer(); ?>
</body>
</html>

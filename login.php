<?php
require_once 'loader.php';
get_header('Login');

if (!empty($_POST)) {
  // Process some post data.
  if ( ( isset($_POST['username']) || isset($_POST['email']) ) && isset($_POST['password'])) {

    csrf_check('login.php');

    // Assign either the username or email field to the username var.
    $username = (isset($_POST['username']) ? $_POST['username'] : $_POST['email']);

    if (empty($username) || empty($_POST['password'])) {
      Flash::make('danger', LOGIN_FORM_DATA_NOT_SUPPLIED);
      redirect('login.php');
    }

    $remember = false;

    if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on')
      $remember = true;

    // Authorise man, authorise... YES that is it!
    User::auth($username, $_POST['password'], (isset($_POST['email'])) ? true : false);

  }
}

?>

<body>
  <!-- Menu -->
  <?=get_menu('login')?>

  <div class="row">
    <div class="container">

      <div class="panel panel-default form-login">
        <div class="panel-heading">
          <h3 class="panel-title">Login to <?=system_name()?></h3>
        </div><!--//panel-heading-->
        <div class="panel-body">
          <form method="post" action="<?=root_path('login.php')?>">
            <fieldset>
              <input type="hidden" name="task" value="login">
              <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
              <?php if (username_disabled()): ?>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email address">
              </div><!--//.form-group-->
              <?php else: ?>
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
              </div><!--//.form-group-->
              <?php endif; ?>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
              </div><!--//.form-group-->
              <button type="submit" class="btn btn-primary btn-xs">Login</button>
              <a href="#forgot-password-modal" data-toggle="modal" class="btn btn-link pull-right">Forgot password</a>
            </fieldset>
          </form>
        </div><!--//panel-body-->
      </div><!--//panel panel-default-->

    </div><!--//.container-->
  </div><!--//.row-->

  <!-- Modal -->
  <div class="modal fade" id="forgot-password-modal">
    <form action="<?=root_path('process.php')?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Forgot your login information?</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <input type="hidden" name="task" value="login">
              <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
              <label for="username" class="control-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
              <small class="help-block" id="forgot-password-help-block">Please enter an email address...</small>
            </div><!--//.form-group-->
          </div><!--//.modal-body-->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn-sm" id="request-forgot-password-btn">Request Password</button>
          </div><!--//.modal-footer-->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <?=get_footer()?>
</body>
</html>

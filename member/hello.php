<?php
require_once '../bootstrap.php';
get_header('Member Area');
restrict_access();
?>

<body>
  <?=get_menu()?>

  <div class="row">
    <div class="container main">

      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Welcome <?=ucfirst(fullname(get_user()))?></h3>
        </div>
        <p>If your viewing this page then you have been given access to it (or your an admin)</p>
      </div><!--//.panel-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>
</body>
</html>

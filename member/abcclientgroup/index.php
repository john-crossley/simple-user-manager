<?php
require_once '../../bootstrap.php';
get_header('Member Area');
restrict_access('ABCClientGroup', 'Administrator');
?>
<body>
  <?=get_menu()?>

  <div class="row">
    <div class="container main">

      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Welcome <?=ucfirst(fullname(get_user()))?></h3>
        </div>
        <p>Only users with the access group <strong>ABCClientGroup</strong> can access this area. (or admin)</p>
      </div><!--//.panel-->

    </div><!--//.container-->
  </div><!--//.row-->

  <?=get_footer()?>
</body>
</html>

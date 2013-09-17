<?php
require_once 'bootstrap.php';

get_header('Home');

?>
<body>

  <!-- Menu -->
  <?=get_menu('home')?>

  <div class="container">

    <div class="jumbotron">
      <h1>Hello Everyone</h1>
      <p class="lead">
        Welcome to my <strong>SUPER AWERSOME</strong> user management system.
        I kinda listened to what some of you guys wanted and produced this amazing
        piece of php machinery.
      </p>

	    <div class="row">
		    <a href="http://codecanyon.net/item/advanced-user-management-system/5366263" onclick="return confirm('Are you sure you want to buy?')" class="btn btn-large btn-primary">Buy Me!</a>
		    <a href="<?=root_path('login.php')?>" class="btn btn-large btn-success">Try User Manager!</a>
	    </div><!--//row-->

	     <hr>

    </div><!--//jumbotron-->

    <div class="row">

      <div class="col-lg-4">
        <h2>Latest Creation</h2>
        <p>
          Hello and thanks for choosing to look at my latest creation! As
          promised a new and improved user management system for you to use
          with extensive documentation. This latest app in the series is
          so much more easier and advanced than its predecessors.
        </p>
        <a href="<?=root_path('register.php')?>" class="btn btn-primary">Get Started &raquo;</a>
      </div><!--//col-lg-4-->

      <div class="col-lg-4">
        <h2>Hello Bootstrap!</h2>
        <p>
          Yes it's using the latest version of bootstrap! That means you can
          use all of bootstraps awesome features. All additional styles are separate
          from bootstrap. So if you want you can completly redesign the system to better
          suit your needs.
        </p>
        <a href="http://getbootstrap.com/" target="_blank" class="btn btn-primary">Visit Bootstrap &raquo;</a>
      </div><!--//col-lg-4-->

      <div class="col-lg-4">
        <h2>Great Documentation</h2>
        <p>
          I have provided great documentation for you on how to use this
          wonderful system. I will also provide videos on how to accomplish
          certain tasks to make your use of advanced user manager that little
          bit easier.
        </p>
        <a href="<?=root_path('help')?>" target="_blank" class="btn btn-primary">View Docs &raquo;</a>
      </div><!--//col-lg-4-->

    </div><!--//row-->

  </div><!--container-->

  <?=get_footer()?>

</body>
</html>

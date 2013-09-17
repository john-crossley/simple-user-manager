  <footer>
    <div class="container">
      <small>
        &copy; <?=date('Y')?> - <a href="http://phpcodemonkey.com/" target="_blank">John Crossley</a> -
        <a href="http://phpcodemonkey.com/contact" target="_blank">Contact</a>
      </small>
      <?php if (DEMO_MODE): ?>
        <small class="text-danger pull-right"><strong>DEMO MODE v<?=system_version()?></strong></small>
      <?php endif; ?>
    </div><!--//.container-->
  </footer>

  <!-- Javascripts -->
  <script src="<?=javascripts_path('jquery-2.0.0.min')?>"></script>
  <script src="<?=javascripts_path('bootstrap')?>"></script>
  <script src="<?=javascripts_path('app')?>"></script>

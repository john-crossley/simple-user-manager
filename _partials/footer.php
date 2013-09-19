  <footer>
    <div class="container">
      <small>
        &copy; <?=date('Y')?> - <a href="http://phpcodemonkey.com/" target="_blank">John Crossley</a> -
        <a href="http://phpcodemonkey.com/contact" target="_blank">Contact</a>
      </small>
    </div><!--//.container-->
  </footer>

  <!-- Javascripts -->
  <script src="<?=javascripts_path('jquery-2.0.0.min')?>"></script>
  <script src="<?=javascripts_path('bootstrap')?>"></script>
  <script src="<?=javascripts_path('app')?>"></script>

<?php ob_end_flush(); // Fixed by KRauer ?>

<div class="container">
    <hr>
    <footer>
        <small>
            &copy; <?=date('Y')?> - <a href="http://phpcodemonkey.com/" target="_blank">John Crossley</a> -
            <a href="mailto:hello@phpcodemonkey.com" target="_blank">Contact</a>
        </small>
        <?php if (DEMO_MODE === true): ?>
            <small class="pull-right text-danger">
                <strong>DEMO MODE ENABLED</strong> v<?=system_version()?>
            </small>
        <?php else: ?>
            <small class="pull-right text-danger">
                v<?=system_version()?>
            </small>
        <?php endif; ?>
    </footer>
</div>
<!-- Javascripts -->
<script src="<?=javascripts_path('jquery-2.0.0.min')?>"></script>
<script src="<?=javascripts_path('bootstrap')?>"></script>
<script src="<?=javascripts_path('app')?>"></script>

<?php ob_end_flush(); ?>
<?php
require_once '../loader.php';
get_header('Member Area');
ensure_login();
$user = get_user();
?>
<body>
<?php get_menu(); ?>

<div class="container">

    <div class="page-header">
        <h1> <?= (! $user->last_login) ? 'Hi there, ' : 'Welcome back, ' . fullname($user); ?> </h1>
        <p class="lead">
            Welcome to your personal area of <?= system_name() ?>. Checkout the menu in the top right for things todo!
        </p>
    </div>

    <h3>Access Areas</h3>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nemo, culpa, eveniet, praesentium, delectus sequi nam 
    explicabo atque minima enim vel <code>assumenda distinctio inventore ea eos debitis architecto</code> ratione nulla. Deserunt.</p>

    <h3>Browsers, scrolling, and fixed elements</h3>
    <p>Non-responsive layouts highlight a key drawback to fixed elements. <strong class="text-danger">Any fixed component, such as a fixed navbar, will not be scrollable when the viewport becomes narrower than the page content.</strong> In other words, given the non-responsive container width of 970px and a viewport of 800px, you'll potentially hide 170px of content.</p>
    <p>There is no way around this as it's default browser behavior. The only solution is a responsive layout or using a non-fixed element.</p>

    <hr>

    <h3>Accessible Areas</h3>

    <?php 
        $pages = $user->getPrivatePages(); 
        if (!$pages):
    ?>
    <span class="text-danger">Your account does not have access to any internal links. 
        If you feel this is a mistake then please contact <a href="mailto:<?= system_email() ?>"><?= system_email() ?></a>
    </span>
    <?php else: ?>
        
    <p>Below are a list of pages that your account may access.</p>

    <ul class="list-group">
        <?php if ($pages): ?>
            <?php foreach ($pages as $page): ?>
                <li class="list-group-item">
                    <a href="<?= root_path($page->URL) ?>"><?= $page->URL ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <?php endif; ?>

</div> <!-- /container -->

<?php get_footer(); ?>
</body>
</html>

<?php
require_once '../loader.php';
get_header('Viewing Users');
ensure_login();
$user = get_user();
check_user_access($user, 'viewMembers', array(
    'redirect' => 'admin/'
));

$p = new Pagination(20, 'page');
$p->setTotal(DB::table('user')->count()->count);
$limit = explode(',', $p->getLimit());

if (!empty($_POST) && isset($_POST['search']) && !empty($_POST['search'])) {
    $searchTerm = strip_tags(trim($_POST['search']));

    // Make sure it's worth searching for
    if (strlen($searchTerm) < 3) {
        Flash::make('info', SEARCH_TERM_TOO_SHORT);
        redirect('admin/view_users.php');
    }

    $users = DB::table('user')->like('username', $searchTerm)->get();

    if (empty($users)) {
        Flash::make('info', _rd('term', $searchTerm, SEARCH_FOUND_NO_RESULTS));
        redirect('admin/view_users.php');
    }

} else {
    $users = grab_all_users($limit[0], $limit[1], 'DESC');
}

?>

<body>
<?php get_menu('home') ?>

<div class="col-sm-3 col-md-2 sidebar">
    <?php get_admin_sidebar('view-users'); ?>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <div class="navbar search-form">

        <form method="post" action="<?php echo root_path('admin/view_users.php') ?>" class="navbar-form pull-right">
            <input type="text" class="form-control input-sm" name="search" placeholder="Enter a username to search..."
                   value="<?= isset($searchTerm) ? $searchTerm : '' ?>">
            <button type="submit" class="btn btn-default btn-sm">Search</button>
        </form>
    </div>
    <!--//.navbar-->

    <?php if (!empty($users)): ?>
        <table class="table table-hover table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Registered</th>
                <th>Last Login</th>
                <th>Options</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="image"><img src="<?= get_gravatar($user->email, 30) ?>" width="30" height="30"
                                           class="gravatar" alt="<?= $user->username ?>'s Gravatar Picture"></td>
                    <td><?= $user->username ?></td>
                    <td><?= $user->email ?></td>
                    <td>
                        <?= date(TIME_FORMAT, strtotime($user->created_at)) ?>
                    </td>
                    <td>
                        <?php
                        if (!empty($user->last_login))
                            echo date(TIME_FORMAT, strtotime($user->last_login));
                        else
                            echo '-';
                        ?>
                    </td>
                    <td>
                        <a class="btn btn-success btn-sm" href="<?= root_path('admin/view.php?user=' . $user->id) ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?= $p->pageLinks() ?>
    <?php else: ?>
        <p>No users have been found.</p>
    <?php endif; ?>
</div>

<?php echo get_admin_footer(); ?>

</body>

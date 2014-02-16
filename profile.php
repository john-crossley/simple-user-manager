<?php
require_once 'loader.php';
// Make sure the user is logged in.
ensure_login();
$user = get_user(); // Right now grab the user.
get_header(fullname($user) . '\'s Profile');

// Check to see the user has posted
if (!empty($_POST) && isset($_POST['task']) && $_POST['task'] == 'saveUserDataFromUserProfile') {

    // Right it's on!
    csrf_check('profile.php');

    // Start the validation process
    $v = new Validator;
    $rules = array();
    $email_user = false;

    if ($_FILES['custom_profile_picture']['name']) {

        // We have an image perform the update.
        try {
            $result = ImageUploader::upload($_FILES['custom_profile_picture']);
        } catch (Exception $e) {
            Flash::make('danger', $e->getMessage());
            redirect('admin/view.php?user=' . $user->id);
        }

        $user->custom_image = $result;
    }

    if (isset($_POST['account_private']) && $_POST['account_private'] == 'on') {
        // The user wants to make their account private
        $user->private = 1;
    } else $user->private = 0;

    if (isset($_POST['delete_profile_picture']) && $_POST['delete_profile_picture'] == 'on') {
        // Todo remove the file...
        $user->custom_image = null;
    }

    if (isset($_POST['email_user']) && $_POST['email_user'] == 'on') {
        // The user wants to be emailed
        $email_user = true;
    }

    if (isset($_POST['personal_message']) && $_POST['personal_message'] == 'on') {
        // The user wants to recieve a message when someone sends them a personal one
        $user->notify_me_personal_message = 1;
    } else $user->notify_me_personal_message = 0;

    if (isset($_POST['receive_personal_messages']) && $_POST['receive_personal_messages'] == 'on') {
        // The user wants to recieve a message when someone sends them a personal one
        $user->receive_personal_messages = 1;
    } else $user->receive_personal_messages = 0;

    // Check to see if the user has added a name
    if (isset($_POST['fullname']) && !empty($_POST['fullname'])) {
        $names = explode(' ', $_POST['fullname']);
        $firstname = $names[0];
        $lastname = (!empty($names[1])) ? $names[1] : '';
        if ($user->firstname != $firstname || $user->lastname != $lastname) {
            $user->firstname = $firstname;
            $user->lastname = $lastname;
        }
    }

    // Right now check the email
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = strip_tags($_POST['email']);
        if ($user->email != $email) {
            $rules['email'] = array('required', 'valid_email');
            $user->email = $email;
        }
    }

    // Check the password
    if (isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['password_again']) && !empty($_POST['password_again'])
    ) {
        $password = strip_tags($_POST['password']);
        $rules['password'] = array('min:8');
        $rules['password_again'] = array('match:password');

        $user->password = $password;
        $passwordChange = $password;
    } else $passwordChange = 'No change';

    // Users BIO
    if (isset($_POST['bio']) && !empty($_POST['bio'])) {
        if ($user->bio != $_POST['bio']) {
            $user->bio = strip_tags($_POST['bio']); // Strip the tags
        }
    }

    // Users location
    if (isset($_POST['current_location']) && !empty($_POST['current_location'])) {
        if ($user->location != $_POST['current_location']) {
            $user->location = strip_tags($_POST['current_location']);
        }
    }

    $v->make($_POST, $rules);

    if ($v->fails()) {
        Flash::make('danger', GENERIC_FORM_ERROR_MESSAGE);
        redirect('profile.php');
    }

    // DEMO MODE BLOCK
    if (DEMO_MODE === true) {
        if ((int)$user->id === 1 || (int)$user->id === 2) {
            Flash::make('info', 'Your in demo mode and unable to delete some user accounts.');
            redirect('profile.php');
        }
    }
    // DEMO MODE BLOCK

    if ($user->save()) {

        $template = DB::table('template')->where('id', '=', 6)->grab(1)->get();

        if ($template) {

            if ($email_user) {

                $text = mini_parse($template->data, array(
                    'username' => $user->username,
                    'fullname' => fullname($user),
                    'user_email' => $user->email,
                    'password' => $passwordChange,
                    'status_change_message' => '',
                    'user_group' => $user->_roleName,
                    'account_private' => ($user->private) ? 'Private Account' : 'Public Account',
                    'bio' => $user->bio,
                    'location' => $user->location
                ));

                // We need to email the user right?
                $e = new Email;
                $e->to($user->email, fullname($user))
                    ->from(system_email(), meta_author())
                    ->subject($template->subject)
                    ->template(TEMPLATE . 'generic_email_template.html', array(
                        'template' => nl2br($text),
                        'system_name' => system_name(),
                        'url' => URL,
                        'year' => date('Y')
                    ))
                    ->send();
            }
        }

        // TODO: When the user is saved for some reason when they
        // select the menu it shows a standard account.. wtf man?
        //
        save_user($user); // Resave the USER MAN!
        Flash::make('success', YOUR_ACCOUNT_HAS_BEEN_UPDATED);
        redirect('profile.php');
    }

}


?>
<body>
<?php get_menu(); ?>

<div class="container main">
    <div class="row">
        <div class="col-md-3">
            <div class="thumbnail">
                <img src="<?php echo get_profile_picture($user, 240) ?>" width="240" height="240" class="gravatar"
                     alt="<?= $user->username ?>'s Profile Picture">
                <div class="caption">
                    <hgroup>
                        <h3><?= fullname($user) ?></h3>
                        <h4><?= $user->username ?> <?= get_role($user) ?></h4>
                    </hgroup>
                    <p><?php echo $user->bio; ?></p>
                    <p><i class="glyphicon glyphicon-envelope"></i> <a
                            href="mailto: <?= $user->email ?>"><?= $user->email ?></a></p>
                    <p><i class="glyphicon glyphicon-map-marker"></i> <?= $user->location ?></p>
                    <p><i class="glyphicon glyphicon-time"></i> Joined
                        on: <?= date(TIME_FORMAT, strtotime($user->created_at)) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <h2>Viewing <?= fullname($user) ?>'s Profile</h2>
            <hr>

            <form method="post" action="<?= root_path('profile.php') ?>" enctype="multipart/form-data">
                <div class="form-group has-<?= form_has_error('username') ?>">
                    <input type="hidden" name="task" value="saveUserDataFromUserProfile">
                    <input type="hidden" name="csrf" value="<?= get_csrf_token() ?>">
                    <input type="hidden" name="user_id" value="<?= $user->id ?>">
                    <label for="username" class="control-label">Username</label>

                    <p class="form-control-static"><?= $user->username ?></p>
                    <small class="help-block"><?= form_has_message('username') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group has-<?= form_has_error('fullname') ?>">
                    <label for="fullname" class="control-label">Full name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname"
                           placeholder="Enter the members full name" value="<?= fullname($user) ?>">
                    <small class="help-block"><?= form_has_message('fullname') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group has-<?= form_has_error('email') ?>">
                    <label for="email" class="control-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="Enter a valid email address" value="<?= $user->email ?>">
                    <small class="help-block"><?= form_has_message('email') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group has-<?= form_has_error('password') ?>">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter a password">
                    <small class="help-block"><?= form_has_message('password') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group has-<?= form_has_error('password_again') ?>">
                    <label for="password_again" class="control-label">Password again</label>
                    <input type="password" class="form-control" id="password_again" name="password_again"
                           placeholder="Enter a password">
                    <small class="help-block"><?= form_has_message('password_again') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group has-<?= form_has_error('bio') ?>">
                    <label for="bio" class="control-label">Bio</label>
                    <textarea class="form-control" id="bio" name="bio" cols="0" rows="0"><?= $user->bio ?></textarea>
                    <small class="help-block"><?= form_has_message('fullname') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group has-<?= form_has_error('current_location') ?>">
                    <label for="current_location" class="control-label">Current location</label>
                    <input type="text" class="form-control" id="current_location" name="current_location"
                           placeholder="Enter the users location" value="<?= $user->location ?>">
                    <small class="help-block"><?= form_has_message('current_location') ?></small>
                </div>
                <!--//.form-group-->

                <div class="form-group">
                    <label for="custom_profile_picture" class="control-label">Custom profile picture</label>
                    <input type="file" id="custom_profile_picture" name="custom_profile_picture">
                    <small class="help-block">
                        <?php $image_url = URL . 'uploads/' . $user->custom_image ?>
                        <?php if ($user->custom_image): ?>
                            <a href="<?= $image_url ?>" target="_blank"><?= $image_url ?></a>
                        <?php endif; ?>
                    </small>
                </div>

                <hr>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="account_private" <?= is_checked($user->private) ?>>
                            Make this account private?<br>
                            <small>Users will not be able to see any contact or bio information</small>
                        </label>
                    </div>
                    <!--//.checkbox-->
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="delete_profile_picture">
                            Delete profile picture?<br>
                            <small>Your picture will be removed and replaced with Gravatar (If you have one)</small>
                        </label>
                    </div>
                    <!--//.checkbox-->
                </div>

                <?php if (pm_system_enabled()): ?>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"
                                       name="personal_message" <?= is_checked($user->notify_me_personal_message) ?>>
                                Notify me when I receive a personal message<br>
                                <small>You <strong>will</strong> be notified via email</small>
                            </label>
                        </div>
                        <!--//.checkbox-->
                    </div><!--//.form-group-->

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"
                                       name="receive_personal_messages" <?= is_checked($user->receive_personal_messages) ?>>
                                I want to receive personal messages<br>
                                <small>Disabling this will <strong>turn off</strong> personal messaging</small>
                            </label>
                        </div>
                        <!--//.checkbox-->
                    </div><!--//.form-group-->
                <?php endif; ?>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="email_user" checked>
                            Update me on any changes I have made via email<br>
                            <small>This option is not saved, and is <strong>checked</strong> by default</small>
                        </label>
                    </div>
                    <!--//.checkbox-->
                </div>
                <!--//.form-group-->

                <div class="form-group">
                    <button class="btn btn-primary pull-right">Save</button>
                </div>
                <!--//.form-group-->

            </form>
        </div>
    </div>
</div>


<?= get_footer() ?>
</body>
</html>

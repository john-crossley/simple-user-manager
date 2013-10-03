<?php
require_once '../loader.php';
get_header('Templates');
ensure_login();
$user = get_user();
check_user_access($user, 'accessSettingsPanel', array('redirect' => $user->redirect_to));
$settings = get_settings();

if (isset($_POST) && isset($_POST['task']) && $_POST['task'] == 'saveTemplateSettingsFromAdminPanel') {
  $v = new Validator;

  $rules = array(
    'template_subject' => array('required', 'max:128'),
    'template_data'    => array('required'),
    'template_name'    => array('required')
  );

  $messages = array(
    'template_subject.required' => 'The template subject is required!',
    'template_data.required'    => 'You must supply some template data!'
  );

  $v->make($_POST, $rules, $messages);


  if ($v->fails()) {
    Flash::make('error', GENERIC_FORM_ERROR_MESSAGE);
    redirect('admin/templates.php');
  }

  // DEMO MODE BLOCK
  if (DEMO_MODE === true) {
    Flash::make('info', 'Your in demo mode and unable to edit templates.');
    redirect('admin/templates.php');
  }
  // DEMO MODE BLOCK

  $template_id = (int)$_POST['template_name'];

  $result = DB::table('template')->where('id', '=', $template_id)->update(array(
    'subject' => $_POST['template_subject'],
    'data'    => $_POST['template_data']
  ));

  if ($result) {
    Flash::make('success', TEMPLATE_SUCCESSFULLY_UPDATED);
    redirect('admin/templates.php');
  }
}

?>

<body>
  <?=get_menu()?>
  <div class="row">
    <div class="container main">
      <div class="col-lg-3">
        <?=get_admin_sidebar('options')?>
        <hr>
        <div id="placeholder"></div><!--//placeholder-->
      </div><!--//.col-lg-3-->

      <div class="col-lg-9">

        <h2>System Templates</h2>
        <p>Here you can modify the templates for the emails that the user receives from the system.</p>

        <form action="<?=root_path('admin/templates.php')?>" method="post">

          <div class="form-group has-<?=form_has_error('template_name')?>">
            <input type="hidden" name="task" value="saveTemplateSettingsFromAdminPanel">
            <input type="hidden" name="csrf" value="<?=get_csrf_token()?>">
            <label for="template_name" class="control-label">Select a template to edit</label>
            <select id="template_name" class="form-control" name="template_name">
              <option>-- Choose Template --</option>
              <?php foreach (get_template_list() as $template): ?>
              <option value="<?=$template->id?>"><?=$template->name?></option>
              <?php endforeach; ?>
            </select>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('template_subject')?>">
            <label for="template_subject" class="control-label">Template subject</label>
            <input type="text" class="form-control" id="template_subject" name="template_subject" placeholder="Enter the email subject" value="<?=form_has_value('template_subject')?>">
            <small class="help-block"><?=form_has_message('email')?></small>
          </div><!--//.form-group-->

          <div class="form-group has-<?=form_has_error('template_data')?>">
            <label for="template_data" class="control-label">Template data</label>
            <textarea class="form-control" id="template_data" name="template_data" cols="0" rows="0"></textarea>
            <span class="help-block"><?=form_has_message('template_data', "Some default data:
            <span class='label label-danger label-block'>{{system_name}}</span>
            <span class='label label-danger label-block'>{{year}}</span>
            <span class='label label-danger label-block'>{{url}}</span>
            ")?></span>
          </div><!--//.form-group-->

          <div class="form-group">
            <button type="submit" class="btn btn-primary pull-right" name="save">Save Template</button>
          </div><!--//.form-group-->

        </form>

      </div><!--//.col-log-9-->

    </div><!--//.container-->
  </div><!--//.row-->

  <div class="modal fade" id="privilege-reminder">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">The Permissions you Requested...</h4>
        </div>
        <div class="modal-body">
        </div><!--//.modal-body-->
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
        </div><!--//.modal-footer-->
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <?=get_footer()?>
</body>
</html>

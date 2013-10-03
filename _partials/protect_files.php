<?php
require_once '../loader.php';

if (isset($_POST['task']) && $_POST['task'] == 'directory_scan'
  && isset($_POST['path']) && !empty($_POST['path'])
  && isset($_POST['user_id']) && !empty($_POST['user_id'])) {

  $user = User::findById((int)$_POST['user_id']);

  if (!$user) die(USER_NOT_FOUND);

  $lockdown = new LockdownBuilder($_POST['path'], ROOT . 'member');

  if ($lockdown->path_not_found()) {
    die("<p class='text-danger'>Unable to find files.</p>");
  }
  $files = $lockdown->prepare_files(array('php'));

  $data = DB::table('private_pages')
              ->where('user_id', '=', $user->id)
              ->get(array('URL'));

} else die("BAD REQUEST");
?>

<table class="table table-condensed">
  <thead>
    <tr>
      <th>Allow Access</th>
      <th>Directory</th>
    </tr>
  </thead>
  <tbody>
  <input type="hidden" name="user_id" value="<?=$user->id?>">
  <?php foreach($files as $file): ?>
    <?php $file = split_file_path($file); ?>
    <tr>
      <td>
        <input type="checkbox" id="<?=$file?>" name="protect[]" value="<?=$file?>" <?=(in_array_r($file, $data)) ? 'disabled' : ''?>>
      </td>
      <td>
        <label class="checkbox-inline" for="<?=$file?>"><?=$file?></label>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<button class="btn btn-success pull-right">Protect</button>


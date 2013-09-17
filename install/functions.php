<?php
/**
 * Checks to see if a current item menu is active or not.
 * @param  integer  $current The current item step
 * @return string          The class if active.
 */
function is_active($current) {
  $step = 1; // Default
  if (isset($_GET['step']))
    $step = (int)$_GET['step'];
  if ($current === $step)
    echo 'class="active"';
}

function check_version($returnBool = false) {
  $version = (float)phpversion();
  if ($version > 5.3) {
    return ($returnBool) ? true : $version;
  }
  return ($returnBool) ? false : $version;
}

function check_license() {
  if (!isset($_COOKIE['agree_license'])) {
    Flash::make('danger', 'You must accept the license agreement.');
    header('Location: index.php');
    exit;
  }
  return true;
}

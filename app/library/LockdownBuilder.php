<?php

class LockdownBuilder {

  protected $path;
  protected $root;
  protected $full_path;
  protected $errors = array();
  protected $files  = array();

  /**
   * Prepares the LockdownBuilder class
   * @param string $path The path in question
   * @param string $root The root of the application.
   */
  public function __construct($path, $root)
  {
    // Set some varrzzz
    $this->root = $this->clean($root);
    $this->path = $this->clean($path);

    // Build the path.
    $this->full_path = $this->root . $this->path . '/';

    // Check to see if the path exists.
    if (!is_dir($this->full_path)) {
      // Store the error so we may use it
      $this->errors['PATH_NOT_FOUND'] = 'The supplied path ['.$this->path.'] does not
        exist.';
        return false;
    }

  }

  /**
   * Just cleans the supplied path information
   * @param  string $path The path to be cleaned.
   * @return string       The path cleaned up.
   */
  protected function clean($path)
  {
    return preg_replace('{/$}', '', $path);
  }

  public function path_not_found()
  {
    if (isset($this->errors['PATH_NOT_FOUND'])) {
      return true;
    }
    return false;
  }

  /**
   * Prepares all of the files for us by only returning
   * what was requested.
   * @param  array  $types The types wanted Eg: array('php', 'rb', 'html')
   * @return array        An array of files
   */
  public function prepare_files(array $types = array('*'))
  {
    // Try and read the path
    if ($h = opendir($this->full_path)) {
      while (false !== ($entry = readdir($h))) {
        // Remove the scraps love
        if ($entry == '.' || $entry == '..')
          continue;

        // Make sure it's a file with an extension
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if (empty($ext)) continue; // If not we don't need it.

        $entry = $this->clean($this->full_path) . '/' . $entry;

        if (!in_array('*', $types)) {
          if (in_array($ext, $types)) {
            $this->files[] = $entry;
          }
        } else $this->files[] = $entry;
      }
      closedir($h);
    }
    return $this->files;
  }

}

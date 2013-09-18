<?php
/**
 * Core Class
 *
 * Core is for managing all of the main application
 * grunt work.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package advanced-user-manager
 * @version 1.0
 */
class Core extends SingletonAbstract
{
  private $csrf;

  public function generateToken()
  {
    $this->csrf = strtoupper('csrf'.md5(uniqid().rand()));
    $_SESSION['CSRF_TOKEN'] = $this->csrf;
    return $this->csrf;
  }

  protected function init()
  {
    $this->generateToken();
  }

  public function getToken()
  {
    return $this->csrf;
  }
}

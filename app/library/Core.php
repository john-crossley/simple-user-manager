<?php
/**
 * Core Class
 *
 * Core is for managing all of the main application
 * grunt work. I know it could do more.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package advanced-user-manager
 * @version 1.0
 */
class Core extends SingletonAbstract
{
  /**
   * The CSRF token
   * @var string
   */
  private $csrf;

  /**
   * Generates a new CSRF token for the application and stores
   * it inside a session. The system will overwrite the token
   * each time one is requested.
   * @return string The CSRF token
   */
  public function generateToken()
  {
    $this->csrf = strtoupper('csrf'.md5(uniqid().rand()));
    $_SESSION['CSRF_TOKEN'] = $this->csrf;
    return $this->csrf;
  }

  /**
   * Calls the generateToken function when ever this
   * class is initialised.
   * @return null
   */
  protected function init()
  {
    $this->generateToken();
  }

  /**
   * Returns the CSRF token
   * @return string The CSRF token
   */
  public function getToken()
  {
    return $this->csrf;
  }
}

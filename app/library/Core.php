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
class Core
{

  protected $csrf;
  protected static $instance;

  protected function __construct(){}

  public function generateToken()
  {
    $this->csrf = strtoupper('csrf'.md5(uniqid().rand()));
    $_SESSION['CSRF_TOKEN'] = $this->csrf;
    return $this->csrf;
  }

  public static function init()
  {
    if (!self::$instance instanceof Core) {
      self::$instance = new Core;
      $core = self::$instance;
      $core->generateToken();
    }
    return self::$instance;
  }

  public function getToken()
  {
    return $this->csrf;
  }

  // /**
  //  * Stores the value of the generated CSRF token
  //  */
  // protected $csrf;

  // /**
  //  * @var $instance stores an instance of the core class.
  //  */
  // protected static $instance;

  // /**
  //  * Construct the core class, used to
  //  * prepare various properties etc.
  //  */
  // protected function __construct()
  // {
  //   $this->generateToken();
  // }

  // /**
  //  * Used to protected forms from Cross Site Request Forgery.
  //  * Because our application accepts user input it's important
  //  * we protect it against such attacks. To find out what CSRF is
  //  * and why it's important check the link below.
  //  *
  //  * Link: http://en.wikipedia.org/wiki/Cross-site_request_forgery
  //  */
  // protected function generateToken()
  // {
  //   $_SESSION['CSRFTOKEN'] = 'CSRF' . strtoupper(md5(uniqid().rand()));
  //   return $_SESSION['CSRFTOKEN'];
  // }

  // /**
  //  * Used to inisitialise one instance of the core class.
  //  */
  // protected static function init()
  // {
  //   if (!self::$instance instanceof Core) {
  //     self::$instance = new Core;
  //   }
  //   return self::$instance;
  // }

  // /**
  //  * Get the generated csrf token.
  //  */
  // public static function getToken()
  // {
  //   $core = static::init();
  //   return (isset($_SESSION['CSRFTOKEN'])) ? $_SESSION['CSRFTOKEN'] : false;
  // }

}
<?php
/**
 * Flash Class
 *
 * Used to create flash messages across the system. Similar
 * to the one used in Ruby on Rails!
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package user-manager
 * @version 1.0
 */
class Flash extends SingletonAbstract
{
  /**
   * Make a new flash message
   *
   * @param string $type The type of the error. Eg: error, notice, success
   * @param string $msg The message to display.
   */
  public static function make($type, $msg)
  {
    $_SESSION['FLASH_MESSAGE'] = array(
      'type' => $type,
      'msg' => $msg
    );
    return true;
  }

  /**
   * Return the flash message and then destory it
   * @return array Returns the flash as an array (or empty array)
   */
  public static function show()
  {
    $message = array();
    if (isset($_SESSION['FLASH_MESSAGE'])) {
      $message = $_SESSION['FLASH_MESSAGE'];
      static::destroy();
    }
    return $message;
  }

  /**
   * Destroys the flash message.
   * @return null
   */
  public static function destroy()
  {
    if (isset($_SESSION['FLASH_MESSAGE'])) {
      unset($_SESSION['FLASH_MESSAGE']);
    }
  }

}

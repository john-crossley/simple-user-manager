<?php
/**
 * Crypter class takes a standard password and generates a
 * salt along with an encrypted password. The salt is generated
 * using time() and rand() functions wrapped in SHA1. It can
 *
 * @package    AdvancedUserManager
 * @author     John Crossley <hello@phpcodemonkey.com>
 * @copyright  2013 John Crossley <phpcodemonkey>
 * @license    http://phpcodemonkey.com/license/advanced-user-manager/
 * @version    Release: 1.0
 */
class Crypter
{
  public static function prepPassword($password)
  {
    // Generate a random salt.
    $salt = sha1(time().rand());
    // Make the password
    $password = self::makePassword($password, $salt);
    // Return the data
    return array('password' => $password, 'salt' => $salt);
  }

  public static function makePassword($password, $salt)
  {
    return sha1($password.$salt);
  }

}
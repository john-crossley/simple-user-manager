<?php namespace JohnCrossley;
/**
 * Crypter class provides security when generating passwords
 * for user accounts.
 *
 * @package    JohnCrossley
 * @author     John Crossley <jonnysnip3r@gmail.com>
 * @copyright  2013 John Crossley <phpcodemonkey>
 * @license    http://johncrossley.io/license/advanced-user-manager/
 * @version    Release: 2.0
 */
class Crypter
{
    public function makePassword($password, $salt)
    {
        return sha1($password . $salt);
    }

    public function preparePassword($password)
    {
        $salt = sha1(time() . rand());
        $password = $this->makePassword($password, $salt);
        return array(
            'password' => $password,
            'salt'     => $salt
        );
    }
}
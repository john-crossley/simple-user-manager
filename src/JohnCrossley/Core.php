<?php namespace JohnCrossley;
/**
 * Core Class
 *
 * The core class offers base functionality of the system. Any house
 * keeping work can be done in here.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package simple-user-manager
 * @version 2.0
 */
class Core
{
    protected $_csrfToken = null;

    public function __construct()
    {
        $this->generateToken();
    }

    public function generateToken()
    {
        $token = strtoupper('csrf' . md5(uniqid() . rand()));
        $_SESSION['CSRF_TOKEN'] = $token;
        $this->_csrfToken = $token;
    }

    public function getToken()
    {
        return $this->_csrfToken;
    }
}
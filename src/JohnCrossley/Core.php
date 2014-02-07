<?php namespace JohnCrossley;
/**
 * Core class provides some basic core functionality to
 * the application. Any house keeping methods can be added here.
 *
 * @package    JohnCrossley
 * @author     John Crossley <jonnysnip3r@gmail.com>
 * @copyright  2013 John Crossley <phpcodemonkey>
 * @license    http://johncrossley.io/license/advanced-user-manager/
 * @version    Release: 2.0
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
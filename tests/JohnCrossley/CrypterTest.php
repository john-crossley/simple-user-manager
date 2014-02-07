<?php namespace JohnCrossley;

class CrypterTest extends \PHPUnit_Framework_TestCase
{
    private $_crypter = null;

    public function setUp()
    {
        $this->_crypter = new Crypter;
    }

    /**
     * @test
     */
    public function it_can_make_a_password()
    {
        $password = 'password';
        $salt = 'MYSALT123';
        $this->assertEquals(sha1($password . $salt), $this->_crypter->makePassword($password, $salt));
    }

    /**
     * @test
     */
    public function it_can_parepare_a_password()
    {
        $preparedPassword = $this->_crypter->preparePassword('password');
        $this->assertArrayHasKey('password', $preparedPassword);
        $this->assertArrayHasKey('salt', $preparedPassword);
    }

    public function tearDown()
    {
        $this->_crypter = null;
    }
}
<?php namespace JohnCrossley;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public $core = null;

    public function setUp()
    {
        $this->core = new Core;
    }

    /**
     * @test
     */
    public function it_generates_a_valid_csrf_token()
    {
        $this->assertRegExp('(^CSRF)', $this->core->getToken(), 'A valid CSRF token was not generated');
    }

    public function tearDown()
    {
        $this->core = null;
    }
}
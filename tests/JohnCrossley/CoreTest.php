<?php namespace JohnCrossley;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_generates_a_valid_csrf_token()
    {
        $this->assertRegExp('(^CSRF)', (new Core)->getToken(), 'A valid CSRF token was not generated');
    }
}
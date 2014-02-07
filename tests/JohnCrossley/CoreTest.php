<?php namespace JohnCrossley;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public $core = null;

    public function setUp()
    {
        $this->core = new Core;
    }

    public function tearDown()
    {
        $this->core = null;
    }
}
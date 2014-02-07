<?php namespace JohnCrossley;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    private $_dateTime = null;

    public function setUp()
    {
        $this->_dateTime = new DateTime;
    }

    /**
     * @test
     */
    public function it_can_calculate_time_since_in_words()
    {
        $dateTimeObject = new DateTime('2014-02-01T12:15:00');

        $this->assertEquals(
            '1 year ago',
            $dateTimeObject->calculateTimeSinceInWords(
                new DateTime('2013-02-01T12:15:00')
            )
        );
    }

    public function tearDown()
    {
        $this->_dateTime = null;
    }
}
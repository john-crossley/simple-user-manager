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

        $this->assertEquals(
            '2 years ago',
            $dateTimeObject->calculateTimeSinceInWords(
                new DateTime('2012-02-01T12:15:00')
            )
        );

        $this->assertEquals(
            '10 minutes ago',
            $dateTimeObject->calculateTimeSinceInWords(
                new DateTime('2014-02-01T12:05:00')
            )
        );

        $this->assertEquals(
            '3 minutes ago',
            $dateTimeObject->calculateTimeSinceInWords(
                new DateTime('2014-02-01T12:12:00')
            )
        );

        $this->assertEquals(
            '5 hours ago',
            $dateTimeObject->calculateTimeSinceInWords(
                new DateTime('2014-02-01T07:15:00')
            )
        );

        $this->assertEquals(
            '12 seconds ago',
            $dateTimeObject->calculateTimeSinceInWords(
                new DateTime('2014-02-01T12:14:48')
            )
        );
    }

    public function tearDown()
    {
        $this->_dateTime = null;
    }
}
<?php namespace JohnCrossley;
/**
 * DateTime extends the default php DateTime class slightly.
 *
 * @package    JohnCrossley
 * @author     John Crossley <jonnysnip3r@gmail.com>
 * @copyright  2013 John Crossley <phpcodemonkey>
 * @license    http://johncrossley.io/license/simple-user-manager/
 * @version    Release: 1.0
 */
class DateTime extends \DateTime
{
    public function calculateTimeSinceInWords(DateTime $dateTimeObject)
    {
        $currentDateTimeObject = $this;
        $difference = $currentDateTimeObject->diff($dateTimeObject);
        $suffix = ($difference->invert ? ' ago': '');

        $units = array(
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second'
        );

        foreach ($units as $unit => $value) {
            if ($difference->{$unit} >= 1) {
                return $this->pluraliseDate($difference->{$unit}, $value) . $suffix;
            }
        }

    }

    private function pluraliseDate($count, $unitName)
    {
        return $count . ' ' . ($count == 1 ? $unitName : $unitName . 's');
    }
}
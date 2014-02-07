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
        return '1 year ago';
    }
}
<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Classes\Server\Date;

trait TraitDatable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function getDate(string $date = 'now', string $to = Date::FORMAT, bool $isObject = false)
    {
        return Date::get($date, $to, $isObject);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function getDateDiff($date, $expire, ?string $i = null, string $to = Date::FORMAT) : int
    {
        return Date::difference($date, $expire, $i, $to);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function getTimeNow() : int
    {
        return Date::timeNow();
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function newTime($h = 0, $m = 0, $s = 0, $mt = 0, $d = 0, $y = 0) : int
    {
        return Date::newTime($h, $m, $s, $mt, $d, $y);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function setDefaultTimezone(string $timezone) : bool
    {
        return Date::setDefaultTimezone($timezone);
    }
}

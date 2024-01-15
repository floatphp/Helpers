<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\{
	Server\System,
	Filesystem\Converter
};
use FloatPHP\Helpers\Connection\Transient;

final class Debugger
{
	/**
	 * Init debug execution time.
	 * 
	 * @access public
	 * @return void
	 */
	public static function init()
	{
		if ( self::enabled() ) {
			global $appStartTime;
			$appStartTime = microtime(true);
		}
	}

	/**
	 * Set debug execution time.
	 * 
	 * @access public
	 * @return void
	 */
	public static function setExecutionTime()
	{
		if ( self::enabled() ) {
			global $appStartTime;
			if ( isset($appStartTime) ) {
				$time = (microtime(true) - $appStartTime);
				$time = Converter::toFloat($time, 3);
				(new Transient())->setTemp('--execution-time', $time, 0);
			}
		}
	}

	/**
	 * Get debug execution time.
	 * 
	 * @access public
	 * @return mixed
	 */
	public static function getExecutionTime()
	{
		return (new Transient())->getTemp('--execution-time', 0);
	}

	/**
	 * Check xdebug status.
	 * 
	 * @access public
	 * @return bool
	 */
	public static function enabled() : bool
	{
		return (bool)System::getIni('xdebug.mode');
	}
}

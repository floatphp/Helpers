<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\Server\System;
use FloatPHP\Classes\Filesystem\Converter;
use FloatPHP\Helpers\Connection\Transient;

final class Debugger
{
	/**
	 * Init debug execution time.
	 *
	 * @access public
	 * @return void
	 */
	public static function init() : void
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
	public static function setExecutionTime() : void
	{
		if ( self::enabled() ) {
			global $appStartTime;
			if ( isset($appStartTime) ) {
				$time = (microtime(true) - $appStartTime);
				$time = Converter::toFloat($time, 3);
				(new Transient())->setTemp(key: '--execution-time', value: $time, ttl: 0);
			}
		}
	}

	/**
	 * Get debug execution time.
	 *
	 * @access public
	 * @return mixed
	 */
	public static function getExecutionTime() : mixed
	{
		return (new Transient())->getTemp(key: '--execution-time');
	}

	/**
	 * Check xdebug status.
	 *
	 * @access public
	 * @return bool
	 */
	public static function enabled() : bool
	{
		return (bool)System::getIni(option: 'xdebug.mode');
	}
}

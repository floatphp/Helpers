<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Classes\Filesystem\Exception;

trait TraitThrowable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function handleException($callable)
	{
		Exception::handle($callable);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getLastError()
	{
		Exception::getLastError();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function clearLastError()
	{
		Exception::clearLastError();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function triggerError(string $error, int $type = E_USER_NOTICE) : bool
	{
		return Exception::trigger($error, $type);
	}
	
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function throwError(string $error)
	{
        Exception::throw($error);
	}
}

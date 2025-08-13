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

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Filesystem\Stringify;

trait TraitSerializable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function serialize($value) : mixed
	{
		return Stringify::serialize($value);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function unserialize($value) : mixed
	{
		return Stringify::unserialize($value);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isSerialized($value) : bool
	{
		return Stringify::isSerialized($value);
	}
}

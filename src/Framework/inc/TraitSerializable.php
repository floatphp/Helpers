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

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Classes\Filesystem\Stringify;

trait TraitSerializable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function serialize($value)
    {
        return Stringify::serialize($value);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function unserialize($value)
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

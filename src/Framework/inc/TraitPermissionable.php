<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Helpers\Framework\Permission;

trait TraitPermissionable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasRole(string $role = 'administrator', ?int $userId = null) : bool
	{
		return Permission::hasRole($role, $userId);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasCapability($capability = null, $userId = null) : bool
	{
		return Permission::hasCapability($capability, $userId);
	}
}

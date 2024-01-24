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

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\{
    Filesystem\TypeCheck,
	Filesystem\Stringify,
	Filesystem\Json,
    Http\Session
};
use FloatPHP\Helpers\Connection\{
	User, Role
};

final class Permission
{
	/**
	 * Check whether user (current) has given roles.
	 * 
	 * @access public
	 * @param mixed $role
	 * @param int $userId
	 * @return bool
	 */
	public static function hasRole($role = 'administrator', $userId = null) : bool
	{
		if ( !$role ) {
			return false;
		}

		if ( !$userId ) {
			$userId = Session::get('userId');
		}

		$u = new User();
		$r = new Role();

		$roleId = $u->getRoleId($userId);
		$slug = Stringify::lowercase($r->getSlug($roleId));

		if ( TypeCheck::isArray($role) ) {
			foreach ($role as $key => $value) {
				$role[$key] = Stringify::lowercase($value);
			}
			if ( Stringify::contains($role, $slug) ) {
				return true;
			}

		} else {
			$role = Stringify::lowercase($role);
			if ( $role === $slug ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether user (current) has given capability.
	 * 
	 * @access public
	 * @param mixed $capability
	 * @param int $userId
	 * @return bool
	 */
	public static function hasCapability($capability = null, $userId = null) : bool
	{
		if ( !$capability ) {
			$capability = ['read', 'create', 'write', 'delete'];
		}

		if ( !$userId ) {
			$userId = Session::get('userId');
		}

		$u = new User();
		$r = new Role();

		$roleId = $u->getRoleId($userId);
		$capabilities = Json::decode($r->getCapability($roleId));

		foreach ((array)$capability as $cap) {
			$cap = Stringify::lowercase($cap);
			if ( !Stringify::contains($capabilities, $cap) ) {
				return false;
			}
		}

		return true;
	}
}

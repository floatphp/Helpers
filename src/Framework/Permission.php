<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\Filesystem\{TypeCheck, Stringify, Json};
use FloatPHP\Classes\Http\Session;
use FloatPHP\Helpers\Connection\{User, Role};

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
	public static function hasRole($role = 'administrator', ?int $userId = null) : bool
	{
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
	 * @param string $capability
	 * @param int $userId
	 * @return bool
	 */
	public static function hasCapability(?string $capability = null, ?int $userId = null) : bool
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
			if ( !Stringify::contains(string: $capabilities, search: $cap) ) {
				return false;
			}
		}

		return true;
	}
}

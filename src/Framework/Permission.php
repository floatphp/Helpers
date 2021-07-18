<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Framework Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Helpers\Connection\User;
use FloatPHP\Helpers\Connection\Role;
use FloatPHP\Classes\Http\Session;
use FloatPHP\Classes\Filesystem\Stringify;
use FloatPHP\Classes\Filesystem\TypeCheck;
use FloatPHP\Classes\Filesystem\Json;

final class Permission
{
	/**
	 * @access public
	 * @param mixed $roles
	 * @param int $userId
	 * @return bool
	 */
	public static function hasRole($roles = 'administrator', $userId = null) : bool
	{
		if ( !$roles ) {
			return false;
		}
		if ( !$userId ) {
			$userId = Session::get('userId');
		}
		$u = new User();
		$r = new Role();
		$roleId = $u->getRoleId($userId);
		$slug = Stringify::lowercase($r->getSlug($roleId));
		if ( TypeCheck::isArray($roles) ) {
			foreach ($roles as $key => $value) {
				$roles[$key] = Stringify::lowercase($value);
			}
			if ( Stringify::contains($roles,$slug) ) {
				return true;
			}
		} else {
			$roles = Stringify::lowercase($roles);
			if ( $roles === $slug ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @access public
	 * @param mixed $capability
	 * @param int $userId
	 * @return bool
	 */
	public static function hasCapability($capability = null, $userId = null) : bool
	{
		if ( !$capability ) {
			$capability = ['read','create','write','delete'];
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
			if ( !Stringify::contains($capabilities,$cap) ) {
				return false;
			}
		}
		return true;
	}
}

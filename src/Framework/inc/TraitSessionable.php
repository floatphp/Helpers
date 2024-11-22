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

use FloatPHP\Classes\{
	Http\Session,
	Http\Cookie
};

trait TraitSessionable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function startSession() : void
	{
		new Session();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function setSession($key, $value = null) : void
	{
		Session::set($key, $value);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getSession($key = null) : mixed
	{
		return Session::get($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasSession($key = null) : bool
	{
		return Session::isSetted($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function registerSession($time = 60) : void
	{
		Session::register($time);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isSessionRegistered() : bool
	{
		return Session::isRegistered();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isSessionExpired() : bool
	{
		return Session::isExpired();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function closeSession() : bool
	{
		return Session::close();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function endSession() : bool
	{
		return Session::end();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getCookie(?string $key = null) : mixed
	{
		return Cookie::get($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function setCookie(string $key, $value = '', $options = []) : bool
	{
		return Cookie::set($key, $value, $options);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasCookie(?string $key = null) : bool
	{
		return Cookie::isSetted($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function clearCookie() : bool
	{
		return Cookie::clear();
	}
}

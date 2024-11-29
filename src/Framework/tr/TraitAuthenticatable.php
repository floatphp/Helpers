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

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Security\Encryption;

trait TraitAuthenticatable
{
	use TraitSessionable;

	/**
	 * Check whether user is authenticated.
	 *
	 * @access public
	 * @return bool
	 */
	public function isAuthenticated() : bool
	{
		return $this->isValidSession();
	}

	/**
	 * Check whether session is valid.
	 *
	 * @access protected
	 * @return bool
	 */
	protected function isValidSession() : bool
	{
		return $this->isSessionRegistered()
			&& !$this->isSessionExpired();
	}

	/**
	 * Get access token.
	 *
	 * @access protected
	 * @param string $token
	 * @param string $secret
	 * @return array
	 */
	protected function getAccessToken(string $token, ?string $secret = null) : array
	{
		$encryption = new Encryption($token, $secret);
		return $encryption->setPrefix()->decrypt() ?: [];
	}

	/**
	 * Set access token.
	 *
	 * @access protected
	 * @param string $user
	 * @param string $pswd
	 * @param string $secret
	 * @return string
	 */
	protected function setAccessToken(string $user, string $pswd, ?string $secret = null) : string
	{
		$data = ['user' => $user, 'pswd' => $pswd];
		$encryption = new Encryption($data, $secret);
		return $encryption->setPrefix()->encrypt();
	}
}

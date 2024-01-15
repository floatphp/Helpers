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
		return ( $this->isSessionRegistered() 
             && !$this->isSessionExpired() );
	}

	/**
	 * Get token pattern (Access).
	 *
	 * @access protected
	 * @return string
	 */
	protected function getTokenPattern() : string
	{
		return '/{user:(.*?)}{pswd:(.*?)}/';
	}

	/**
	 * Get token access (Username / Password).
	 *
	 * @access protected
	 * @param string $token
	 * @param string $secret
	 * @return string
	 */
	protected function getTokenAccess(string $token, ?string $secret = null) : string
	{
		$encryption = new Encryption($token, $secret);
        return (string)$encryption->decrypt();
	}
}

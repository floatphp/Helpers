<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.2.x
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
	 * Get token access.
	 *
	 * @access protected
	 * @param string $token
	 * @param string $secret
	 * @return array
	 */
	protected function getTokenAccess(string $token, ?string $secret = null) : array
	{
		$encryption = new Encryption($token, $secret);
        return (array)$encryption->decrypt();
	}
}

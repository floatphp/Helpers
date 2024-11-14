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

namespace FloatPHP\Helpers\Framework\hook;

use FloatPHP\Kernel\BaseController;
use FloatPHP\Classes\Filesystem\Arrayify;
use FloatPHP\Classes\Http\Server;
use FloatPHP\Helpers\Connection\Transient;

class Security extends BaseController
{
	/**
	 * Force strong password.
	 * 
	 * @access public
	 * @return object
	 */
	public function useStrongPassword() : self
	{
		$this->addFilter('auth-strong-pswd', function () {
			return true;
		});
		return $this;
	}

	/**
	 * Force token method.
	 * 
	 * @access public
	 * @return object
	 */
	public function useTokenOnly() : self
	{
		if ( !Server::getBearerToken() ) {
			$msg = $this->applyFilter('api-auth-method-msg', 'Access forbidden');
			$this->setHttpResponse($msg, [], 'error', 403);
		}
		return $this;
	}

	/**
	 * Limit login attemps.
	 * 
	 * @access public
	 * @param int $max
	 * @return object
	 */
	public function useLimitedAttempt(int $max = 3) : self
	{
		// Log failed authentication
		$this->addAction('auth-failed', function ($username) {
			if ( !empty($username) ) {
				$transient = new Transient();
				$key = "auth-{$username}";
				if ( !($attempt = $transient->getTemp($key)) ) {
					$transient->setTemp($key, 1, 0);

				} else {
					$transient->setTemp($key, $attempt + 1, 0);
				}
			}
		});

		// Apply attempts limit
		$this->addAction('authenticate', function ($username) use ($max) {
			if ( !empty($username) ) {
				$key = "auth-{$username}";
				$transient = new Transient();
				$attempt = $transient->getTemp($key);
				if ( $attempt >= $max ) {
					$msg = $this->applyFilter('auth-attempt-msg', 'Access forbidden');
					$msg = $this->translate($msg);
					$this->setResponse($msg, [], 'error', 401);
				}
			}
		});

		return $this;
	}

	/**
	 * API authentication protection.
	 *
	 * @access public
	 * @param int $max
	 * @param int $seconds
	 * @param bool $address
	 * @param bool $method
	 * @return object
	 */
	public function useAccessProtection(int $max = 120, int $seconds = 60, bool $address = true, bool $method = true) : self
	{
		$args = [
			'max'     => $max,
			'seconds' => $seconds,
			'address' => $address,
			'method'  => $method
		];

		$this->addAction('api-authenticate', function ($request = []) use ($args) {

			// Exception
			$exception = (array)$this->applyFilter('api-exception', []);
			if ( Arrayify::inArray($request['username'], $exception) ) {
				return;
			}

			// Authentication
			$transient = new Transient();
			$key = "api-auth-{$request['username']}";

			// Address check
			if ( $args['address'] ) {
				$key = "{$key}-{$request['address']}";
			}

			// Method check
			if ( $args['method'] ) {
				$key = "{$key}-{$request['method']}";
			}

			// Attempts
			$attempts = 0;
			if ( !($attempts = $transient->getTemp($key)) ) {
				$transient->setTemp($key, 1, $args['seconds']);

			} else {
				$transient->setTemp($key, $attempts + 1, $args['seconds']);
			}

			if ( $attempts >= $args['max'] && $args['max'] !== 0 ) {
				$msg = $this->applyFilter('api-auth-attempt-msg', 'Access forbidden');
				$this->setHttpResponse($msg, [], 'error', 429);
			}
		});

		return $this;
	}
}

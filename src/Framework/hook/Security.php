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

namespace FloatPHP\Helpers\Framework\hook;

use FloatPHP\Kernel\BaseController;
use FloatPHP\Classes\Filesystem\Arrayify;
use FloatPHP\Classes\Http\Server;
use FloatPHP\Helpers\Connection\Transient;

class Security extends BaseController
{
    /**
     * @access protected
     * @var int $max, Max attemps
     * @var int $seconds, Ban seconds
     * @var bool $address, Check for address
     * @var bool $method, Check for method
     */
	protected $max;
	protected $seconds;
	protected $address;
	protected $method;

	/**
	 * Force strong password.
	 * 
	 * @access public
	 * @return void
	 */
	public function useStrongPassword()
	{
		$this->addFilter('authenticate-strong-password', function(){
			return true;
		});
	}

	/**
	 * Force token method.
	 * 
	 * @access public
	 * @return void
	 */
	public function useTokenOnly()
	{
		if ( !Server::getBearerToken() ) {
			$msg = $this->applyFilter('api-authenticate-method-message', 'Access forbidden');
			$this->setHttpResponse($msg, [], 'error', 403);
		}
	}

	/**
	 * Limit login attemps.
	 * 
	 * @access public
	 * @param int $max
	 * @return void
	 */
	public function useLimitedAttempt(int $max = 3)
	{
		$this->max = $max;

		// Log failed authentication
		$this->addAction('authenticate-failed', function($username) {
			if ( !empty($username) ) {
				$transient = new Transient();
				$key = "authenticate-{$username}";
				if ( !($attempt = $transient->getTemp($key)) ) {
					$transient->setTemp($key, 1, 0);

				} else {
					$transient->setTemp($key, $attempt + 1, 0);
				}
			}
		});

		// Apply attempts limit
		$this->addAction('authenticate', function($username) {
			if ( !empty($username) ) {
				$key = "authenticate-{$username}";
				$transient = new Transient();
				$attempt = $transient->getTemp($key);
				if ( $attempt >= $this->max ) {
					$msg = $this->applyFilter('authenticate-attempt-message', 'Access forbidden');
					$msg = $this->translate($msg);
					$this->setResponse($msg, [], 'error', 401);
				}
			}
		});
	}

	/**
	 * API authentication protection.
	 *
	 * @access public
	 * @param int $max
	 * @param int $seconds
	 * @param bool $address
	 * @param bool $method
	 * @return void
	 */
	public function useAccessProtection(int $max = 120, int $seconds = 60, bool $address = true, bool $method = true)
	{
		$this->max = $max;
		$this->seconds = $seconds;
		$this->address = $address;
		$this->method = $method;

		$this->addAction('api-authenticate', function($args = []) {

			// Exception
			$exception = (array)$this->applyFilter('api-exception', []);
			if ( Arrayify::inArray($args['username'], $exception) ) {
				return;
			}

			// Authentication
			$transient = new Transient();
			$key = "api-authenticate-{$args['username']}";

			// Address check
			if ( $this->address ) {
				$key = "{$key}-{$args['address']}";
			}

			// Method check
			if ( $this->method ) {
				$key = "{$key}-{$args['method']}";
			}

			// Attempts
			$attempts = 0;
			if ( !($attempts = $transient->getTemp($key)) ) {
				$transient->setTemp($key, 1, $this->seconds);
				
			} else {
				$transient->setTemp($key, $attempts + 1, $this->seconds);
			}

			if ( $attempts >= $this->max && $this->max !== 0 ) {
				$msg = $this->applyFilter('api-authenticate-attempt-message', 'Access forbidden');
				$this->setHttpResponse($msg, [], 'error', 429);
			}
		});
	}
}

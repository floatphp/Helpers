<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\BaseController;
use FloatPHP\Classes\Filesystem\Arrayify;
use FloatPHP\Helpers\Filesystem\Transient;

class Security extends BaseController
{
	/**
	 * Force strong password.
	 * 
	 * @access public
	 * @param void
	 * @return void
	 */
	public function useStrongPassword()
	{
		$this->addFilter('authenticate-strong-password',function(){
			return true;
		});
	}

	/**
	 * Limit login attemps.
	 * 
	 * @access public
	 * @param int $max
	 * @return void
	 */
	public function useLimitedAttempt($max = 3)
	{
		// Log failed authentication
		$this->addAction('authenticate-failed',function($username){
			if ( !empty($username) ) {
				$transient = new Transient();
				$key = "authenticate-{$username}";
				if ( !($attempt = $transient->getTemp($key)) ) {
					$transient->setTemp($key,1,0);
				} else {
					$transient->setTemp($key,$attempt + 1,0);
				}
			}
		});

		// Apply attempts limit
		$this->addAction('authenticate',function($username) use ($max) {
			if ( !empty($username) ) {
				$key = "authenticate-{$username}";
				$transient = new Transient();
				$attempt = $transient->getTemp($key);
				if ( $attempt >= (int)$max ) {
					$msg = $this->applyFilter('authenticate-attempt-message','Access forbidden');
					$msg = $this->translate($msg);
					$this->setResponse($msg,[],'error',401);
				}
			}
		});
	}

	/**
	 * API Authentication protection.
	 *
	 * @access public
	 * @param int $max
	 * @param int $seconds
	 * @param bool $address
	 * @param bool $method
	 * @return void
	 */
	public function useAccessProtection($max = 120, $seconds = 60, $address = true, $method = true)
	{
		$this->addAction('api-authenticate',function($args = []) use ($max,$seconds,$address,$method){
			// Exception
			$exception = (array)$this->applyFilter('api-exception',[]);
			if ( Arrayify::inArray($args['username'],$exception) ) {
				return;
			}
			// Authentication
			$transient = new Transient();
			$key = "api-authenticate-{$args['username']}";
			if ( $address ) {
				$key = "{$key}-{$args['address']}";
			}
			if ( $method ) {
				$key = "{$key}-{$args['method']}";
			}
			$attempts = 0;
			if ( !($attempts = $transient->getTemp($key)) ) {
				$transient->setTemp($key,1,$seconds);
			} else {
				$transient->setTemp($key,$attempts + 1,$seconds);
			}
			$max = (int)$max;
			if ( $attempts >= $max && $max !== 0 ) {
				$msg = $this->applyFilter('api-authenticate-attempt-message','Access forbidden');
				$this->setHttpResponse($msg,[],'error',429);
			}
		});
	}
}

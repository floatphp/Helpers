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
	public function limitAttempts($max = 3)
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
}

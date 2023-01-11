<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.1
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Classes\{
    Filesystem\TypeCheck, Filesystem\Stringify,
    Server\Date
};
use FloatPHP\Helpers\Connection\Config;

final class Transient extends Config
{
	/**
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @param int $expire
	 * @return void
	 */
	public function setTemp($name, $value = true, $expire = 300)
	{
		$cache = $this->initCache($expire);
		$key = Stringify::formatKey($name);
		$cache->get($key);
		if ( !$cache->isCached() ) {
			$cache->set($value, '--temp');
		} else {
			$this->updateTemp($name, $value, $expire);
		}
	}

	/**
	 * @access public
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getTemp($name, $default = null)
	{
		$cache = $this->initCache(false);
		$key = Stringify::formatKey($name);
		$value = $cache->get($key);
		if ( !TypeCheck::isNull($default) && TypeCheck::isNull($value) ) {
			$value = $default;
		}
		return $value;
	}

	/**
	 * @access public
	 * @param string $name
	 * @return void
	 */
	public function deleteTemp($name)
	{
		$cache = $this->initCache(false);
		$key = Stringify::formatKey($name);
		$cache->delete($key);
	}

	/**
	 * @access public
	 * @param void
	 * @return void
	 */
	public function resetBaseTemp()
	{
		$this->deleteBase('--temp');
	}

	/**
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @param int $expire
	 * @return bool
	 */
	public function setBaseTemp($name, $value = true, $expire = 300)
	{
		$temp = $this->getBase('--temp');
		if ( empty($temp) ) {
			$temp = [];
		} else {
			$temp = Stringify::unserialize($temp);
		}
		$name = Stringify::formatKey($name);
		$temp[$name]['value'] = $value;
		if ( $expire !== 0 ) {
			$temp[$name]['created'] = Date::newTime(0, 0, $expire);
		}
		$temp = Stringify::serialize($temp);
		return $this->setBase('--temp', $temp);
	}

	/**
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function getBaseTemp($name)
	{
		$temp = $this->getBase('--temp');
		if ( empty($temp) ) {
			return null;
		} else {
			$temp = Stringify::unserialize($temp);
		}
		if ( isset($temp[$name]) ) {
			if ( isset($temp[$name]['created']) ) {
		        if ( $temp[$name]['created'] < Date::timeNow() ) {
		        	unset($temp[$name]);
		        	$temp = Stringify::serialize($temp);
		        	$this->setBase('--temp', $temp);
		            return null;
		        }
			}
			return $temp[$name]['value'];
		}
	}

	/**
	 * @access private
	 * @param string $name
	 * @param mixed $value
	 * @param int $expire
	 * @return void
	 */
	private function updateTemp($name, $value = true, $expire = 300)
	{
		$cache = $this->initCache($expire);
		$key = Stringify::formatKey($name);
		$cache->get($key);
		if ( $cache->isCached() ) {
			$cache->update($key, $value);
		}
	}
}

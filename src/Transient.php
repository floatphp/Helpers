<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Kernel Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers;

use FloatPHP\Kernel\Cache;
use FloatPHP\Classes\Filesystem\Stringify;
use FloatPHP\Classes\Server\Date;

class Transient extends ConfigProvider
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
			$cache->set($value);
		} else {
			$this->updateTemp($name,$value,$expire);
		}
	}

	/**
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function getTemp($name)
	{
		$cache = $this->initCache(false);
		$key = Stringify::formatKey($name);
		return $cache->get($key);
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
	public function resetTemp()
	{
		$this->deleteBaseOptions('temp');
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
		$temp = $this->getBaseOptions('temp');
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
		return $this->setBaseOptions('temp',$temp);
	}

	/**
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function getBaseTemp($name)
	{
		$temp = $this->getBaseOptions('temp');
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
		        	$this->setBaseOptions('temp',$temp);
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
			$cache->update($key,$value);
		}
	}
}

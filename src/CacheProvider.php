<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 */

namespace FloatPHP\Helpers;

use FloatPHP\Kernel\Cache;

abstract class CacheProvider
{
	/**
	 * @access protected
	 * @param string $name
	 * @param mixed $data
	 * @param string $group
	 * @param int $expire
	 * @return mixed
	 */
	abstract protected function set($name, $data, $group) {}

	/**
	 * @access protected
	 * @param void
	 * @return object
	 */
	protected function initCache()
	{
		Cache::expireIn(0);
		return new Cache();
	}

	/**
	 * @access protected
	 * @param string $name
	 * @param string $group
	 * @return mixed
	 */
	protected function get($name, $group)
	{
		$cache = $this->initCache();
		$value = $cache->get($name);
		if ( !$cache->isCached() ) {
			$value = $this->set($name,$data,$group);
			$cache->set($value);
		}
		return $value;
	}

	/**
	 * @access protected
	 * @param string $name
	 * @param mixed $data
	 * @param int $expire
	 * @return void
	 */
	protected function update($name, $data, $expire = 0)
	{
		$cache = $this->initCache($expire);
		$cache->update($name,$data);
	}
}

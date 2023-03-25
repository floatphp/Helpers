<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.2
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
    Filesystem\TypeCheck,
    Server\Date
};
use FloatPHP\Helpers\Connection\Config;

/**
 * Built-in Transient for FloatPHP,
 * @see Heavily inspired by WordPress kernel https://make.wordpress.org
 */
final class Transient extends Config
{
	/**
	 * @access private
	 * @var string NAME
	 */
	private const NAME = '--temp';

	/**
	 * Get temp file cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getTemp($key, $default = null)
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$value = $cache->get($key); // Set cache key
		if ( !TypeCheck::isNull($default) && TypeCheck::isNull($value) ) {
			$value = $default;
		}
		return $value;
	}

	/**
	 * Set temp file cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return void
	 */
	public function setTemp($key, $value = true, $ttl = 300)
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$cache->get($key); // Set cache key
		if ( !$cache->isCached() ) {
			$cache->set($value, static::NAME, $ttl);
			
		} else {
			$this->updateTemp($key, $value, $ttl);
		}
	}

	/**
	 * Delete temp file cache.
	 * 
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function deleteTemp($key)
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$cache->get($key); // Set cache key
		Cache::delete($key);
	}

	/**
	 * Update temp file cache.
	 * 
	 * @access private
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return void
	 */
	private function updateTemp($key, $value = true, $ttl = 300)
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$cache->get($key); // Set cache key
		if ( $cache->isCached() ) {
			$cache->set($value, static::NAME, $ttl);
		}
	}

	/**
	 * Get temp database cache.
	 * 
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function getBaseTemp($key)
	{
		$temp = $this->getBase(static::NAME);
		if ( empty($temp) ) {
			return null;

		} else {
			$temp = $this->unserialize($temp);
		}
		if ( isset($temp[$key]) ) {
			if ( isset($temp[$key]['created']) ) {
		        if ( $temp[$key]['created'] < Date::timeNow() ) {
		        	unset($temp[$key]);
		        	$temp = $this->serialize($temp);
		        	$this->setBase(static::NAME, $temp);
		            return null;
		        }
			}
			return $temp[$key]['value'];
		}
	}

	/**
	 * Set temp database cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	public function setBaseTemp($key, $value = true, $ttl = 300)
	{
		$temp = $this->getBase(static::NAME);
		if ( empty($temp) ) {
			$temp = [];
			
		} else {
			$temp = $this->unserialize($temp);
		}
		$key = $this->formatKey($key);
		$temp[$key]['value'] = $value;
		if ( $ttl !== 0 ) {
			$temp[$key]['created'] = Date::newTime(0, 0, $ttl);
		}
		$temp = $this->serialize($temp);
		return $this->setBase(static::NAME, $temp);
	}

	/**
	 * Reset temp database cache.
	 * 
	 * @access public
	 * @param void
	 * @return void
	 */
	public function resetBaseTemp()
	{
		$this->deleteBase(static::NAME);
	}
}

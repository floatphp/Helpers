<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Helpers\Filesystem\Cache;

/**
 * Built-in Transient,
 * @uses Inspired by WordPress kernel https://make.wordpress.org
 */
final class Transient
{
	use \FloatPHP\Helpers\Framework\inc\TraitConfigurable,
		\FloatPHP\Helpers\Framework\inc\TraitCacheable,
		\FloatPHP\Helpers\Framework\inc\TraitFormattable,
		\FloatPHP\Helpers\Framework\inc\TraitDatable;

	/**
	 * @access private
	 * @var string $row, Temp row name
	 * @var string $cache, Cache object
	 * @var string ROW
	 * @var string DRIVER
	 * @var string TTL
	 */
	private $row;
	private $cache;
	private const ROW    = '--temp';
	private const DRIVER = 'File';
	private const TTL    = 300;

	/**
	 * @access public
	 * @param string $row
	 * @param string $driver
	 */
	public function __construct(string $row = self::ROW, string $driver = self::DRIVER)
	{
		// Set temp row name
		$this->row = $row;

		// Init config
		$this->getConfigObject();

		// Init cache
		if ( $this->useCache ) {
			$this->cache = new Cache($driver);
		}
	}

	/**
	 * Disable cache.
	 *
	 * @access public
	 * @return object
	 */
	public function noCache() : self
	{
		$this->useCache = false;
		return $this;
	}

	/**
	 * Get temp value from database through cache,
	 * Get cached value if persistent.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get(string $key, $default = null) : mixed
	{
		$value = $this->getTemp($key, $default);
		if ( $this->isType('null', $value) ) {
			$value = $this->getBaseTemp($key, $default, true);
		}
		return $value;
	}

	/**
	 * Set temp value in database,
	 * Add cache layer if persistent.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	public function set(string $key, $value = true, int $ttl = self::TTL) : bool
	{
		if ( $this->setBaseTemp($key, $value, $ttl) ) {
			if ( $ttl == 0 ) {
				$this->setTemp($key, $value, 0);
			}
			return true;
		}
		return false;
	}

	/**
	 * Delete temp value from database and cache.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function delete(string $key) : bool
	{
		$this->deleteTemp($key);
		return $this->deleteBaseTemp($key);
	}

	/**
	 * Get temp value from cache.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function getTemp(string $key) : mixed
	{
		if ( !$this->useCache ) {
			return null;
		}
		return $this->cache->get($key);
	}

	/**
	 * Set temp value in cache.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	public function setTemp(string $key, $value = true, int $ttl = self::TTL) : bool
	{
		if ( !$this->useCache ) {
			return false;
		}
		return $this->cache->set($key, $value, $ttl, $this->row);
	}

	/**
	 * Delete temp value from cache.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function deleteTemp(string $key) : bool
	{
		if ( !$this->useCache ) {
			return false;
		}
		return $this->cache->delete($key);
	}

	/**
	 * Get temp value from database.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $default
	 * @param bool $persistent
	 * @return mixed
	 */
	public function getBaseTemp(string $key, $default = null, bool $persistent = false) : mixed
	{
		$temp = $this->getConfigValue($this->row);

		if ( empty($temp) ) {
			return null;
		}

		$temp = $this->unserialize($temp);
		$key = $this->formatKey($key);

		if ( isset($temp[$key]) ) {

			$value = $temp[$key]['value'];

			if ( isset($temp[$key]['created']) ) {
				if ( $temp[$key]['created'] < $this->getTimeNow() ) {
					unset($temp[$key]);
					$temp = $this->serialize($temp);
					$this->setConfigValue($this->row, $temp);
					return null;
				}

			} else {
				if ( $persistent ) {
					$this->setTemp($key, $value, 0);
				}
			}

			return $value;
		}

		if ( !$this->isType('null', $default) ) {
			return $default;
		}
	}

	/**
	 * Set temp value in database.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	public function setBaseTemp(string $key, $value = true, int $ttl = self::TTL) : bool
	{
		$temp = $this->getConfigValue($this->row);

		if ( empty($temp) ) {
			$temp = [];

		} else {
			$temp = $this->unserialize($temp);
		}

		$key = $this->formatKey($key);
		$temp[$key]['value'] = $value;
		if ( $ttl !== 0 ) {
			$temp[$key]['created'] = $this->newTime(0, 0, $ttl);
		}
		$temp = $this->serialize($temp);

		return $this->setConfigValue($this->row, $temp);
	}

	/**
	 * Delete temp value from database.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	public function deleteBaseTemp(string $key) : bool
	{
		if ( empty($temp = $this->getConfigValue($this->row)) ) {
			return false;
		}

		if ( empty($temp = $this->unserialize($temp)) ) {
			return false;
		}

		$key = $this->formatKey($key);
		if ( isset($temp[$key]) ) {
			unset($temp[$key]);
			$temp = $this->serialize($temp);
			return $this->setConfigValue($this->row, $temp);
		}

		return false;
	}

	/**
	 * Reset database temp row.
	 *
	 * @access public
	 * @return bool
	 */
	public function resetBaseTemp() : bool
	{
		return $this->deleteConfigValue($this->row);
	}
}

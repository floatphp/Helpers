<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Exceptions\Helpers\CacheException;
use FloatPHP\Helpers\Filesystem\cache\{FileCache, RedisCache};

/**
 * Built-in cache factory.
 */
class Cache
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access protected
	 * @var object $instance, Cache instance
	 * @var string $group, Cache group
	 * @var bool $validate, Validate cache value
	 * @var bool $persist, Cache persist
	 * @var bool $debug, Cache debug
	 * @var object DRIVERS, Cache drivers
	 */
	protected static $instance;
	protected $group = null;
	protected $validate = false;
	protected $persist = false;
	protected $debug = false;
	protected const DRIVERS = ['File', 'Redis'];

	/**
	 * Instance cache driver.
	 *
	 * @access public
	 * @param string $driver
	 * @param array $config
	 */
	public function __construct(string $driver = 'File', array $config = [])
	{
		if ( !self::$instance ) {

			if ( !$this->inArray($driver, self::DRIVERS) ) {
				throw new CacheException(
					CacheException::invalidCacheDriver($driver)
				);
			}

			if ( $driver == 'Redis' ) {
				self::$instance = new RedisCache($config);

			} else {
				self::$instance = new FileCache($config);
			}

			if ( !$this->hasObject('interface', self::$instance, 'Cache') ) {
				throw new CacheException(
					CacheException::invalidCacheInstance()
				);
			}

		}
	}

	/**
	 * @inheritdoc
	 */
	public function get(string $key, ?bool &$status = null) : mixed
	{
		$key = $this->slugify($key);
		return self::$instance->get($key, $status);
	}

	/**
	 * @inheritdoc
	 */
	public function has(string $key) : bool
	{
		$key = $this->slugify($key);
		return self::$instance->has($key);
	}

	/**
	 * @inheritdoc
	 */
	public function set(string $key, $value, ?int $ttl = null, ?string $group = null) : bool
	{
		if ( ($this->validate && !$value) || $this->debug ) {
			return false;
		}

		if ( $this->persist ) {
			$ttl = 0;
		}

		$group = $group ?: $this->group;
		$group = $group ? $this->slugify($this->basename($group)) : null;

		$key = $this->slugify($key);
		return self::$instance->set($key, $value, $ttl, $group);
	}

	/**
	 * @inheritdoc
	 */
	public function delete(string $key) : bool
	{
		$key = $this->slugify($key);
		return self::$instance->delete($key);
	}

	/**
	 * @inheritdoc
	 */
	public function purge(?string $group = null) : bool
	{
		$group = $group ?: $this->group;
		$group = $group ? $this->slugify($this->basename($group)) : null;

		return self::$instance->purge($group);
	}

	/**
	 * Disable cache.
	 *
	 * @inheritdoc
	 */
	public function debug(?string $group = null) : self
	{
		$this->debug = true;
		$this->purge($group);
		return $this;
	}

	/**
	 * Validate cache value.
	 *
	 * @inheritdoc
	 */
	public function validate() : self
	{
		$this->validate = true;
		return $this;
	}

	/**
	 * Persist cache value.
	 *
	 * @inheritdoc
	 */
	public function persist() : self
	{
		$this->persist = true;
		return $this;
	}

	/**
	 * Set cache group.
	 *
	 * @inheritdoc
	 */
	public function setGroup(string $group) : self
	{
		$this->group = $group;
		return $this;
	}

	/**
	 * Get cache key.
	 *
	 * @access public
	 * @param string $item
	 * @param array $args
	 * @return string
	 */
	public function getKey(string $item = '--temp', array $args = []) : string
	{
		$parts = [$item];

		foreach ($args as $name => $value) {

			if ( $this->isType('array', $value) ) {
				$nested = $this->getKey((string)$name, $value);
				$parts[] = $nested;
				continue;
			}

			// Convert all types to string
			$value = match (true) {
				$this->isType('zero', $value)  => '0',
				$this->isType('false', $value) => 'false',
				$this->isType('true', $value)  => 'true',
				$this->isType('null', $value)  => null, // Skip
				$this->isType('empty', $value) => null, // Skip
				default                        => (string)$value
			};

			if ( !$this->isType('null', $value) ) {
				$parts[] = $name !== 0 ? "{$name}-{$value}" : $value;
			}
		}

		// Slugify parts
		return $this->slugify(implode('-', $parts));
	}

	/**
	 * Clear cache (force remove files).
	 *
	 * @access public
	 * @return bool
	 */
	public function clear() : bool
	{
		$this->initConfig();
		return $this->clearDir(
			$this->getCachePath()
		);
	}
}

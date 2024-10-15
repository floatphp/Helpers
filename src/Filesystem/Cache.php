<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Exceptions\Helpers\CacheException;
use FloatPHP\Helpers\Filesystem\cache\{
	FileCache, RedisCache
};

/**
 * Built-in cache factory.
 */
class Cache
{
	use \FloatPHP\Helpers\Framework\inc\TraitFormattable;

	/**
	 * @access private
	 * @var object $instance, Cache instance
	 * @var object DRIVERS, Cache drivers
	 */
	private static $instance;
	private const DRIVERS = ['File', 'Redis'];

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

			if ( !$this->hasItem('interface', self::$instance, 'Cache') ) {
				throw new CacheException(
					CacheException::invalidCacheInstance()
				);
			}
			
		}
	}

	/**
	 * @inheritdoc
	 */
	public function get(string $key, ?bool &$status = null)
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
	public function purge() : bool
	{
		return self::$instance->purge();
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
		$key = $item;

		foreach ($args as $name => $value) {

			if ( $this->isType('array', $value) 
			  || $this->isType('null', $value) 
			  || $this->isType('empty', $value) ) {
				continue;
			}

			if ( $value === 0 ) {
				$value = '0';

			} elseif ( $this->isType('false', $value) ) {
				$value = 'false';

			} elseif ( $this->isType('true', $value) ) {
				$value = 'true';
			}

			$key .= "-{$name}-{$value}";
		}

		return $this->slugify($key);
	}
}

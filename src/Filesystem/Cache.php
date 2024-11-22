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
	use \FloatPHP\Helpers\Framework\inc\TraitFormattable;

	/**
	 * @access protected
	 * @var object $instance, Cache instance
	 * @var bool $validate, Validate cache value
	 * @var bool $debug, Cache debug
	 * @var object DRIVERS, Cache drivers
	 */
	protected static $instance;
	protected $validate = false;
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

			if (
				$this->isType('array', $value)
				|| $this->isType('null', $value)
				|| $this->isType('empty', $value)
			) {
				continue;
			}

			if ( $value === 0 ) {
				$value = '0';

			} elseif ( $this->isType('false', $value) ) {
				$value = 'false';

			} elseif ( $this->isType('true', $value) ) {
				$value = 'true';
			}

			if ( $name !== 0 ) {
				$key .= "-{$name}";
			}
			$key .= "-{$value}";
		}

		return $this->slugify($key);
	}
}

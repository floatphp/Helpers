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

use FloatPHP\Helpers\Filesystem\cache\{
	FileCache,
	RedisCache,
	ViewCache
};
use FloatPHP\Helpers\Http\Catcher;
use FloatPHP\Exceptions\Helpers\CacheException;

/**
 * Built-in cache factory class.
 */
class Cache
{
	use \FloatPHP\Helpers\Framework\inc\TraitFormattable,
		\FloatPHP\Helpers\Framework\inc\TraitRequestable;

	/**
	 * @access private
	 * @var object $instance, Cache instance
	 * @var object DRIVERS, Valid drivers
	 */
	private $instance;
	private const DRIVERS = ['Files', 'Redis'];

	/**
	 * Instance cache driver.
	 * 
	 * @access public
	 * @param string $driver
	 * @param array $config
	 */
	public function __construct(string $driver = 'Files', array $config = [])
	{
		// Check driver
		if ( !$this->inArray($driver, self::DRIVERS) ) {
	        throw new CacheException(
	            CacheException::invalidCacheDriver($driver)
	        );
		}

		// Instance driver
		if ( $driver == 'Redis' ) {
			$this->instance = new RedisCache($config);

		} else {
			$this->instance = new FileCache($config);
		}

		// Check instance
		if ( !$this->hasItem('interface', $this->instance, 'cache') ) {
	        throw new CacheException(
	            CacheException::invalidCacheInstance()
	        );
		}
	}

	/**
	 * Get cache.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->instance->get(
			$this->formatKey($key)
		);
	}

	/**
	 * Set cache key.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function setKey($key)
	{
		$this->get($key);
	}

	/**
	 * Check cache.
	 *
	 * @access public
	 * @return bool
	 */
	public function isCached() : bool
	{
		return $this->instance->isCached();
	}

	/**
	 * Set cache.
	 * 
	 * @access public
	 * @param mixed $value
	 * @param string $tag
	 * @param mixed $ttl
	 * @return bool
	 */
	public function set($value, ?string $tag = null, $ttl = null) : bool
	{
		return $this->instance->set($value, $tag, $ttl);
	}

	/**
	 * Delete cache by key.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function delete($key) : bool
	{
		$status = 0;
		if ( $this->isType('array', $key) ) {
			foreach ($key as $k) {
				$status += (int)$this->instance->delete(
					$this->formatKey($k)
				);
			}

		} else {
			$status += (int)$this->instance->delete(
				$this->formatKey($key)
			);
		}
		return (bool)$status;
	}

	/**
	 * Delete cache by tag.
	 * 
	 * @access public
	 * @param mixed $tag
	 * @return bool
	 */
	public function deleteByTag($tag) : bool
	{
		$status = 0;
		if ( $this->isType('array', $tag) ) {
			foreach ($tag as $t) {
				$status += (int)$this->instance->deleteByTag(
					$this->formatKey($t)
				);
			}

		} else {
			$status += (int)$this->instance->deleteByTag(
				$this->formatKey($tag)
			);
		}
		return (bool)$status;
	}

	/**
	 * Purge cache (all).
	 * 
	 * @access public
	 * @return bool
	 */
	public function purge() : bool
	{
		return $this->instance->purge();
	}

	/**
	 * Reset instance.
	 *
	 * @access public
	 * @return void
	 */
	public function reset()
	{
		$this->instance->reset();
	}

	/**
	 * Purge view cache.
	 * 
	 * @access public
	 * @return bool
	 */
	public function purgeView() : bool
	{
		$cache = new ViewCache();
		return (bool)$cache->purge();
	}

	/**
	 * Purge path cache.
	 * 
	 * @access public
	 * @return bool
	 */
	public function purgePath() : bool
	{
		$cache = new FileCache();
		return (bool)$cache->purgePath();
	}

	/**
	 * Generate cache key.
	 * 
	 * @access public
	 * @param string $item
	 * @param bool $request
	 * @param array $args
	 * @return string
	 */
	public function generateKey($item = '', $request = true, $args = []) : string
	{
		$args = ($request !== false) ? $this->getRequest() : $args;
		$key = !empty($item) ? $item : '--temp';
		
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

		return $key;
	}

    /**
     * Purge cache by request.
     * 
     * @access public
     * @param array $request
     * @return bool
     */
    public static function auto(array $request = []) : bool
    {
		$catcher = new Catcher($request, 'cache');
		
    	$key   = $catcher->key;
    	$type  = ($catcher->type) ? $catcher->type : 'tag';
        $count = 0;

        foreach (self::DRIVERS as $driver) {

        	$cache = new self($driver);
        	if ( $driver == 'Files' ) {
		        switch ($type) {
		            case 'view':
		                $count += (int)$cache->purgeView();
		                break;
		            case 'path':
		                $count += (int)$cache->purgePath();
		                break;
		        }
        	}
	        switch ($type) {
	            case 'key':
	                $count += (int)$cache->delete($key);
	                break;
	            case 'tag':
	                $count += (int)$cache->deleteByTag($key);
	                break;
	            case 'all':
	                $count += (int)$cache->purge();
	                break;
	        }
        }

        return (bool)$count;
    }
}

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
	Filesystem\Stringify,
	Filesystem\Arrayify,
	Http\Request
};
use FloatPHP\Helpers\Filesystem\cache\{
	FileCache,
	RedisCache,
	ViewCache
};
use FloatPHP\Exceptions\Helpers\CacheException;

/**
 * Cache helper class.
 */
class Cache
{
	/**
	 * @access private
	 * @var object $instance, Cache instance
	 * @var array DRIVERS, Valid drivers
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
		if ( !Arrayify::inArray($driver, self::DRIVERS) ) {
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
		if ( !TypeCheck::hasInterface($this->instance, 'CacheInterface') ) {
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
	 * Check cache.
	 *
	 * @access public
	 * @param void
	 * @return bool
	 */
	public function isCached(): bool
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
	public function set($value, $tag = null, $ttl = null): bool
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
	public function delete($key = ''): bool
	{
		$status = 0;
		if ( TypeCheck::isArray($key) ) {
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
	public function deleteByTag($tag = ''): bool
	{
		$status = 0;
		if ( TypeCheck::isArray($tag) ) {
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
	 * @param void
	 * @return bool
	 */
	public function purge(): bool
	{
		return $this->instance->purge();
	}

	/**
	 * Reset instance.
	 *
	 * @access public
	 * @param void
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
	 * @param void
	 * @return bool
	 */
	public function purgeView(): bool
	{
		$cache = new ViewCache();
		return (bool)$cache->purge();
	}

	/**
	 * Purge path cache.
	 * 
	 * @access public
	 * @param void
	 * @return bool
	 */
	public function purgePath(): bool
	{
		$cache = new FileCache();
		return (bool)$cache->purgePath();
	}

    /**
     * Purge cache by request.
     * 
     * @access public
     * @param array $request
     * @return bool
     */
    public static function auto($request = []): bool
    {
        if ( !$request ) {
            $request = Request::get('cache');
        }

        $type = $request['type'] ?? 'tag';
        $id = $request['id'] ?? false;

        $id = Stringify::stripSpace($id);
        if ( Stringify::contains($id, ',') ) {
            $id = explode(',', $id);
        }

        foreach (self::DRIVERS as $driver) {
        	$cache = new self($driver);
        	if ( $driver == 'Files' ) {
		        switch ($type) {
		            // View
		            case 'view':
		                return $cache->purgeView();
		                break;
		            // Path
		            case 'path':
		                return $cache->purgePath();
		                break;
		        }
        	}
	        switch ($type) {
	            // Key
	            case 'key':
	                return $cache->delete($id);
	                break;
	            // Tag
	            case 'tag':
	                return $cache->deleteByTag($id);
	                break;
	            // All
	            case 'all':
	                return $cache->purge();
	                break;
	        }
        }

        return false;
    }

	/**
	 * Format key.
	 * 
	 * @access private
	 * @param string $key
	 * @return string
	 */
	private function formatKey($key) : string
	{
		return Stringify::formatKey((string)$key);
	}
}

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

use FloatPHP\Classes\Filesystem\{
	TypeCheck, Stringify, Arrayify, File,
	Exception as ErrorHandler
};
use Phpfastcache\{
	CacheManager,
	Drivers\Files\Config,
	Exceptions\PhpfastcacheIOException
};
use \Exception;

/**
 * Wrapper class for external FileCache.
 * 
 * @see https://www.phpfastcache.com
 */
class FileCache
{
	/**
	 * @access private
	 * @var object $cache
	 * @var object $adapter
	 * @var array $config
	 * @var int $ttl
	 */
	private $cache = false;
	private $adapter = false;
	private $config = [];
	private $ttl;

	/**
	 * @param array $config
	 * @param int $ttl
	 */
	public function __construct(array $config = [], $ttl = 5)
	{
		// Set cache ttl
		$this->ttl = (int)$ttl;

		// Set cache config
		$this->config = Arrayify::merge([
			'path'                   => 'cache',
			'secureFileManipulation' => false,
			'autoTmpFallback'        => true,
			'compressData'           => true,
			'preventCacheSlams'      => true,
			'defaultChmod'           => 0755,
			'securityKey'            => 'private',
			'cacheFileExtension'     => 'db'
		], $config);

		// Set adapter default config
		CacheManager::setDefaultConfig(
			new Config($this->config)
		);

		// Init adapter
		$this->reset();

		try {
			
			$this->adapter = CacheManager::getInstance('Files');

		} catch (Exception $e) {
			ErrorHandler::clearLastError();

		} catch (PhpfastcacheIOException $e) {
			ErrorHandler::clearLastError();
		}
	}

	/**
	 * Clear adapter instances.
	 */
	public function __destruct()
	{
		$this->reset();
	}

	/**
	 * Get cache by key.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		try {

			if ( $this->adapter ) {
				$key = $this->formatKey($key);
				$this->cache = $this->adapter->getItem($key);
				return $this->cache->get();
			}
			
		} catch (Exception $e) {
			ErrorHandler::clearLastError();

		} catch (PhpfastcacheIOException $e) {
			ErrorHandler::clearLastError();
		}

		return false;
	}

	/**
	 * Set cache by tags.
	 *
	 * @access public
	 * @param mixed $value
	 * @param mixed $tags
	 * @return bool
	 */
	public function set($value, $tags = null) : bool
	{
		try {

			if ( $this->adapter ) {
				$this->cache->set($value)
				->expiresAfter($this->ttl);
				if ( $tags ) {
					if ( TypeCheck::isArray($tags) ) {
						foreach ($tags as $key => $value) {
							$tags[$key] = $this->formatKey($value);
						}
						$this->cache->addTags($tags);
					} else {
						$tags = $this->formatKey($tags);
						$this->cache->addTag($tags);
					}
				}
				return $this->adapter->save($this->cache);
			}

		} catch (Exception $e) {
			ErrorHandler::clearLastError();

		} catch (PhpfastcacheIOException $e) {
			ErrorHandler::clearLastError();
		}

		return false;
	}

	/**
	 * Update cache by key.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function update($key, $value) : bool
	{
		try {

			if ( $this->adapter ) {
				$key = $this->formatKey($key);
				$this->cache = $this->adapter->getItem($key);
				$this->cache->set($value)
				->expiresAfter($this->ttl);
				return $this->adapter->save($this->cache);
			}

		} catch (Exception $e) {
			ErrorHandler::clearLastError();

		} catch (PhpfastcacheIOException $e) {
			ErrorHandler::clearLastError();
		}

		return false;
	}

	/**
	 * Delete cache by key.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function delete($key) : bool
	{
		try {

			if ( $this->adapter ) {
				$key = $this->formatKey($key);
				return $this->adapter->deleteItem($key);
			}
			
		} catch (Exception $e) {
			ErrorHandler::clearLastError();

		} catch (PhpfastcacheIOException $e) {
			ErrorHandler::clearLastError();
		}
		
		return false;
	}

	/**
	 * Delete cache by tags.
	 *
	 * @access public
	 * @param mixed $tags
	 * @return bool
	 */
	public function deleteByTag($tags) : bool
	{
		try {

			if ( $this->adapter ) {
				if ( TypeCheck::isArray($tags) ) {
					foreach ($tags as $key => $value) {
						$tags[$key] = $this->formatKey($value);
					}
					return $this->adapter->deleteItemsByTags($tags);
				} else {
					$tags = $this->formatKey($tags);
					return $this->adapter->deleteItemsByTag($tags);
				}
			}
			
		} catch (Exception $e) {
			ErrorHandler::clearLastError();

		} catch (PhpfastcacheIOException $e) {
			ErrorHandler::clearLastError();
		}

		return false;
	}

	/**
	 * Check cache.
	 *
	 * @access public
	 * @param void
	 * @return bool
	 */
	public function isCached() : bool
	{
		if ( $this->cache ) {
			return $this->cache->isHit();
		}
		return false;
	}

	/**
	 * Get cache TTL.
	 *
	 * @access public
	 * @param void
	 * @return mixed
	 */
	public function getTTL()
	{
		if ( $this->cache ) {
			return $this->cache->getTtl();
		}
		return false;
	}
	
	/**
	 * Set filecache TTL.
	 *
	 * @access public
	 * @param int
	 * @return void
	 */
	public function setTTL($ttl = 5)
	{
		$this->ttl = (int)$ttl;
	}
	
	/**
	 * Get cache tags.
	 *
	 * @access public
	 * @param void
	 * @return mixed
	 */
	public function getTags()
	{
		if ( $this->cache ) {
			return $this->cache->getTags();
		}
		return false;
	}

	/**
	 * Purge filecache.
	 *
	 * @access public
	 * @param void
	 * @return bool
	 */
	public function purge() : bool
	{
		return File::clearDir($this->config['path']);
	}

	/**
	 * Format cache key.
	 *
	 * @access protected
	 * @param int|string $key
	 * @return string
	 */
	protected function formatKey($key)
	{
		return Stringify::formatKey($key);
	}

	/**
	 * Reset cache instance.
	 *
	 * @access protected
	 * @param void
	 * @return void
	 */
	protected function reset()
	{
		CacheManager::clearInstances();
	}
}

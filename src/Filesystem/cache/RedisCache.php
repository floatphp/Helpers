<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem\cache;

use FloatPHP\Interfaces\Helpers\CacheInterface;
use FloatPHP\Helpers\Filesystem\Logger;
use FloatPHP\Classes\Filesystem\{
	Arrayify, Exception as ErrorHandler
};
use Phpfastcache\{
	CacheManager,
	Drivers\Redis\Config,
	Proxy\PhpfastcacheAbstractProxy
};

/**
 * Wrapper class for RedisCache.
 * @see https://www.phpfastcache.com
 */
final class RedisCache extends PhpfastcacheAbstractProxy implements CacheInterface
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access private
	 * @var object $temp, Temp cache object
	 * @var bool $isCached, Cache status
	 */
	private $temp;
	private $isCached = false;
	
	/**
	 * @param array $config
	 */
    public function __construct(array $config = [])
    {
		// Reset instance
		CacheManager::clearInstances();

		// Init configuration
		$this->initConfig();

		// Init path
		$config = Arrayify::merge([
			'host'       => '127.0.0.1',
			'port'       => 6379,
			'password'   => '',
			'database'   => 0,
			'defaultTtl' => $this->getCacheTTL()
		], $config);

		if ( isset($config['path']) ) {
			unset($config['path']);
		}

		// Init instance
		try {
			$this->instance = CacheManager::getInstance('Redis', new Config($config));

		} catch (\Phpfastcache\Exceptions\PhpfastcacheDriverConnectException $e) {

			ErrorHandler::clearLastError();
			$logger = new Logger('core', 'system');
			$logger->error('Redis cache failed');
			if ( $this->isDebug() ) {
				$logger->debug($e->getMessage());
			}

		} catch (\Phpfastcache\Exceptions\PhpfastcacheDriverCheckException $e) {

			ErrorHandler::clearLastError();
			$logger = new Logger('core', 'system');
			$logger->error('Redis cache driver failed');
			if ( $this->isDebug() ) {
				$logger->debug($e->getMessage());
			}
		}

		// Set backup instance
		if ( !$this->instance ) {
			$this->instance = CacheManager::getInstance('Files');
		}

		// Reset configuration
		$this->resetConfig();
    }

	/**
	 * Get cache.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key)
	{
		$this->temp = $this->instance->getItem($key);
		$this->isCached = $this->temp->isHit();
		return $this->temp->get();
	}

	/**
	 * Check cache.
	 *
	 * @access public
	 * @return bool
	 */
	public function isCached() : bool
	{
		return $this->isCached;
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
	public function set($value, ?string $tag = null, ?int $ttl = null) : bool
	{
		$this->temp->set($value);
		if ( $ttl ) {
			$this->temp->expiresAfter($ttl);
		}
		if ( $tag ) {
			$this->temp->addTag($tag);
		}
		return $this->instance->save($this->temp);
	}

	/**
	 * Delete cache by key.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function delete(string $key) : bool
	{
		return $this->instance->deleteItem($key);
	}

	/**
	 * Delete cache by tag.
	 *
	 * @access public
	 * @param string $tag
	 * @return bool
	 */
	public function deleteByTag(string $tag) : bool
	{
		return $this->instance->deleteItemsByTag($tag);
	}

	/**
	 * Purge cache (all).
	 *
	 * @access public
	 * @return bool
	 */
	public function purge() : bool
	{
		return $this->instance->clear();
	}
}

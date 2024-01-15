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

namespace FloatPHP\Helpers\Filesystem\cache;

use FloatPHP\Interfaces\Helpers\CacheInterface;
use FloatPHP\Helpers\Filesystem\Logger;
use FloatPHP\Classes\Filesystem\{
	Stringify, Arrayify, File, Exception as ErrorHandler
};
use Phpfastcache\{
	CacheManager,
	Drivers\Files\Config,
	Proxy\PhpfastcacheAbstractProxy
};

/**
 * Wrapper class for FileCache.
 * @see https://www.phpfastcache.com
 */
class FileCache extends PhpfastcacheAbstractProxy implements CacheInterface
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

		// Init config
		$config = Arrayify::merge([
			'path'               => 'temp',
			'autoTmpFallback'    => true,
			'compressData'       => true,
			'preventCacheSlams'  => true,
			'cacheSlamsTimeout'  => 5,
			'defaultChmod'       => 0777,
			'defaultTtl'         => $this->getCacheTTL(),
			'securityKey'        => 'private',
			'cacheFileExtension' => 'txt'
		], $config);

		// Init path
		$config['path'] = "{$this->getCachePath()}/{$config['path']}";

		// Init instance
		try {
			$this->instance = CacheManager::getInstance('Files', new Config($config));

		} catch (\Phpfastcache\Exceptions\PhpfastcacheIOException $e) {

			ErrorHandler::clearLastError();
			$logger = new Logger('core', 'system');
			$logger->error('File cache failed');
			if ( $this->isDebug() ) {
				$logger->debug($e->getMessage());
			}
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
	 * @param int $ttl
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

	/**
	 * Purge cache path.
	 *
	 * @access public
	 * @return bool
	 */
	public function purgePath() : bool
	{
		if ( Stringify::contains($this->getCachePath(), '/cache/') ) {
			return File::clearDir($this->getCachePath());
		}
		return false;
	}
}

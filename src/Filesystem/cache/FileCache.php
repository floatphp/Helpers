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

namespace FloatPHP\Helpers\Filesystem\cache;

use FloatPHP\Interfaces\Helpers\CacheInterface;
use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Files\Config;

/**
 * Wrapper class for FileCache.
 * @see https://www.phpfastcache.com
 */
final class FileCache extends ProxyCache implements CacheInterface
{
	/**
	 * @access private
	 * @var bool $initialized
	 */
	private static $initialized = false;

	/**
	 * @inheritdoc
	 */
	public function __construct(array $config = [])
	{
		if ( !static::$initialized ) {

			$this->initConfig();

			$config = $this->mergeArray([
				'path'               => "{$this->getCachePath('temp')}/temp",
				'autoTmpFallback'    => true,
				'preventCacheSlams'  => true,
				'cacheSlamsTimeout'  => 3,
				'defaultChmod'       => 0777,
				'defaultTtl'         => $this->getCacheTTL(),
				'securityKey'        => 'private',
				'cacheFileExtension' => 'txt'
			], $config);

			try {

				$this->instance = CacheManager::getInstance('Files', new Config($config));

			} catch (\Phpfastcache\Exceptions\PhpfastcacheIOException $e) {

				$this->clearLastError();

				if ( $this->isDebug() ) {
					$this->error('File cache failed');
					$this->debug($e->getMessage());
				}
			}

		}
	}
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.2.x
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
use Phpfastcache\Drivers\Redis\Config;

/**
 * Wrapper class for RedisCache.
 * @see https://www.phpfastcache.com
 */
final class RedisCache extends ProxyCache implements CacheInterface
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

			unset($config['path']);
			$config = $this->mergeArray([
				'host'       => '127.0.0.1',
				'port'       => 6379,
				'password'   => '',
				'database'   => 0,
				'timeout'    => 1,
				'defaultTtl' => $this->getCacheTTL()
			], $config);
	
			try {
				$this->instance = CacheManager::getInstance('Redis', new Config($config));
	
			} catch (
				\Phpfastcache\Exceptions\PhpfastcacheDriverConnectException |
				\Phpfastcache\Exceptions\PhpfastcacheDriverCheckException $e
			) {
	
				$this->clearLastError();

				if ( $this->isDebug() ) {
					$this->error('Redis cache failed');
					$this->debug($e->getMessage());
				}
			}

			if ( !$this->instance ) {
				$this->instance = new FileCache();
			}
			
		}
    }
}

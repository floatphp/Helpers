<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Helpers\Filesystem\Cache;

trait TraitCacheable
{
    /**
     * @access protected
     * @var bool $useCache, Cache status
     * @var object $cache, Cache object
     */
	protected $useCache = true;
    protected $cache;

    /**
     * Disable cache.
     *
     * @access protected
     * @return object
     */
    protected function noCache() : Cache
    {
        $this->useCache = false;
        return $this;
    }

    /**
     * Get cache object.
     * [default:Redis]
     *
     * @access protected
     * @param string $driver
     * @return object
     */
    protected function getCacheObject($driver = 'Redis') : Cache
    {
        $this->cache = new Cache($driver);
        return $this->cache;
    }

    /**
     * Purge cache by request.
     *
     * @access protected
     * @param array $request
     * @return bool
     */
    protected function purgeCache(array $request = []) : bool
    {
        return Cache::auto($request);
    }
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Helpers\Connection\Transient;
use FloatPHP\Helpers\Filesystem\Cache;

trait TraitCacheable
{
    /**
     * Get cache value.
     *
     * @access public
     * @inheritdoc
     */
    public function getCache(string $key, ?bool &$status = null) : mixed
    {
        return (new Cache)->get($key, $status);
    }

    /**
     * Set cache value.
     *
     * @access public
     * @inheritdoc
     */
    public function setCache(string $key, $value, ?int $ttl = null, ?string $group = null) : bool
    {
        return (new Cache)->set($key, $value, $ttl, $group);
    }

    /**
     * Check cache status.
     *
     * @access public
     * @inheritdoc
     */
    public function hasCache(string $key) : bool
    {
        return (new Cache)->has($key);
    }

    /**
     * Delete cache.
     *
     * @access public
     * @inheritdoc
     */
    public function deleteCache(string $key) : bool
    {
        return (new Cache)->delete($key);
    }

    /**
     * Purge cache.
     *
     * @access public
     * @inheritdoc
     */
    public function purgeCache(?string $group = null) : bool
    {
        return (new Cache)->purge($group);
    }

    /**
     * Purge cache.
     *
     * @access public
     * @inheritdoc
     */
    public function getCacheKey(string $item, array $args = []) : string
    {
        return (new Cache)->getKey($item, $args);
    }

    /**
     * Get transient.
     *
     * @access public
     * @inheritdoc
     */
    public function getTransient(string $key, $default = null) : mixed
    {
        return (new Transient)->get($key, $default);
    }

    /**
     * Set transient.
     *
     * @access public
     * @inheritdoc
     */
    public function setTransient(string $key, $value = true, int $ttl = Transient::TTL) : bool
    {
        return (new Transient)->set($key, $value, $ttl);
    }

    /**
     * Delete transient.
     * 
     * @access public
     * @inheritdoc
     */
    public function deleteTransient(string $key) : bool
    {
        return (new Transient)->delete($key);
    }
}

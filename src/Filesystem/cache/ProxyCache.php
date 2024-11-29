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

/**
 * Wrapper class for AbstractCache.
 * @see https://www.phpfastcache.com
 */
class ProxyCache
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\tr\TraitThrowable,
		\FloatPHP\Helpers\Framework\tr\TraitLoggable;

	/**
	 * @access protected
	 * @var Phpfastcache $instance
	 */
	protected $instance;

	/**
	 * Get cache.
	 *
	 * @access public
	 * @param string $key
	 * @param bool $status
	 * @return mixed
	 */
	public function get(string $key, ?bool &$status = null) : mixed
	{
		$data = $this->getItem($key)->get();
		$status = $this->has($key);
		return $data;
	}

	/**
	 * Check cache status.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key) : bool
	{
		return $this->getItem($key)->isHit();
	}

	/**
	 * Set cache value.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @param string $group
	 * @return bool
	 */
	public function set(string $key, $value, ?int $ttl = null, ?string $group = null) : bool
	{
		$item = $this->getItem($key);
		$item->set($value);

		if ( !$this->isType('null', $ttl) ) {
			$item->expiresAfter($ttl);
		}

		if ( !$this->isType('null', $group) ) {
			$item->addTag($group);
		}

		return $this->instance->save($item);
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
	 * Purge any cache.
	 *
	 * @access public
	 * @param string $group
	 * @return bool
	 */
	public function purge(?string $group = null) : bool
	{
		if ( $group ) {
			return $this->instance->deleteItemsByTag($group);
		}
		return $this->instance->clear();
	}

	/**
	 * Get cache item.
	 *
	 * @access protected
	 * @param string $key
	 * @return object
	 */
	protected function getItem(string $key) : object
	{
		return $this->instance->getItem($key);
	}
}

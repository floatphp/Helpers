<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\Orm;
use FloatPHP\Classes\Filesystem\{
    TypeCheck, Stringify
};
use FloatPHP\Helpers\Filesystem\Cache;

/**
 * Wrapper class for database config (serialized).
 * @see Heavily inspired by WordPress kernel https://make.wordpress.org
 */
class Config
{
	/**
	 * @access private
	 * @var string $table
	 */
	private $table = 'config';

	/**
	 * Get config through cache.
	 * 
	 * @access public
	 * @param string $table
	 * @return void
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}

	/**
	 * Get config through cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $default
	 * @param bool $purge
	 * @return mixed
	 */
	public function get($key, $default = null, $purge = false)
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$value = $cache->get($key); // Set cache key
		if ( $purge ) {
			$cache->deleteByTag($key);
		}
		if ( !$cache->isCached() ) {
			$value = $this->getBase($key);
			$value = $this->unserialize($value);
			$cache->set($value, $key);
		}
		if ( !TypeCheck::isNull($default) && empty($value) ) {
			$value = $default;
		}
		return $value;
	}

	/**
	 * Update config through cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function update($key, $value = '') : bool
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$cache->get($key); // Set cache key
		$cache->deleteByTag($key);
		$value = $this->serialize($value);
		return $this->setBase($key, $value);
	}

	/**
	 * Delete config through cache.
	 * 
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function delete($key) : bool
	{
		$cache = new Cache();
		$key = $this->formatKey($key);
		$cache->get($key); // Set cache key
		$cache->deleteByTag($key);
		return $this->deleteBase($key);
	}

	/**
	 * Get database config.
	 * 
	 * @access protected
	 * @param string $key
	 * @return string
	 */
	protected function getBase($key) : string
	{
		$orm = new Orm();
		$bind = ['name' => $key];
		$sql = "SELECT `options` FROM `{$this->table}` WHERE `name` LIKE :name;";
		return (string)$orm->query($sql, $bind, ['isSingle' => true]);
	}

	/**
	 * Set database config.
	 * 
	 * @access protected
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	protected function setBase($key, $value = '') : bool
	{
		$orm = new Orm();
		$bind = ['name' => $key, 'value' => $value];
		if ( $this->exists($key) ) {
			return $this->updateBase($key, $value);
		}
		$sql = "INSERT INTO `{$this->table}` (`name`,`options`) VALUES(:name, :value);";
		return (bool)$orm->query($sql, $bind);
	}

	/**
	 * Update database config.
	 * 
	 * @access protected
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	protected function updateBase($key, $value = '') : bool
	{
		$orm = new Orm();
		$bind = ['name' => $key, 'value' => $value];
		$sql = "UPDATE `{$this->table}` SET `options` = :value WHERE `name` LIKE :name;";
		return (bool)$orm->query($sql, $bind);
	}

	/**
	 * Delete database config.
	 * 
	 * @access protected
	 * @param string $key
	 * @return bool
	 */
	protected function deleteBase($key) : bool
	{
		$orm = new Orm();
		$bind = ['name' => $key];
		$sql = "DELETE FROM `{$this->table}` WHERE `name` LIKE :name;";
		return (bool)$orm->query($sql, $bind);
	}

	/**
	 * Check database config.
	 * 
	 * @access protected
	 * @param string $key
	 * @return bool
	 */
	protected function exists($key) : bool
	{
		$orm = new Orm();
		$bind = ['name' => $key];
		$sql = "SELECT COUNT('name') FROM `{$this->table}` WHERE `name` LIKE :name;";
		return (bool)$orm->query($sql, $bind, ['isSingle' => true]);
	}

	/**
	 * Format key.
	 * 
	 * @access protected
	 * @param string $key
	 * @return string
	 */
	protected function formatKey($key) : string
	{
		return Stringify::formatKey((string)$key);
	}

	/**
	 * Serialize value.
	 * 
	 * @access protected
	 * @param string $value
	 * @return mixed
	 */
	protected function serialize($value)
	{
		return Stringify::serialize($value);
	}

	/**
	 * Unserialize value.
	 * 
	 * @access protected
	 * @param string $value
	 * @return mixed
	 */
	protected function unserialize($value)
	{
		return Stringify::unserialize($value);
	}
}

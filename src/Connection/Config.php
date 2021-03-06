<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Helpers\Filesystem\Cache;
use FloatPHP\Kernel\Orm;
use FloatPHP\Classes\Filesystem\Stringify;
use FloatPHP\Classes\Filesystem\TypeCheck;

class Config
{
	/**
	 * @access public
	 * @param string $name
	 * @param mixed $default
	 * @param bool $purge
	 * @return mixed
	 */
	public function get($name, $default = null, $purge = false)
	{
		$cache = $this->initCache(false);
		$key = Stringify::formatKey($name);
		if ( $purge ) {
			$cache->deleteByTag($key);
		}
		$value = $cache->get($key);
		if ( !$cache->isCached() ) {
			$value = $this->getBase($name);
			$value = Stringify::unserialize($value);
			$cache->set($value,$key);
		}
		if ( !TypeCheck::isNull($default) && empty($value) ) {
			$value = $default;
		}
		return $value;
	}

	/**
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function update($name = '', $value = '') : bool
	{
		$cache = $this->initCache(false);
		$key = Stringify::formatKey($name);
		$cache->deleteByTag($key);
		$value = Stringify::serialize($value);
		return $this->setBase($name,$value);
	}

	/**
	 * @access public
	 * @param string $name
	 * @return bool
	 */
	public function delete($name = '') : bool
	{
		$cache = $this->initCache(false);
		$key = Stringify::formatKey($name);
		$cache->deleteByTag($key);
		return $this->deleteBase($name);
	}

	/**
	 * @access protected
	 * @param string $name
	 * @return string
	 */
	protected function getBase($name) : string
	{
		$orm = new Orm();
		$bind = ['name' => $name];
		$sql = "SELECT `options` FROM `config` WHERE `name` LIKE :name;";
		return $orm->query($sql,$bind,['isSingle' => true]);
	}

	/**
	 * @access protected
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	protected function updateBase($name, $value = '') : bool
	{
		$orm = new Orm();
		$bind = ['name' => $name, 'value' => $value];
		$sql = "UPDATE `config` SET `options` = :value WHERE `name` LIKE :name;";
		return $orm->query($sql,$bind);
	}

	/**
	 * @access protected
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	protected function setBase($name, $value = '') : bool
	{
		$orm = new Orm();
		$bind = ['name' => $name, 'value' => $value];
		if ( $this->exists($name) ) {
			return $this->updateBase($name,$value);
		} else {
			$sql = "INSERT INTO `config` (`name`,`options`) VALUES(:name,:value);";
			return $orm->query($sql,$bind);
		}
	}

	/**
	 * @access protected
	 * @param string $name
	 * @return bool
	 */
	protected function deleteBase($name) : bool
	{
		$orm = new Orm();
		$bind = ['name' => $name];
		$sql = "DELETE FROM `config` WHERE `name` LIKE :name;";
		return $orm->query($sql,$bind);
	}

	/**
	 * @access protected
	 * @param int $expire
	 * @return object
	 */
	protected function initCache($expire = 0)
	{
		$cache = new Cache();
		if ( $expire !== false ) {
			$cache->setTTL($expire);
		}
		return $cache;
	}

	/**
	 * @access private
	 * @param string $name
	 * @return int
	 */
	private function exists($name) : int
	{
		$orm = new Orm();
		$bind = ['name' => $name];
		$sql = "SELECT COUNT('name') FROM `config` WHERE `name` LIKE :name;";
		return (int) $orm->query($sql,$bind,['isSingle' => true]);
	}
}

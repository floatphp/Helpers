<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\Orm;

/**
 * Config class used to store config in database.
 */
class Config extends Orm
{
	/**
	 * @access private
	 * @var string $keyColumn, Config key column name
	 * @var string $valueColumn, Config value column name
	 */
	private $keyColumn;
	private $valueColumn;

	/**
	 * @access private
	 * @var string ID
	 * @var string KEY
	 * @var string VALUE
	 */
	private const ID    = 'configId';
	private const KEY   = 'name';
	private const VALUE = 'options';
	private const TABLE = 'config';

	/**
	 * Init database.
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $id
	 * @param string $table
	 */
	public function __construct($key = self::KEY, $value = self::VALUE, $id = self::ID, $table = self::TABLE)
	{
		// Init ORM
		parent::__construct();

		// Set table
		$this->keyColumn = $key;
		$this->valueColumn = $value;
		$this->key = $id;
		$this->table = $table;

		// Reset config
		$this->resetConfig();
	}

	/**
	 * Get database config value by key.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function getValue(string $key) : string
	{
		$this->bind(["{$this->keyColumn}" => $key]);
		$sql = "SELECT `{$this->valueColumn}` FROM `{$this->table}` ";
		$sql .= "WHERE `{$this->keyColumn}` LIKE :{$this->keyColumn};";
		return (string)$this->getSingle($sql);
	}

	/**
	 * Set database config value.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function setValue(string $key, $value = '') : bool
	{
		if ( $this->hasValue($key) ) {
			return $this->updateValue($key, $value);
		}
		$this->bind([
			"{$this->keyColumn}"   => $key,
			"{$this->valueColumn}" => $value
		]);
		$sql = "INSERT INTO `{$this->table}` (`$this->keyColumn`, `{$this->valueColumn}`) ";
		$sql .= "VALUES(:{$this->keyColumn}, :{$this->valueColumn});";
		return (bool)$this->execute($sql);
	}

	/**
	 * Update database config value.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function updateValue(string $key, $value = '') : bool
	{
		$this->bind([
			"{$this->keyColumn}"   => $key,
			"{$this->valueColumn}" => $value
		]);
		$sql = "UPDATE `{$this->table}` SET `{$this->valueColumn}` = :{$this->valueColumn} ";
		$sql .= "WHERE `{$this->keyColumn}` LIKE :{$this->keyColumn};";
		return (bool)$this->execute($sql);
	}

	/**
	 * Delete database config by key.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function deleteValue(string $key) : bool
	{
		$this->bind(["{$this->keyColumn}" => $key]);
		$sql = "DELETE FROM `{$this->table}` ";
		$sql .= "WHERE `{$this->keyColumn}` LIKE :{$this->keyColumn};";
		return (bool)$this->execute($sql);
	}

	/**
	 * Check database config exists by key.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function hasValue(string $key) : bool
	{
		$this->bind(["{$this->keyColumn}" => $key]);
		$sql = "SELECT COUNT('{$this->keyColumn}') FROM `{$this->table}` ";
		$sql .= "WHERE `{$this->keyColumn}` LIKE :{$this->keyColumn};";
		return (bool)$this->getSingle($sql);
	}

	/**
	 * Set config Id.
	 *
	 * @access public
	 * @param string $key
	 * @return int
	 */
	public function getId(string $key) : int
	{
		$this->bind(["{$this->keyColumn}" => $key]);
		$sql = "SELECT `{$this->key}` FROM `{$this->table}` ";
		$sql .= "WHERE `{$this->keyColumn}` LIKE :{$this->keyColumn};";
		return (int)$this->getSingle($sql);
	}
}

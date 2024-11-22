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

/**
 * Built-in Settings IO class.
 */
final class Settings
{
	use \FloatPHP\Helpers\Framework\inc\TraitConfigurable,
		\FloatPHP\Helpers\Framework\inc\TraitFormattable;

	/**
	 * @access private
	 * @var string $row, Temp row name
	 * @var string ROW
	 * @var string TTL
	 */
	private $row;
	private const ROW = 'settings';

	/**
	 * Set settings row group.
	 *
	 * @access public
	 * @param string $row
	 */
	public function __construct(string $row = self::ROW)
	{
		// Set settings row name
		$this->row = $row;

		// Init config
		$this->getConfigObject();
	}

	/**
	 * Get settings value.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get(?string $key = null, $default = null) : mixed
	{
		$settings = $this->getValues();
		if ( !$this->isType('null', $key) ) {
			return $settings[$key] ?? $default;
		}
		return $settings;
	}

	/**
	 * Set settings value.
	 *
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return bool
	 */
	public function set($key, $value = null) : bool
	{
		if ( $this->isType('array', $key) ) {
			return $this->setValues($key);
		}
		if ( $this->isType('string', $key) ) {
			$settings = $this->getValues();
			$settings[$key] = $value;
			return $this->setValues($settings);
		}
		return false;
	}

	/**
	 * Delete settings value.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function delete(string $key) : bool
	{
		$settings = $this->getValues();
		if ( isset($settings[$key]) ) {
			unset($settings[$key]);
			return $this->setValues($settings);
		}
		return false;
	}

	/**
	 * Reset database settings row.
	 *
	 * @access public
	 * @return bool
	 */
	public function reset() : bool
	{
		return $this->deleteConfigValue($this->row);
	}

	/**
	 * Get settings values from database.
	 *
	 * @access private
	 * @return array
	 */
	private function getValues() : array
	{
		$settings = $this->getConfigValue($this->row);
		$settings = $this->unserialize($settings);

		if ( $this->isType('string', $settings) ) {
			$settings = $this->decodeJson($settings, true);
		}

		return $settings ?: [];
	}

	/**
	 * Set settings values in database.
	 *
	 * @access private
	 * @param array $settings
	 * @return bool
	 */
	private function setValues(array $settings) : bool
	{
		$settings = $this->formatJson($settings, 256);
		$settings = $this->serialize($settings);

		return $this->setConfigValue($this->row, $settings);
	}
}

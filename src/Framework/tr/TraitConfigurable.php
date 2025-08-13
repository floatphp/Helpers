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

use FloatPHP\Helpers\Connection\Config;

trait TraitConfigurable
{
	/**
	 * @access protected
	 * @var string $config, Config object
	 */
	protected $config;

	/**
	 * Get config object.
	 *
	 * @access protected
	 * @return object
	 */
	protected function getConfigObject() : Config
	{
		return $this->config = new Config();
	}

	/**
	 * Get database config value by key.
	 * 
	 * @access protected
	 * @param string $key
	 * @return string
	 */
	protected function getConfigValue(string $key) : string
	{
		return $this->config->getValue($key);
	}

	/**
	 * Set database config value.
	 * 
	 * @access protected
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	protected function setConfigValue(string $key, $value = '') : bool
	{
		return $this->config->setValue($key, $value);
	}

	/**
	 * Update database config value.
	 * 
	 * @access protected
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	protected function updateConfigValue(string $key, $value = '') : bool
	{
		return $this->config->updateValue($key, $value);
	}

	/**
	 * Delete database config by key.
	 * 
	 * @access protected
	 * @param string $key
	 * @return bool
	 */
	protected function deleteConfigValue(string $key) : bool
	{
		return $this->config->deleteValue($key);
	}

	/**
	 * Check database config exists by key.
	 * 
	 * @access protected
	 * @param string $key
	 * @return bool
	 */
	protected function hasConfigValue(string $key) : bool
	{
		return $this->config->hasValue($key);
	}

	/**
	 * Set config Id.
	 * 
	 * @access protected
	 * @param string $table
	 * @return int
	 */
	protected function getConfigId(string $key) : int
	{
		return $this->config->getId($key);
	}
}

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
        $this->config = new Config();
        return $this->config;
    }

	/**
	 * Get database config value by key.
	 * 
	 * @access protected
	 * @param string $key
	 * @return string
	 */
	protected function getConfigValue($key) : string
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
	protected function setConfigValue($key, $value = '') : bool
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
	protected function updateConfigValue($key, $value = '') : bool
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
	protected function deleteConfigValue($key) : bool
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
	protected function hasConfigValue($key) : bool
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
	protected function getConfigId($key) : int
	{
		return $this->config->getId($key);
	}
}

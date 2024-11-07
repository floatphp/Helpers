<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\Filesystem\TypeCheck;
use FloatPHP\Exceptions\Kernel\ConfigurationException;
use JsonSchema\Validator as JsonValidator;

final class Validator
{
	/**
	 * Check global config.
	 * 
	 * @access public
	 * @var object $config
	 * @return void
	 * @throws ConfigurationException
	 */
	public static function checkConfig($config)
	{
		$error = self::isValidConfig($config);
		if ( TypeCheck::isString($error) ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidApplicationConfiguration($error)
	        );

		} elseif ( $error === false ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidApplicationConfigurationFile()
	        );
		}
	}

	/**
	 * Check module config.
	 * 
	 * @access public
	 * @var object $config
	 * @return void
	 * @throws ConfigurationException
	 */
	public static function checkModuleConfig($config)
	{
		$error = self::isValidConfig($config, 'module.schema.json');
		if ( TypeCheck::isString($error) ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidModuleConfiguration($error)
	        );

		} elseif ( $error === false ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidModuleConfigurationFile()
	        );
		}
	}

	/**
	 * Check route config.
	 * 
	 * @access public
	 * @var object $config
	 * @return void
	 * @throws ConfigurationException
	 */
	public static function checkRouteConfig($config)
	{
		$error = self::isValidConfig($config, 'route.schema.json');
		if ( TypeCheck::isString($error) ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidRouteConfiguration($error)
	        );

		} elseif ( $error === false ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidRouteConfigurationFile()
	        );
		}
	}

	/**
	 * Check database config.
	 * 
	 * @access public
	 * @var array $access
	 * @return void
	 * @throws ConfigurationException
	 */
	public static function checkDatabaseConfig($access)
	{
		if ( !isset($access['default']) || !isset($access['root']) ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidDatabaseConfiguration()
	        );
		}
	}

	/**
	 * Check whether config is valid using schema.
	 * 
	 * @access private
	 * @var object $config
	 * @var string $schema
	 * @return mixed
	 */
	private static function isValidConfig($config, $schema = 'config.schema.json')
	{
		$validator = new JsonValidator;
		$validator->validate($config, (object)[
			'$ref' => 'file://' . dirname(__FILE__). '/bin/' . $schema
		]);
		if ( $validator->isValid() ) {
			return true;

		} else {
			$errors = [];
		    foreach ($validator->getErrors() as $error) {
		        $errors[] = sprintf("[%s] %s", $error['property'], $error['message']);
		    }
		    return implode("\n", $errors);
		}
		return false;
	}
}

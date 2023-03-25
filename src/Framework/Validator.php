<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
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
	 * @access public
	 * @var array $access
	 * @return void
	 * @throws ConfigurationException
	 */
	public static function checkDatabaseAccess($access)
	{
		if ( !isset($access['default']) || !isset($access['root']) ) {
	        throw new ConfigurationException(
	            ConfigurationException::invalidDatabaseConfiguration()
	        );
		}
	}

	/**
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

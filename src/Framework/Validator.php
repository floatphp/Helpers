<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\Filesystem\{Arrayify, TypeCheck};
use FloatPHP\Exceptions\Kernel\ConfigException;
use JsonSchema\Validator as JsonValidator;

final class Validator
{
	/**
	 * Check global config.
	 * 
	 * @access public
	 * @var object $config
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkConfig($config) : void
	{
		$error = self::isValidConfig($config);
		if ( TypeCheck::isString($error) ) {
			throw new ConfigException(
				ConfigException::invalidApplicationConfiguration($error)
			);

		} elseif ( $error === false ) {
			throw new ConfigException(
				ConfigException::invalidApplicationConfigurationFile()
			);
		}
	}

	/**
	 * Check module config.
	 * 
	 * @access public
	 * @var object $config
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkModuleConfig($config) : void
	{
		$error = self::isValidConfig($config, 'module.schema.json');
		if ( TypeCheck::isString($error) ) {
			throw new ConfigException(
				ConfigException::invalidModuleConfiguration($error)
			);

		} elseif ( $error === false ) {
			throw new ConfigException(
				ConfigException::invalidModuleConfigurationFile()
			);
		}
	}

	/**
	 * Check route config.
	 * 
	 * @access public
	 * @var object $config
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkRouteConfig($config) : void
	{
		$error = self::isValidConfig($config, 'route.schema.json');
		if ( TypeCheck::isString($error) ) {
			throw new ConfigException(
				ConfigException::invalidRouteConfiguration($error)
			);

		} elseif ( $error === false ) {
			throw new ConfigException(
				ConfigException::invalidRouteConfigurationFile()
			);
		}
	}

	/**
	 * Check database config.
	 *
	 * @access public
	 * @var array $data
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkDatabaseConfig(array $data, string $type = 'default') : void
	{
		$required = ['user', 'pswd'];

		if ( $type == 'default' ) {
			$default = ['host', 'port', 'name', 'charset', 'collate'];
			$required = Arrayify::merge($default, $required);
		}

		foreach ($required as $key => $value) {
			if ( !isset($data[$value]) ) {
				throw new ConfigException(
					ConfigException::invalidDatabaseConfiguration()
				);
			}
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
	private static function isValidConfig($config, $schema = 'config.schema.json') : mixed
	{
		$validator = new JsonValidator;
		$validator->validate(value: $config, schema: (object)[
			'$ref' => 'file://' . dirname(__FILE__) . '/bin/' . $schema
		]);

		if ( $validator->isValid() ) {
			return true;
		}

		$errors = [];
		foreach ($validator->getErrors() as $error) {
			$errors[] = sprintf("[%s] %s", $error['property'], $error['message']);
		}

		return implode("\n", $errors);
	}
}

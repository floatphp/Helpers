<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Classes\Filesystem\TypeCheck;
use FloatPHP\Classes\Filesystem\Json;
use FloatPHP\Exceptions\Kernel\ConfigException;
use JsonSchema\Validator as JsonValidator;

final class Validator
{
	/**
	 * @access public
	 * @var mixed $config
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkConfig($config)
	{
		try {
			$error = self::isValidConfig($config,'config.schema.json');
			if ( TypeCheck::isString($error) ) {
				throw new ConfigException($error);
				
			} elseif ( $error === false ) {
				throw new ConfigException();
			}
		} catch (ConfigException $e) {
			die($e->get(1));
		}
	}

	/**
	 * @access public
	 * @var mixed $config
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkModuleConfig($config)
	{
		try {
			$error = self::isValidConfig($config,'module.schema.json');
			if ( TypeCheck::isString($error) ) {
				throw new ConfigException($error);

			} elseif ( $error === false ) {
				throw new ConfigException();
			}
		} catch (ConfigException $e) {
			die($e->get(2));
		}
	}

	/**
	 * @access public
	 * @var mixed $config
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkRouteConfig($config)
	{
		try {
			$error = self::isValidConfig($config,'route.schema.json');
			if ( TypeCheck::isString($error) ) {
				throw new ConfigException($error);

			} elseif ( $error === false ) {
				throw new ConfigException();
			}
		} catch (ConfigException $e) {
			die($e->get(3));
		}
	}

	/**
	 * @access public
	 * @var mixed $access
	 * @return void
	 * @throws ConfigException
	 */
	public static function checkDatabaseAccess($access)
	{
		try {
			if ( !isset($access['default']) || !isset($access['root']) ) {
				throw new ConfigException();
			}
		} catch (ConfigException $e) {
			die($e->get(4));
		}
	}

	/**
	 * @access private
	 * @var mixed $config
	 * @return mixed
	 */
	private static function isValidConfig($config, $schema)
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
		        $errors[] = sprintf("[%s] %s",$error['property'],$error['message']);
		    }
		    return implode("\n",$errors);
		}
		return false;
	}
}

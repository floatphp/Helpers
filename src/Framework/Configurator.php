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

final class Configurator
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * Init configuration.
	 */
	public function __construct()
	{
		$this->initConfig();
	}

	/**
	 * @access public
	 * @return object
	 */
	public function reflect() : object
	{
		return $this->getConfig();
	}

	/**
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function getRoot(string $path = '') : string
	{
		global $appDir;
		$root = dirname($appDir);
		return $this->formatPath("{$root}/{$path}", true);
	}
}

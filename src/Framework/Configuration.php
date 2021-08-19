<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Framework Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\Filesystem\Stringify;

final class Configuration
{
	use TraitConfiguration;

	/**
	 * @access public
	 * @param void
	 * @return object
	 */
	public function reflect() : object
	{
		$this->initConfig();
		return $this->getConfig();
	}

	/**
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function getRoot($path = '') : string
	{
		global $appDir;
		$root = dirname($appDir);
		return Stringify::formatPath("{$root}/{$path}",true);
	}
}

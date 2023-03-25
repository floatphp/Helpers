<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\Filesystem\{
    File, TypeCheck, Stringify
};

/**
 * Class loader for custom helpers.
 */
class Loader
{
	use TraitConfiguration;

	/**
	 * @access protected
	 * @var string $baseDir
	 * @var string $regex
	 */
	protected $baseDir = 'App/Helpers';
	protected $regex = '/^.*\.(php)$/i';

	/**
	 * Set base dir.
	 * 
	 * @access public
	 * @param string $baseDir
	 * @return mixed
	 */
	public function setBaseDir($baseDir)
	{
        $this->baseDir = $this->formatPath($baseDir);
	}

	/**
	 * Instance class.
	 * 
	 * @access public
	 * @param string $path
	 * @param string $className
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return mixed
	 */
	public function instance($path, $className, $arg1 = null, $arg2 = null)
	{
		$path = $this->formatPath($path);
		$dir = "{$this->getRoot()}/{$this->baseDir}/{$path}";
		if ( File::isDir($dir) ) {
			$files = $this->scan($dir);
			$className = Stringify::lowercase($className);
			if ( isset($files[$className]) ) {
				if ( TypeCheck::isClass($files[$className]) ) {
					$class = $files[$className];
					return new $class($arg1, $arg2);
				}
			}
		}
		return false;
	}

	/**
	 * Instance alias.
	 * 
	 * @access public
	 * @param string $path
	 * @param string $className
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return mixed
	 */
	public function i($path, $className, $arg1 = null, $arg2 = null)
	{
		return $this->instance($path, $className, $arg1, $arg2);
	}

	/**
	 * Set regex.
	 * 
	 * @access public
	 * @param string $regex
	 * @return void
	 */
	public function setRegex($regex)
	{
		$this->regex = $regex;
	}

	/**
	 * Scan classes files.
	 * 
	 * @access protected
	 * @param string $path
	 * @return array
	 */
	protected function scan($path)
	{
		$files = File::scanDir($path);
		$base = basename($path);
		$namespace = $this->formatPath("{$this->baseDir}/{$base}", true);
		foreach ($files as $key => $name) {
			if ( Stringify::match($this->regex, $name) ) {
				$name = substr($name, 0, strrpos($name, '.php'));
				$slug = Stringify::lowercase($name);
				$files[$slug] = "{$namespace}\\{$name}";
			}
			unset($files[$key]);
		}
		return $files;
	}

	/**
	 * Format path.
	 * 
	 * @access protected
	 * @param string $path
	 * @param bool $namespace
	 * @return string
	 */
	protected function formatPath($path, $namespace = false)
	{
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');
        $path = ltrim($path, '\\');
        $path = rtrim($path, '\\');
        if ( $namespace ) {
        	$path = Stringify::replace('/', '\\', $path);
        }
        return $path;
	}
}

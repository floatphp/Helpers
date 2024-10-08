<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

/**
 * Built-in Loader factory class.
 */
class Loader
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access protected
	 * @var string $baseDir
	 * @var string $pattern
	 */
	protected $baseDir = 'App/Helpers';
	protected $pattern = '/^.*\.(php)$/i';

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
		if ( $this->isDir($dir) ) {
			$files = $this->scan($dir, $path);
			$className = $this->lowercase($className);
			if ( isset($files[$className]) ) {
				if ( $this->isType('class', $files[$className]) ) {
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
	 * Set pattern.
	 * 
	 * @access public
	 * @param string $pattern
	 * @return void
	 */
	public function setRegex($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * Scan classes files.
	 * 
	 * @access protected
	 * @param string $dir
	 * @param string $base
	 * @return array
	 */
	protected function scan(string $dir, string $base)
	{
		$files = $this->scanDir($dir);
		$namespace = $this->formatPath("{$this->baseDir}/{$base}", true);
		foreach ($files as $key => $name) {
			if ( $this->matchString($this->pattern, $name) ) {
				$name = substr($name, 0, strrpos($name, '.php'));
				$slug = $this->lowercase($name);
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
	protected function formatPath(string $path, bool $namespace = false)
	{
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');
        $path = ltrim($path, '\\');
        $path = rtrim($path, '\\');
        if ( $namespace ) {
        	$path = $this->replaceString('/', '\\', $path);
        }
        return $path;
	}
}

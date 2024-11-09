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

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Classes\Filesystem\{
	File, Archive, Stringify, TypeCheck
};

trait TraitIO
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function addDir(string $path, int $p = 0755, bool $r = true, $c = null) : bool
	{
		return File::addDir($path, $p, $r, $c);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function isDir(string $path) : bool
    {
        return File::isDir($path);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function scanDir(string $path = '.', int $sort = 0, array $except = []) : array
    {
        return File::scanDir($path, $sort, $except);
    }

    /**
     * Clear directory recursively.
	 *
	 * @access protected
	 * @inheritdoc
	 */
    protected function clearDir(string $path, $from = []) : bool
	{
		if ( !$this->secureRemove($path, $from) ) {
			return false;
		}
		return File::clearDir($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function removeDir(string $path, bool $clear = false) : bool
	{
		return File::removeDir($path, $clear);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function readFile(string $path, bool $inc = false, $context = null, int $offset = 0)
	{
		return File::r($path, $inc, $context, $offset);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function writeFile(string $path, $input = '', $append = false) : bool
	{
		return File::w($path, $input, $append);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function copyFile(string $path, string $to, $context = null) : bool
	{
		return File::copy($path, $to, $context);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasFile(string $path) : bool
	{
		return File::isFile($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function indexFiles(string $path = '.')
	{
		return File::index($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function countFiles(string $path = '.')
	{
		return File::count($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function lastFile(string $path = '.')
	{
		return File::last($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function firstFile(string $path = '.')
	{
		return File::first($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeFile(string $path)
	{
		return File::remove($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getFileExtension(string $path, bool $format = true) : string
	{
		return File::getExtension($path, $format);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function parseIni(string $path, bool $sections = false, int $mode = INI_SCANNER_NORMAL)
	{
		return File::parseIni($path, $sections, $mode);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function compressArchive(string $path, string $to = '', string $archive = '') : bool
	{
		return Archive::compress($path, $to, $archive);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function uncompressArchive(string $archive, string $to = '', $remove = false) : bool
	{
		return Archive::uncompress($archive, $to, $remove);
	}

    /**
     * Secure remove.
	 *
	 * @access private
	 * @param string $path
	 * @param mixed $secure
	 * @return bool
	 */
    private function secureRemove(string $path, $secure = []) : bool
    {
		if ( $secure && !TypeCheck::isArray($secure) ) {
			$secure = (string)$secure;
			$secure = [$secure];
		}

		$secure = ($secure) ? $secure : ['public'];
		foreach ($secure as $include) {
			if ( !Stringify::contains($path, $include) ) {
				return false;
			}
		}

		return true;
    }
}

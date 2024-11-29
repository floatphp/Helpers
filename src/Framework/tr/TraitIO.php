<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Filesystem\{File, Json, Archive, Stringify, TypeCheck};
use FloatPHP\Helpers\Filesystem\Yaml;

/**
 * Define filesystem IO functions.
 */
trait TraitIO
{
	/**
	 * @access protected
	 * @var array IOSTORAGE
	 */
	protected const IOSTORAGE = ['App/Storage'];

	/**
	 * Check file.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isFile(string $path) : bool
	{
		return File::isFile($path);
	}

	/**
	 * Check file readable.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isReadable(string $path, bool $fileType = false) : bool
	{
		if ( $fileType && !$this->isFile($path) ) {
			return false;
		}
		return File::isReadable($path);
	}

	/**
	 * Check file writable.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isWritable(string $path, bool $fileType = false) : bool
	{
		if ( $fileType && !$this->isFile($path) ) {
			return false;
		}
		return File::isWritable($path);
	}

	/**
	 * Read file.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function readFile(string $path, bool $inc = false, $context = null, int $offset = 0) : mixed
	{
		return File::r($path, $inc, $context, $offset);
	}

	/**
	 * Get all file lines.
	 * 
	 * @access public
	 * @inheritdoc
	 */
	public function getLines(string $path) : array
	{
		return File::getLines($path);
	}

	/**
	 * Parse file lines using stream.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function parseLines(string $path, int $limit = 10) : array
	{
		return File::parseLines($path, $limit);
	}

	/**
	 * Parse JSON file.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function parseJson(string $file, bool $isArray = false) : mixed
	{
		return Json::parse($file, $isArray);
	}

	/**
	 * Check directory.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isDir(string $path) : bool
	{
		return File::isDir($path);
	}

	/**
	 * Scan directory.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function scanDir(string $path = '.', int $sort = 0, array $except = []) : array
	{
		return File::scanDir($path, $sort, $except);
	}

	/**
	 * Write file.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function writeFile(string $path, $input = '', bool $append = false) : bool
	{
		return File::w($path, $input, $append);
	}

	/**
	 * Copy file.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function copyFile(string $path, string $to, $context = null) : bool
	{
		return File::copy($path, $to, $context);
	}

	/**
	 * Remove file.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeFile(string $path, $from = []) : bool
	{
		if ( !$this->secureRemove($path, $from) ) {
			return false;
		}
		return File::remove($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function indexFiles(string $path = '.') : mixed
	{
		return File::index($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function countFiles(string $path = '.') : mixed
	{
		return File::count($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function firstFile(string $path = '.') : mixed
	{
		return File::first($path);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function lastFile(string $path = '.') : mixed
	{
		return File::last($path);
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
	protected function parseIni(string $path, bool $sections = false, int $mode = 0) : mixed
	{
		return File::parseIni($path, $sections, $mode);
	}

	/**
	 * Parse Yaml file.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function parseYaml(string $path, ?string $section = null) : mixed
	{
		return Yaml::parse($path, $section);
	}

	/**
	 * Add directory.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function addDir(string $path, int $p = 0755, bool $r = true, $c = null) : bool
	{
		return File::addDir($path, $p, $r, $c);
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
	 * Remove directory.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeDir(string $path, bool $clear = false, $from = []) : bool
	{
		if ( !$this->secureRemove($path, $from) ) {
			return false;
		}
		return File::removeDir($path, $clear);
	}

	/**
	 * Compress archive.
	 * 
	 * @access protected
	 * @inheritdoc
	 */
	protected function compressArchive(string $path, string $to = '', string $archive = '') : bool
	{
		return Archive::compress($path, $to, $archive);
	}

	/**
	 * Uncompress archive.
	 *
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
	 * @access protected
	 * @param string $path
	 * @param mixed $from
	 * @return bool
	 */
	protected function secureRemove(string $path, $from = []) : bool
	{
		$secure = [];
		if ( $from && !TypeCheck::isArray($from) ) {
			$from = (string)$from;
			$secure = [$from];
		}

		$secure = $secure ?: static::IOSTORAGE;
		foreach ($secure as $include) {
			if ( !Stringify::contains($path, $include) ) {
				return false;
			}
		}

		return true;
	}
}

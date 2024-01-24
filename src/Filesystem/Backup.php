<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

/**
 * Backup helper class.
 */
final class Backup
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access private
	 * @var string SECRET Default backup secret key
	 */
	private const SECRET = '--backup';

	/**
	 * @access private
	 * @var string $path, Backup path
	 * @var string $pattern, Backup extension pattern
	 * @var bool $compression, Backup compression
	 * @var bool $encryption, Backup encryption
	 * @var string $secret, Backup encryption secret
	 */
	private $path;
	private $pattern;
	private $compression = false;
	private $encryption = false;
	private $secret;

	/**
	 * Init backup, Using pattern (.e.g *.backup).
	 *
	 * @param string $path
	 * @param string $pattern
	 * @uses initConfig()
	 * @uses resetConfig()
	 */
	public function __construct(string $path = '/backups', ?string $pattern = null)
	{
		// Init configuration
		$this->initConfig();

		// Set path
		$this->path = $this->getAdminUploadPath($path);

		// Set pattern
		$this->pattern = $pattern;

		// Set secret
		$this->setSecret();

		// Prepare directory
		if ( !$this->isDir($this->path) ) {
			$this->addDir($this->path);
		}

		// Reset config
		$this->resetConfig();
	}

	/**
	 * Set backup compression.
	 * 
	 * @access public
	 * @return object
	 */
	public function compress() : self
	{
		$this->compression = true;
		return $this;
	}

	/**
	 * Set backup encryption.
	 * 
	 * @access public
	 * @param string $secret
	 * @return object
	 */
	public function encrypt(string $secret = self::SECRET) : self
	{
		$this->setSecret($secret);
		$this->encryption = true;
		return $this;
	}

	/**
	 * Set backup encryption secret.
	 * 
	 * @access public
	 * @param string $secret
	 * @return object
	 */
	public function setSecret(string $secret = self::SECRET) : self
	{
		$this->secret = $secret;
		return $this;
	}

	/**
	 * Export backup.
	 * 
	 * @access public
	 * @param mixed $data
	 * @param string $filename, Backup filename
	 * @param int $keep, Backups to keep
	 * @return bool
	 */
	public function export($data, string $filename = '{name}{date}{ext}', int $keep = 5) : bool
	{
		// Encrypt data
		if ( $this->encryption ) {
			$this->getHashObject($data, $this->secret);
			$this->hash->setPrefix('backup');
			$data = $this->hash->encrypt();
		}

		// Format data
		if ( !$this->isType('string', $data) ) {
			$data = $this->serialize($data);
		}

		// Format filename
		$filename = $this->replaceStringArray([
			'{name}' => 'data-',
			'{date}' => date('dmyhis'),
			'{ext}'  => '.backup'
		], $filename);

		// Export backup
		$backup = $this->formatPath("{$this->path}/{$filename}");
		$status = $this->writeFile($backup, $data);

		// Reset backup
		$this->reset($keep, "{$this->path}/{$this->pattern}");

		// Compress backup
		if ( $this->compression ) {
			if ( $this->doCompress($backup) ) {
				$this->reset($keep, "{$this->path}/{$this->pattern}.zip");
			}
		}

		return $status;
	}

	/**
	 * Import backup.
	 * 
	 * @access public
	 * @param string $path, Backup file
	 * @return mixed
	 */
	public function import(?string $path = null)
	{
		// Set backup
		if ( $path ) {
			$backup = (string)$path;

		} else {
			$backup = (string)$this->lastFile($this->path);
		}

		// Check backup
		if ( !$this->hasFile($backup) ) {
			return false;
		}

		// Decompress backup
		if ( $this->isCompressed($backup) ) {
			if ( $this->decompress($backup) ) {
				$backup = $this->removeString('.zip', $backup);
			}
		}

		// Get backup data
		$data = $this->readFile($backup);

		// Decrypt data
		$this->getHashObject($data, $this->secret);
		$this->hash->setPrefix('backup');
		if ( $this->hash->isCrypted() ) {
			$data = $this->hash->decrypt();
		}

		return $data;
	}

	/**
	 * Archive backups root directory.
	 *
	 * @access public
	 * @param bool $remove
	 * @return bool
	 */
	public function archive($remove = false) : bool
	{
		$root = dirname($this->path);
		if ( $this->compressArchive($this->path, $root) ) {
			if ( $remove ) {
				$this->removeDir($this->path, true);
			}
			return true;
		}
		return false;
	}

	/**
	 * Count backup files.
	 * 
	 * @access public
	 * @param string $path
	 * @return int
	 */
	private function count(string $path) : int
	{
		return (int)$this->countFiles($path);
	}

	/**
	 * Remove first backup file.
	 * 
	 * @access public
	 * @param string $path
	 * @return bool
	 */
	private function remove(string $path) : bool
	{
		return $this->removeFile($this->firstFile($path));
	}

	/**
	 * Reset backup files using pattern.
	 * 
	 * @access public
	 * @param int $keep
	 * @param string $pattern
	 * @return void
	 */
	private function reset(int $keep, string $pattern)
	{
		if ( !$keep ) $keep = 1;
		$pattern = $this->formatPath($pattern);
		$count = $this->count($pattern);
		if ( $count > $keep ) {
			while ($this->count($pattern) > $keep) {
				$this->remove($pattern);
			}
		}
	}

	/**
	 * Compress backup file.
	 *
	 * @access private
	 * @param string $file
	 * @return bool
	 */
	private function doCompress(string $file) : bool
	{
		if ( $this->compressArchive($file) ) {
			$this->removeFile($file);
			return true;
		}
		return false;
	}

	/**
	 * Decompress backup file.
	 *
	 * @access private
	 * @param string $file
	 * @return bool
	 */
	private function decompress(string $file) : bool
	{
		if ( $this->uncompressArchive($file) ) {
			$this->removeFile($file);
			return true;
		}
		return false;
	}

	/**
	 * Check whether backup is compressed.
	 *
	 * @access private
	 * @param string $file
	 * @return bool
	 */
	private function isCompressed(string $file) : bool
	{
		return ($this->getFileExtension($file) == 'zip');
	}
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\Orm;
use FloatPHP\Helpers\{
	Http\Catcher,
	Filesystem\Backup,
	Filesystem\Cache
};

/**
 * Database table helper class.
 */
final class Table extends Orm
{
	/**
	 * @access private
	 * @var array $exclude
	 */
	private $exclude = [];

	/**
	 * Init database.
	 *
	 * @param string $table
	 * @uses resetConfig()
	 */
	public function __construct(?string $table = null)
	{
		// Init ORM
		parent::__construct();

		// Set table
		$this->table = (new Catcher(['--key' => $table]))->key;

		// Set key
		$this->key = "{$this->table}Id";

		// Reset config
		$this->resetConfig();
	}

	/**
	 * Export table backup.
	 * 
	 * @access public
	 * @param bool $compress
	 * @return bool
	 */
	public function export(bool $compress = false) : bool
	{
		// Check table
		if ( !$this->hasTable() ) return false;
		
		// Exclude table
		if ( $this->inArray($this->table, $this->exclude) ) {
			return false;
		}

		// Export backup
		if ( ($data = $this->all()) ) {
			$path = "/backups/{$this->table}";
			$file = "{$this->table}-{date}{ext}";
			$backup = new Backup($path);
			if ( $compress ) {
				$backup->compress();
			}
			return $backup->encrypt()->export($data, $file);
		}
		
		return false;
	}

	/**
	 * Restore table backup.
	 * 
	 * @access public
	 * @param bool $resetId
	 * @return bool
	 * @todo Add cache reset
	 */
	public function restore($resetId = false) : bool
	{
		// Check table
		if ( !$this->hasTable() ) return false;

		// Import backup
		$path = "/backups/{$this->table}";
		$data = (new Backup($path))->import();

		// Convert serialized data
		if ( $this->isType('string', $data) ) {
			$data = $this->unserialize($data);
		}

		// Check data
		if ( !$this->isType('array', $data) ) {
			return false;
		}

		// Reset table
		if ( $this->clear() ) {
			$this->resetId();
		}

		// Restore backup
		$count = 0;
		foreach ($data as $item) {
			if ( $resetId ) {
				unset($item[$this->key]);
			}
			$count += (int)$this->create($item);
		}
		if ( $count ) {
			// Cache::auto(['key' => $this->table]);
		}

		return (bool)$count;
	}

	/**
	 * Reset table including Ids.
	 * 
	 * @access public
	 * @return bool
	 * @todo Add cache reset
	 */
	public function reset() : bool
	{
		// Check table
		if ( !$this->hasTable() ) return false;

		// Backup table
		if ( $this->count() ) {
			$this->export();
		}

		// Reset table
		if ( $this->clear() ) {
			$this->resetId();
			// Cache::auto(['key' => $this->table]);
			return true;
		}

		return false;
	}

	/**
	 * Rebuild table with initialized Ids.
	 * 
	 * @access public
	 * @return bool
	 */
	public function rebuild() : bool
	{
		// Check table
		if ( !$this->hasTable() ) return false;

		// Backup table
		if ( $this->count() ) {
			$this->export();
		}

		// Reset Ids
		return $this->restore(true);
	}

	/**
	 * Exclude table from export.
	 * 
	 * @access public
	 * @param array $exclude
	 * @return object
	 */
	public function exclude(array $exclude) : self
	{
		$this->exclude = $exclude;
		return $this;
	}
}

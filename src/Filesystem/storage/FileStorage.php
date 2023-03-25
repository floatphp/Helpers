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

namespace FloatPHP\Helpers\Filesystem\storage;

use FloatPHP\Classes\Filesystem\{
	File, Arrayify
};
use SleekDB\Store;

/**
 * Wrapper class for FileStorage (Database).
 * @see https://sleekdb.github.io
 */
class FileStorage
{
	/**
	 * @access protected
	 * @var object $adapter
	 */
	protected $adapter;

	/**
	 * @param string $table
	 * @param string $dir
	 * @param array $config
	 */
	public function __construct($table = 'table', $dir = 'database', $config = [])
	{
		if ( !File::isDir($dir) ) {
			File::addDir($dir);
		}

		// Init config
		$config = Arrayify::merge([
			'timeout'           => false,
			'folderPermissions' => 777
		], $config);

		$this->adapter = new Store($table, $dir, $config);
	}

	/**
	 * @access public
	 * @param void
	 * @return object
	 */
	public function getAdapter() : object
	{
		return $this->adapter;
	}
}

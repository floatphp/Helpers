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

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Classes\Filesystem\Exception as ErrorHandler;
use SleekDB\Store;

/**
 * Wrapper class for file based NoSQL.
 * @see https://sleekdb.github.io
 */
final class Storage
{
	use \FloatPHP\Kernel\TraitConfiguration;
	
    /**
     * @access private
     * @var object $instance, Storage instance
     */
    private $instance;

	/**
	 * Init storage.
	 * 
	 * @param string $table
	 * @param string $key
	 * @param array $config
	 * @param string $dir
	 * @uses initConfig()
	 * @uses resetConfig()
	 */
	public function __construct(string $table, ?string $key = null, array $config = [], string $dir = 'db')
	{
		// Init configuration
		$this->initConfig();

		// Init directory
		$dir = $this->getAdminUploadPath($dir);
		if ( !$this->isDir($dir) ) {
			$this->addDir($dir);
		}

		// Init config
		if ( !$key ) $key = "{$table}Id";
		$config = $this->mergeArray([
			'timeout'            => false,
			'primary_key'        => $key,
			'folder_permissions' => 777
		], $config);

		// Init instance
		try {
			$this->instance = new Store($table, $dir, $config);

		} catch (\SleekDB\Exceptions\IOException $e) {

			ErrorHandler::clearLastError();
			$logger = new Logger('core', 'system');
			$logger->error('File cache failed');
			if ( $this->isDebug() ) {
				$logger->debug($e->getMessage());
			}
		}

        // Reset configuration
        $this->resetConfig();
	}
	
    /**
     * Create row.
     *
     * @access public
     * @param array $where
     * @return array
     */
    public function create(array $data) : array
	{
		return $this->instance->insert($data);
	}

    /**
     * Read row.
     *
     * @access public
     * @param array $where
     * @return array
     */
    public function read(array $where) : array
	{
		$query = $this->instance->createQueryBuilder();
		$query->where($where)->limit(1);
		return $query->getQuery()->fetch();
	}

    /**
     * Update row by Id.
     *
     * @access public
     * @param int $id
     * @param array $where
     * @return bool
     */
    public function update(int $id, array $data) : bool
	{
		return (bool)$this->instance->updateById($id, $data);
	}

    /**
     * Delete row by Id.
     *
     * @access public
     * @param int $id
     * @return bool
     */
    public function delete(int $id) : bool
	{
		return $this->instance->deleteById($id);
	}

	/**
	 * Delete rows.
	 *
	 * @access public
	 * @param array $where
	 * @return int
	 */
	public function deleteAny(array $where) : int
	{
		return (int)$this->instance->deleteBy($where);
	}

	/**
	 * Get all rows.
	 *
	 * @access public
	 * @param array $order
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function all(?array $order = null, ?int $limit = null, ?int $offset = null) : array
	{
		return $this->instance->findAll($order, $limit, $offset);
	}

	/**
	 * Search rows.
	 *
	 * @access public
	 * @param array $where
	 * @param array $order
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function search(array $where, ?array $order = null, ?int $limit = null, ?int $offset = null) : array
	{
		return $this->instance->findBy($where, $order, $limit, $offset);
	}

	/**
	 * Search row.
	 *
	 * @access public
	 * @param array $where
	 * @return array
	 */
	public function searchOne(array $where) : array
	{
		$row = $this->instance->findOneBy($where);
		return ($row) ? $row : [];
	}

    /**
     * Check row.
     *
     * @access public
     * @param array $where
     * @return bool
     */
    public function exists(array $where) : bool
	{
		$query = $this->instance->createQueryBuilder();
		return $query->where($where)->getQuery()->exists();
	}

	/**
	 * Count rows.
	 *
	 * @access public
	 * @return int
	 */
	public function count() : int
	{
		return $this->instance->count();
	}

	/**
	 * Get storage instance.
	 *
	 * @access public
	 * @return object
	 */
	public function getInstance() : object
	{
		return $this->instance;
	}
}

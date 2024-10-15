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

use SleekDB\Store;

/**
 * Wrapper class for file based NoSQL.
 * @see https://sleekdb.github.io
 */
final class Storage
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\inc\TraitThrowable,
		\FloatPHP\Helpers\Framework\inc\TraitLoggable;

    /**
     * @access private
	 * @var bool $initialized
     * @var object $instance, Storage instance
     */
	private static $initialized = false;
    private $instance;

	/**
	 * Init storage.
	 *
	 * @inheritdoc
	 */
    public function __construct(array $config = [])
    {
		if ( !static::$initialized ) {

			$this->initConfig();

			$config = $this->mergeArray([
				'dir'   => 'db',
				'table' => 'sys',
				'key'   => null
			], $config);

			try {

				$dir = $this->getAdminUploadPath($config['dir']);
				if ( !$this->isDir($dir) ) {
					$this->addDir($dir);
				}

				$table = $config['table'];
				$key   = $config['key'] ?: "{$table}Id";

				$this->instance = new Store($table, $dir, [
					'timeout'            => false,
					'primary_key'        => $key,
					'folder_permissions' => 755
				]);
	
			} catch (\SleekDB\Exceptions\IOException $e) {

				$this->clearLastError();

				if ( $this->isDebug() ) {
					$this->error('Storage failed');
					$this->debug($e->getMessage());
				}

			}

        	$this->resetConfig();

		}
    }

    /**
     * Insert row.
     *
     * @access public
     * @param array $data
     * @return array
     */
    public function insert(array $data) : array
	{
		return $this->getInstance()->insert($data);
	}

    /**
     * Fetch row.
     *
     * @access public
     * @param array $where
     * @return array
     */
    public function fetch(array $where) : array
	{
		$query = $this->getInstance()->createQueryBuilder();
		$query->where($where)->limit(1);
		return $query->getQuery()->fetch();
	}

    /**
     * Update row by Id.
     *
     * @access public
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data) : bool
	{
		return (bool)$this->getInstance()->updateById($id, $data);
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
		return $this->getInstance()->deleteById($id);
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
		return (int)$this->getInstance()->deleteBy($where);
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
		return $this->getInstance()->findAll($order, $limit, $offset);
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
		return $this->getInstance()->findBy($where, $order, $limit, $offset);
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
		$row = $this->getInstance()->findOneBy($where);
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
		$query = $this->getInstance()->createQueryBuilder();
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
		return $this->getInstance()->count();
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

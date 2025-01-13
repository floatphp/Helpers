<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

/**
 * Built-in Array based database.
 * @see https://jakiboy.github.io/Sqalar/
 */
class Sqalar extends \ArrayObject
{
	/**
	 * @access private
	 * @var object $db, Source data array
	 * @var array $data, // Result data array after where
	 * @var array $result, // Result data array after column filter
	 * @var array $columns, // Columns in select query
	 * @var array $where, // Where statment
	 * @var int $limit, // Result data limit
	 * @var bool $error, // Display error
	 * @var string $operator, // Operator (like, =, >, <, contain)
	 * @todo $operator, // Operator (like, =, >, <, contain)
	 */
	private $db;
	private $data = [];
	private $result = [];
	private $columns = [];
	private $where = [];
	private $limit = 0;
	private $error = false;
	private $operator;

	/**
	 * @access public
	 * @var int $count, Base data count
	 */
	public $count;

	/**
	 * Init array db.
	 *
	 * @access public
	 * @param array $entry
	 */
	public function __construct(array $entry)
	{
		if ( $this->isMultiple($entry) ) {
			$this->db = new parent($entry);
			$this->count = $this->db->count();
		}
	}

	/**
	 * Select ORM behavior.
	 * 
	 * @access public
	 * @param mixed $column
	 * @return object
	 */
	public function select($column = '*') : self
	{
		if ( $column == '*' ) {
			$this->selectDefault();

		} elseif ( !is_array($column) && strpos($column, ',') !== false ) {

			$column = preg_replace('/\s+/', '', $column);
			$column = explode(',', $column);
			$this->columns = $column;

		} else {
			$this->columns = is_array($column) ? $column : [$column];
		}

		return $this;
	}

	/**
	 * When empty select Set default columns,
	 * from db entries of first array.
	 *
	 * @access private
	 * @return void
	 */
	private function selectDefault() : void
	{
		if ( isset($this->db[0]) ) {
			$this->columns = array_keys($this->db[0]);
		}
	}

	/**
	 * Show error.
	 *
	 * @access public
	 * @return object
	 */
	public function debug() : self
	{
		$this->error = true;
		return $this;
	}

	/**
	 * Set where clause.
	 *
	 * @access public
	 * @param array $where
	 * @return object
	 */
	public function where(array $where) : self
	{
		if ( !$this->columns ) {
			$this->selectDefault();
		}
		$this->where = $where;
		return $this;
	}

	/**
	 * Set result limit.
	 *
	 * @access public
	 * @param int $limit
	 * @return object
	 */
	public function limit(int $limit) : self
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Distinct result.
	 *
	 * @access public
	 * @param string $column
	 * @return object
	 * @todo
	 */
	public function distinct(string $column) : self
	{
		// ...
	}

	/**
	 * Randomize result.
	 *
	 * @access public
	 * @return object
	 */
	public function random() : self
	{
		$this->db = (array)$this->db;
		shuffle($this->db);
		return $this;
	}

	/**
	 * Order result.
	 *
	 * @access public
	 * @param string $col
	 * @param string $order
	 * @return object
	 */
	public function order(string $col, ?string $order = null) : self
	{
		$new = [];
		$sortable = [];

		if ( count($this->db) > 0 ) {

			foreach ($this->db as $k => $v) {
				if ( is_array($v) ) {
					foreach ($v as $k2 => $v2) {
						if ( $k2 == $col ) {
							$sortable[$k] = $v2;
						}
					}
				} else {
					$sortable[$k] = $v;
				}
			}

			switch ($order) {
				case 'asc':
					asort($sortable);
					break;
				case 'desc':
					arsort($sortable);
					break;
				default:
					asort($sortable);
					break;
			}

			foreach ($sortable as $k => $v) {
				$new[$k] = $this->db[$k];
			}
		}

		$this->db = $new;
		return $this;
	}

	/**
	 * Performe query.
	 *
	 * @access public
	 * @param string $response
	 * @return mixed
	 */
	public function query(?string $response = null) : mixed
	{
		if ( $response == 'json' ) {
			return json_encode($this->buildQuery());
		}
		return $this->buildQuery();
	}

	/**
	 * Build query (Expesive).
	 *
	 * @access protected
	 * @return mixed
	 */
	protected function buildQuery() : mixed
	{
		if ( $this->isMultipleWhere($this->where) ) {

			if ( !$this->db ) {
				$this->db = [];
			}

			foreach ($this->db as $key => $row) {

				if ( empty($row) ) return;

				foreach ($this->where as $where) {

					if ( empty($where) ) {
						throw new \Exception('Invalid Where statment');
					}

					if ( !isset($this->db[$key][$where['column']]) ) {
						throw new \Exception('Invalid column name');
					}

					$source = $this->db[$key][$where['column']];
					$search = $where['value'];

					// Start Operator
					switch (strtolower($where['link'])) {
						case '=': // int
							if ( $source === $search )
								$this->data[$key] = $this->db[$key];
							break;

						case 'like': // int|string
							if ( $source == $search )
								$this->data[$key] = $this->db[$key];
							break;

						case '%=': // int|string
							if ( strcasecmp($source, $search) == 0 )
								$this->data[$key] = $this->db[$key];
							break;

						case '%like': // int|string
							if ( strcasecmp($source, $search) == 0 )
								$this->data[$key] = $this->db[$key];
							break;

						case '!=': // int|string
							if ( $source !== $search )
								$this->data[$key] = $this->db[$key];
							break;

						case '!like': // int|string
							if ( $source !== $search )
								$this->data[$key] = $this->db[$key];
							break;

						case 'in': // string
							if ( strpos($source, $search) !== false )
								$this->data[$key] = $this->db[$key];
							break;

						case '%in': // string
							if ( strpos(strtolower($source), strtolower($search)) !== false )
								$this->data[$key] = $this->db[$key];
							break;

						case '!in': // string
							if ( strpos($source, $search) == false )
								$this->data[$key] = $this->db[$key];
							break;

						case '<': // string|int
							if ( $source < $search )
								$this->data[$key] = $this->db[$key];
							break;

						case '<=': // string|int
							if ( $source <= $search )
								$this->data[$key] = $this->db[$key];
							break;

						case '>': // string|int
							if ( $source > $search )
								$this->data[$key] = $this->db[$key];
							break;

						case '>=': // string|int
							if ( $source >= $search )
								$this->data[$key] = $this->db[$key];
							break;

						default:
							$this->db = [];
							break;
					}
				}
			}

		} elseif ( $this->isSingleWhere($this->where) ) {

			foreach ($this->db as $key => $row) {

				foreach ($this->where as $filter => $value) {

					if ( $this->db[$key][$filter] === $value ) {
						$this->data[$key] = $this->db[$key];
					}
				}
			}

		} else {
			$this->data = $this->db;
		}

		// Reset database if nothing found
		if ( empty($this->data) ) {
			$this->db = [];
		}

		if ( $this->isValidColumn($this->columns) ) {

			// Get database if no where statement
			$this->data = $this->data ?: $this->db;

			foreach ($this->data as $key => $row) {

				foreach ($this->columns as $column) {

					if ( isset($this->data[$key][$column]) ) {
						// Set result
						$this->result[$key][$column] = $this->data[$key][$column];
						$this->result[$key] = array_filter($this->result[$key]);

					} else {

						if ( $this->error ) {
							throw new \Exception("Invalid column name : '{$column}'");
						}
					}
				}
			}
		}

		// Apply Limit
		if ( $this->limit ) {
			$this->result = array_slice($this->result, 0, $this->limit);
		}

		// Reset keys
		$this->result = array_values($this->result);
		return $this->result;
	}

	/**
	 * Valid column.
	 *
	 * @access private
	 * @param array $column
	 * @return bool
	 */
	private function isValidColumn(array $column) : bool
	{
		return $column && $column[0] != '*';
	}

	/**
	 * Check multiple where clause.
	 *
	 * @access private
	 * @param array $where
	 * @return bool
	 */
	private function isMultipleWhere(array $where) : bool
	{
		return $this->isValidWhere($where)
			&& $this->isMultiple($where);
	}

	/**
	 * Check single where clause.
	 *
	 * @access private
	 * @param array $where
	 * @return bool
	 */
	private function isSingleWhere(array $where) : bool
	{
		return $this->isValidWhere($where)
			&& !$this->isMultiple($where);
	}

	/**
	 * Validate where clause.
	 *
	 * @access private
	 * @param array $where
	 * @return bool
	 */
	private function isValidWhere(array $where) : bool
	{
		return $where && is_array($where);
	}

	/**
	 * Check 2D array.
	 *
	 * @access private
	 * @param array $data
	 * @return bool
	 */
	private function isMultiple($data) : bool
	{
		if ( $this->depth($data) > 2 ) {
			throw new \Exception('Invalid 2D Array');
		}

		$data = array_filter($data, 'is_array');
		if ( count($data) > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Get array dept.
	 *
	 * @access private
	 * @param array $array
	 * @return int
	 */
	private function depth(array $array) : int
	{
		$max = 1;
		foreach ($array as $value) {
			if ( is_array($value) ) {
				$depth = $this->depth($value) + 1;
				if ( $depth > $max ) {
					$max = $depth;
				}
			}
		}
		return $max;
	}
}

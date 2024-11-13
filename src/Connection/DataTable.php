<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Helpers\Filesystem\Cache;
use FloatPHP\Helpers\Http\Catcher;
use FloatPHP\Kernel\Orm;

/**
 * Built-in DataTable class for server-side rendering.
 * @see https://datatables.net
 */
class DataTable extends Orm
{
	use \FloatPHP\Helpers\Framework\inc\TraitCacheable,
		\FloatPHP\Helpers\Framework\inc\TraitRequestable,
		\FloatPHP\Helpers\Framework\inc\TraitDatable,
		\FloatPHP\Helpers\Framework\inc\TraitTranslatable,
		\FloatPHP\Helpers\Framework\inc\TraitLoggable;

    /**
     * @access protected
     * @var array $columns, Table columns
     * @var array $request, Table request
     * @var array $search, Search binded data
     * @var array $match, Custom columns keys
     * @var int $total, Table total
     * @var int $filtered, Search count
     * @var int $chars, Search length
     */
	protected $columns = [];
	protected $request = [];
	protected $search = [];
	protected $match = [];
	protected $total = 0;
	protected $filtered = 0;
	protected $chars = 30;

	/**
	 * Render server-side data.
	 *
	 * @access public
	 * @return string
	 */
	public function render() : string
	{
		return $this->build();
	}

	/**
	 * Format static data,
	 * '{"data":[["XXXX"],["XXXX"]]}'.
	 *
	 * @access public
	 * @param array $data
	 * @return string
	 */
	public function format(array $data = []) : string
	{
		$json = $this->formatJson($data);
		$prefix = '"data":';
		return "{{$prefix}{$json}}";
	}

	/**
	 * @inheritdoc
	 */
	public function noCache() : self
	{
		$this->useCache = false;
		return $this;
	}

	/**
	 * Build datatable response.
	 *
	 * @access protected
	 * @param string $table
	 * @param string $key
	 * @param array $columns
	 * @return string
	 */
	protected function build(?string $table = null, ?string $key = null, array $columns = []) : string
	{
		// Init ORM
		parent::__construct();
		
		// Set table
		$this->table   = ($table) ? $table : (new Catcher())->key;
		$this->key     = ($key) ? $key : "{$this->table}Id";
		$this->columns = ($columns) ? $columns : $this->columns();
		
		// Set request
		$this->request = $this->getRequest();

		// Init count
		$this->filtered = $this->total = $this->count();

		// Init translator
		$this->getTranslatorObject();

		// Get response
		return $this->getResponse();
	}

	/**
	 * Pre-render server-side data,
	 * Columns should match rendred data.
	 *
	 * @access protected
	 * @param array $data
	 * @return array
	 */
	protected function preRender(array $data = []) : array
	{
		return $data;
	}

	/**
	 * Match custom columns keys.
	 *
	 * @access protected
	 * @param array $match
	 * @return void
	 */
	protected function match(array $match = [])
	{
		$this->match = $match;
	}

	/**
	 * Get search pattern.
	 *
	 * @access protected
	 * @param string $search
	 * @return string
	 */
	protected function getSearchPattern(string $search) : string
	{
		if ( (strlen($search) < 3) && intval($search) ) {
			return $search;
		}
		return "%{$search}%";
	}

	/**
	 * Get start.
	 *
	 * @access protected
	 * @return int
	 */
	protected function getStart() : int
	{
		return isset($this->request['start']) 
		? intval($this->request['start']) : 0;
	}

	/**
	 * Get length.
	 *
	 * @access protected
	 * @return int
	 */
	protected function getLength() : int
	{
		return isset($this->request['length']) 
		? intval($this->request['length']) : 1;
	}

	/**
	 * Get draw.
	 *
	 * @access protected
	 * @return int
	 */
	protected function getDraw() : int
	{
		return isset($this->request['draw']) 
		? intval($this->request['draw']) : 0;
	}

	/**
	 * Get rows.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getRows() : string
	{
		$fields = [];
		foreach ($this->columns as $key => $value) {
			$fields[$key] = "`{$value}`";
		}
		$rows = implode(', ', $fields);
		if ( empty($rows) ) {
			$rows = '*';
		}
		return $rows;
	}

	/**
	 * Get orderBy.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getOrderBy() : string
	{
		$orderBy = $this->key;

		if ( isset($this->request['order'][0]['column']) ) {

			$column = (int)$this->request['order'][0]['column'];

			if ( $this->match ) {
				if ( isset($this->match[$column]) ) {
					$orderBy = $this->match[$column];
				}

			} else {
				if ( isset($this->columns[$column]) ) {
					$orderBy = $this->columns[$column];
				}
			}
		}

		return $orderBy;
	}

	/**
	 * Get order.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getOrder() : string
	{
		return $this->request['order'][0]['dir'] ?? 'asc';
	}

	/**
	 * Get search.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getSearch() : string
	{
		$search = $this->request['search']['value'] ?? '';
		return substr($search, 0, $this->chars);
	}

	/**
	 * Get query.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getQuery() : string
	{
		// Begin query
		$sql = "SELECT {$this->getRows()} FROM `{$this->table}`";

		// Search query
		$sql .= $this->getSearchQuery();

		// Order query
		$sql .= $this->getOrderQuery();

		// End query
		$sql .= "LIMIT {$this->getStart()}, {$this->getLength()};";

		// Log query
		if ( $this->isDebug() ) {
			$this->logger->debug($sql);
		}

		return $sql;
	}

	/**
	 * Get search query.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getSearchQuery() : string
	{
		// Init search query
		$sql = '';

		if ( !empty($search = $this->getSearch()) ) {

			$where = '';
			$columns = $this->columns;

		    // Set where clause
			if ( $this->match ) {
				$columns = $this->match;
			}

		    foreach ($columns as $key => $column) {
		    	if ( $column !== $this->key ) {
			   		if ( isset($this->request['columns'][$key]) ) {
			   			$option = $this->request['columns'][$key];
						if ( isset($option['searchable']) && $option['searchable'] == 'true' ) {
							$this->search[$column] = $this->getSearchPattern($search);
							$where .= "(`{$column}` LIKE :{$column}) OR ";
						}
			   		}
		    	}
		    }

		    // Format where clause
		    if ( !empty($where) ) {
		    	$where = substr(trim($where), 0, -3);
		    	$where = "WHERE {$where}";
		    	$sql .= " {$where} ";
		    }
		}

		return $sql;
	}

	/**
	 * Get order query.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getOrderQuery() : string
	{
		return " ORDER BY `{$this->getOrderBy()}` {$this->getOrder()} ";
	}

	/**
	 * Set search count (No cache).
	 *
	 * @access protected
	 * @return void
	 */
	protected function setSearchCount()
	{
		$query = "SELECT COUNT(`{$this->key}`) FROM `{$this->table}` {$this->getSearchQuery()};";
		$this->filtered = (int)$this->query($query, $this->search, 'single');
	}

	/**
	 * Get data.
	 *
	 * @access protected
	 * @return array
	 */
	protected function getData() : array
	{
		// Set filtered data outside cache
		$this->setSearchCount();

		if ( $this->useCache ) {

			$cache = new Cache();
			$key   = $cache->getKey($this->table, [
				'start'    => $this->getStart(),
				'length'   => $this->getLength(),
				'orderby'  => $this->getOrderBy(),
				'order'    => $this->getOrder(),
				'search'   => $this->getSearch(),
				'total'    => $this->total,
				'filtered' => $this->filtered
			]);

			$data = $cache->get($key, $status);
			if ( !$status ) {
				$data = $this->prepare();
				$cache->validate()->set($key, $data, 0, $this->table);
			}
			return $data;
		}

		return $this->prepare();
	}

	/**
	 * Execute query and prepare formatted data.
	 *
	 * @access protected
	 * @return array
	 */
	protected function prepare() : array
	{
		// Set data
		$data = $this->query($this->getQuery(), $this->search);

		// Override data
		$data = $this->preRender($data);

		// Append action column
		$data = $this->appendAction($data);

		// Verify data
		$this->verify($data);

		// Format data
		return $this->formatData($data);
	}

	/**
	 * Verify data and columns.
	 *
	 * @access protected
	 * @return void
	 */
	protected function verify(array $data)
	{
		if ( $this->isDebug() && !$this->match ) {
			$d = $data[0] ?? [];
			$d = count($d);
			$c = count($this->columns);
			if ( $this->hasAction() ) $c++;
			if ( $this->getOrderBy() !== $this->key && ($c !== $d) ) {
				$this->logger->warning("Datatable '{$this->table}' : Data does not match colmuns");
			}
		}
	}

	/**
	 * Get action.
	 *
	 * @access protected
	 * @return bool
	 */
	protected function hasAction() : bool
	{
		if ( isset($this->request['action']) ) {
			return ($this->request['action'] == 'true');
		}
		return false;
	}

	/**
	 * Append action column.
	 *
	 * @access protected
	 * @return array
	 */
	protected function appendAction(array $data = []) : array
	{
		if ( $this->hasAction() ) {
			foreach ($data as $key => $value) {
				$data[$key]['action'] = '{action}';
			}
		}
		return $data;
	}

	/**
	 * Format data for datatable.
	 *
	 * @access protected
	 * @param array $data
	 * @return array
	 */
	protected function formatData(array $data = []) : array
	{
		return $this->mapArray('values', $data);
	}

	/**
	 * Get response.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getResponse() : string
	{
		return $this->formatJson([
			'draw'            => $this->getDraw(),
			'data'            => $this->getData(), // Before count
			'recordsTotal'    => $this->total,
			'recordsFiltered' => $this->filtered
		]);
	}
}

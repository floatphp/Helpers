<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers;

use FloatPHP\Kernel\Orm;
use FloatPHP\Kernel\OrmQuery;
use FloatPHP\Classes\Filesystem\Json;
use FloatPHP\Classes\Http\Request;

abstract class AbstractDataTable extends Orm
{
	/**
	 * @access private
	 * @var Object $orm
	 * @var array $columns
	 * @var array $request POST
	 */
	private $orm;
	private $columns = [];
	private $request;

	/**
	 * @access public
	 * @param array $data
	 * @return string
	 */
	abstract public static function render($data) : string;

	/**
	 * Server Render
	 *
	 * @access public
	 * @param string $table
	 * @param string $key
	 * @param array $columns
	 * @return string
	 */
	public function serverRender($table, $key, $columns) : string
	{
		return $this->serverFormat($table,$key,$columns);
	}

	/**
	 * Simple Format datatable
	 * '{"data":[["X","X"]]}'
	 *
	 * @access protected
	 * @param array $data
	 * @return string
	 */
	protected static function format($data) : string
	{
		$json = Json::format($data);
		$prefix = '"data": ';
		return "{{$prefix}{$json}}";
	}

	/**
	 * Server Format
	 *
	 * @access protected
	 * @param string $table
	 * @param string $key
	 * @param array $columns
	 * @return string
	 */
	protected function serverFormat($table, $key, $columns) : string
	{
		$this->init();
		$this->key = $key;
		$this->table = $table;
		$this->columns = $columns;
		$this->request = Request::get();
		$this->total = $this->count();

		// Check if its simple request or search request
		if ( !$this->isSearch() ) {
			$data = $this->fetch();
			$totalFilter = $this->total;
		} else {
			$data = $this->serverSearch();
			$totalFilter = $this->total;
		}

		// Format Server data output
		$wrapper = [];
		foreach ($data as $column) {
			array_push($wrapper, array_values($column));
		}

		// Combine result
		$result = [
			'draw'            => intval($this->request['draw']),
			'recordsTotal'    => intval($this->total),
			'recordsFiltered' => intval($totalFilter),
			'data'            => $wrapper
		];

		return Json::format($result);
	}

    /**
     * Fetch data
     *
     * @access private
     * @param void
     * @return array
     */
    private function fetch() : array
    {
    	$rows = implode(', ',$this->columns);
    	$sql  = "SELECT {$rows} FROM `{$this->table}` ORDER BY ";

    	$order  = $this->columns[$this->request['order'][0]['column']];
    	$dir    = $this->request['order'][0]['dir'];
    	$start  = $this->request['start'];
    	$length = $this->request['length'];

    	$sql .= "{$order} {$dir} LIMIT {$start}, {$length}";
    	return $this->db->query($sql);
    }

    /**
     * Get search data
     *
     * @access private
     * @param void
     * @return array
     */
    private function serverSearch() : array
    {
    	$rows = implode(',',$this->columns);
    	$sql  = "SELECT {$rows} FROM `{$this->table}` WHERE ";

    	// Dynamically set binded and SQL
    	foreach ($this->columns as $column) {
    		$bind[$column] = "%{$this->request['search']['value']}%";
    		$sql .= "({$column} LIKE :{$column}) OR ";
    	}

    	// Remove last 'OR'
    	$sql = substr($sql,0,-3);

    	// Small break to get filtred count
    	$this->total = $this->db->query($sql,$bind);

    	// Continue query
    	$order  = $this->columns[$this->request['order'][0]['column']];
    	$dir    = $this->request['order'][0]['dir'];
    	$start  = $this->request['start'];
    	$length = $this->request['length'];
    	$sql .= "ORDER BY {$order} {$dir} LIMIT {$start}, {$length}";

    	return $this->db->query($sql,$bind);
    }

	/**
	 * Check if request is search
	 *
	 * @access private
	 * @param void
	 * @return bool
	 */
	private function isSearch() : bool
	{
		if ( !empty($this->request['search']['value']) ) {
			return true;
		}
		return false;
	}
}

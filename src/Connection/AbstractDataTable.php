<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2022 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\Orm;
use FloatPHP\Helpers\Filesystem\Cache;
use FloatPHP\Classes\Http\Request;
use FloatPHP\Classes\Filesystem\{
    TypeCheck, Stringify, Arrayify, Json
};

abstract class AbstractDataTable extends Orm
{
	/**
	 * Data render
	 *
	 * @access public
	 * @param array $data
	 * @return string
	 */
	abstract public static function render($data) : string;

	/**
	 * Server render
	 *
	 * @access public
	 * @param string $table
	 * @param string $primaryKey
	 * @param array $columns
	 * @param int $ttl
	 * @return string
	 */
	public function serverRender($table, $primaryKey, $columns = [], $ttl = 3600) : string
	{
		// Init database
		parent::__construct();
		$this->table = $table;
		$this->key = $primaryKey;
		$bind = [];

		// Catch request
		$request = Request::get();

		// Set total
		$filtered = $total = $this->count();
		
		// Set start
		$start = 0;
		if ( isset($request['start']) ) {
			$start = intval($request['start']);
		}

		// Set length
		$length = 1;
		if ( isset($request['length']) ) {
			$length = intval($request['length']);
		}

		// Set draw
		$draw = 0;
		if ( isset($request['draw']) ) {
			$draw = intval($request['draw']);
		}

		// Set rows
		$fields = [];
		foreach ($columns as $key => $value) {
			$fields[$key] = "`{$value}`";
		}
		$rows = implode(',',$fields);
		if ( empty($rows) ) {
			$rows = '*';
		}

		// Set order by
		$orderBy = $this->key;
		if ( isset($request['order'][0]['column']) ) {
			$column = $request['order'][0]['column'];
			if ( isset($columns[$column]) ) {
				$orderBy = $columns[$column];
			}
		}

		// Set order
		$order = 'asc';
		if ( isset($request['order'][0]['dir']) ) {
			$order = $request['order'][0]['dir'];
		}

		// Init cache
		$cache = new Cache('temp',$ttl);
		$cacheId  = "datatable-{$this->table}-{$this->key}-{$start}-{$length}-";
		$cacheId .= "{$orderBy}-{$order}";

		// Begin query
		$sql = "SELECT {$rows} FROM `{$this->table}`";

		// Check search request
		if ( isset($request['search']['value']) ) {
			if ( !empty($search = $request['search']['value']) ) {
				$cacheId .= "-{$search}";
				$where = '';
		    	// Set search bind
		    	foreach ($columns as $key => $column) {
		    		if ( $column !== $this->key ) {
			    		if ( isset($request['columns'][$key]) ) {
			    			$option = $request['columns'][$key];
			    			if ( isset($option['searchable']) ) {
			    				if ( $option['searchable'] == 'true' ) {
						    		$bind[$column] = "%{$search}%";
						    		$where .= "(`{$column}` LIKE :{$column}) OR ";
			    				}
			    			}
			    		}
		    		}
		    	}
		    	// Format where query
		    	if ( !empty($where) ) {
		    		$where = substr($where,0,-3);
		    		$where = " WHERE {$where}";
		    		$sql .= $where;
		    	}
				// Get filtered count
		    	$count = "SELECT COUNT(`{$this->key}`) FROM `{$this->table}` {$where}";
    			$filtered = $this->db->single($count,$bind);
			}
		}

		// End query
		$sql .= " ORDER BY `{$orderBy}` {$order} ";
		$sql .= "LIMIT {$start}, {$length};";

		// Use cache
		$cacheId = Stringify::slugify($cacheId);
		$data = $cache->get($cacheId);
		if ( !$cache->isCached() ) {
			// Execute query
			$data = $this->db->query($sql,$bind);
			// Format server data
			$data = static::serverPreRender($data);
			$cache->set($data,[$this->table,'data']);
		}

		// Format server data output
		$wrapper = [];
		foreach ((array)$data as $column) {
			if ( isset($request['customColumn']) ) {
				if ( TypeCheck::isArray($request['customColumn']) ) {
					foreach ($request['customColumn'] as $key => $value) {
						$column["custom-col-{$key}"] = $value;
					}
				} else {
					$column['custom-col'] = $request['customColumn'];
				}
			}
			Arrayify::push($wrapper,Arrayify::values($column));
		}

		// Combine response
		$response = [
			'draw'            => $draw,
			'recordsTotal'    => $total,
			'recordsFiltered' => $filtered,
			'data'            => $wrapper
		];

		return Json::format($response);
	}

	/**
	 * Pre-render data
	 *
	 * @access protected
	 * @param array $data
	 * @return array
	 */
	protected static function serverPreRender($data = []) : array
	{
		return $data;
	}

	/**
	 * Data format
	 * '{"data":[["X","X"]]}'
	 *
	 * @access protected
	 * @param array $data
	 * @return string
	 */
	protected static function format($data = []) : string
	{
		$json = Json::format($data);
		$prefix = '"data": ';
		return (string)"{{$prefix}{$json}}";
	}
}

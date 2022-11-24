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

use FloatPHP\Helpers\Filesystem\Storage;
use FloatPHP\Classes\Http\Request;
use FloatPHP\Classes\Filesystem\{
    TypeCheck, Arrayify, Json
};

/**
 * Storage DataTable
 */
abstract class AbstractSDT extends AbstractDataTable
{
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
	public function serverRender($table, $primaryKey = '_id', $columns = [], $ttl = 3600) : string
	{
		// Catch request
		$request = Request::get();

		// Init storage
		$storage = new Storage($table,[
			'primary_key' => $primaryKey,
			'timeout'     => false
		]);

		// Set total
		$filtered = $total = $storage->getAdapter()->count();
		
		// Set start
		$start = 0;
		if ( isset($request['start']) ) {
			$start = intval($request['start']);
		}

		// Set length
		$length = 5;
		if ( isset($request['length']) ) {
			$length = intval($request['length']);
		}

		// Set draw
		$draw = 0;
		if ( isset($request['draw']) ) {
			$draw = intval($request['draw']);
		}

		// Set order by
		$orderBy = $primaryKey;
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

		// Begin select query
		$query = $storage->getAdapter()->createQueryBuilder()->useCache($ttl)
		->select($columns);

		// Check search request
		if ( isset($request['search']['value']) ) {
			if ( !empty($search = $request['search']['value']) ) {
				$where = [];
		    	// Set search
		    	foreach ($columns as $key => $column) {
		    		if ( $column !== $primaryKey ) {
			    		if ( isset($request['columns'][$key]) ) {
			    			$option = $request['columns'][$key];
			    			if ( isset($option['searchable']) ) {
			    				if ( $option['searchable'] == 'true' ) {
			    					$where[] = [$column,'LIKE',"%{$search}%"];
			    					$where[] = 'OR';
			    				}
			    			}
			    		}
		    		}
		    	}
		    	// Set where query
		    	if ( $where ) {
		    		$last = count($where) - 1;
		    		unset($where[$last]);
		    		$query->where($where);
		    		// Set search filtered count
		    		$filtered = count($query->getQuery()->fetch());
		    	}
			}
		}

		// End query
		$query->skip($start)
		->limit($length)
		->orderBy([$orderBy => $order]);

		// Execute query
		$data = $query->getQuery()->fetch();

		// Format server data
		$data = static::serverPreRender($data);

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
}

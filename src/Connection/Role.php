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

use FloatPHP\Kernel\Model;

class Role extends Model
{
	/**
	 * @access protected
	 */
	protected $table = 'role';
	protected $key = 'roleId';

	/**
	 * @access public
	 * @param int $roleId
	 * @return array
	 */
	public function get($roleId = 0) : array
	{
		$this->roleId = intval($roleId);
		$this->find();
		return (array)$this->data;
	}

	/**
	 * @access public
	 * @param int $roleId
	 * @return string
	 */
	public function getSlug($roleId = 0) : string
	{
		$this->roleId = intval($roleId);
		$this->find();
		return (string)$this->slug;
	}

	/**
	 * @access public
	 * @param int $roleId
	 * @return string
	 */
	public function getName($roleId = 0) : string
	{
		$this->roleId = intval($roleId);
		$this->find();
		return (string)$this->name;
	}

	/**
	 * @access public
	 * @param int $roleId
	 * @return string
	 */
	public function getCapability($roleId = 0) : string
	{
		$this->roleId = intval($roleId);
		$this->find();
		return (string)$this->capability;
	}

	/**
	 * @access public
	 * @param int $data
	 * @return bool
	 */
	public function add($data = []) : bool
	{
		$this->$data = $data;
		return (bool)$this->create();
	}

	/**
	 * @access public
	 * @param int $data
	 * @return bool
	 */
	public function update($data = []) : bool
	{
		$this->$data = $data;
		return (bool)$this->save();
	}
}

<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\Model;

class User extends Model
{
	/**
	 * @access protected
	 * @var string $table
	 * @var string $key
	 */
	protected $table = 'user';
	protected $key = 'userId';

	/**
	 * @access public
	 * @param int $userId
	 * @return array
	 */
	public function get($userId = 0) : array
	{
		$this->userId = intval($userId);
		$this->find();
		return (array)$this->data;
	}

	/**
	 * @access public
	 * @param int $userId
	 * @return int
	 */
	public function getRoleId($userId = 0) : int
	{
		$this->userId = intval($userId);
		$this->find();
		return (int)$this->roleId;
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

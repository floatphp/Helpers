<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
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
	 * Get user by username or email.
	 *
	 * @access public
	 * @param string $user
	 * @return array
	 */
	public function getUser(string $user) : array
	{
		$this->bind(['username' => $user, 'email' => $user]);
		$sql = "SELECT * FROM `{$this->table}` ";
		$sql .= "WHERE `username` = :username OR `email` = :email;";
		return $this->getRow($sql);
	}

	/**
	 * Check user secret.
	 *
	 * @access public
	 * @param string $user
	 * @return bool
	 */
	public function hasSecret(string $user) : bool
	{
		$this->bind(['username' => $user, 'email' => $user]);
		$sql = "SELECT `secret` FROM `{$this->table}` ";
		$sql .= "WHERE `username` = :username OR `email` = :email;";
		return ($this->getSingle($sql)) ? true : false;
	}

	/**
	 * Get user table key.
	 *
	 * @access public
	 * @return string
	 */
	public function getKey() : string
	{
		return (string)$this->key;
	}

	/**
	 * Get user.
	 *
	 * @access public
	 * @param int $id
	 * @param bool $secured
	 * @return array
	 */
	public function getById(?int $id, bool $secured = true) : array
	{
		$user = (array)$this->get((int)$id);
		if ( $secured ) {
			unset($user['password']);
		}
		return $user;
	}

	/**
	 * Get user name.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getName(?int $id) : string
	{
		$this->get((int)$id);
		return (string)$this->name;
	}

	/**
	 * Get user role.
	 *
	 * @access public
	 * @param int $id
	 * @return int
	 */
	public function getRoleId(?int $id) : int
	{
		$this->get((int)$id);
		return (int)$this->roleId;
	}

	/**
	 * Update user.
	 *
	 * @access public
	 * @param array $data
	 * @return bool
	 */
	public function updateUser(array $data) : bool
	{
		return $this->save($data);
	}
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
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
	 * Get role slug.
	 * 
	 * @access public
	 * @param int $id
	 * @return string
	 */
	public function getSlug(?int $id) : string
	{
		$this->get((int)$id);
		return (string)$this->slug;
	}

	/**
	 * Get role name.
	 * 
	 * @access public
	 * @param int $id
	 * @return string
	 */
	public function getName(?int $id) : string
	{
		$this->get((int)$id);
		return (string)$this->name;
	}

	/**
	 * Get role capability.
	 * 
	 * @access public
	 * @param int $id
	 * @return string
	 */
	public function getCapability(?int $id) : string
	{
		$this->get((int)$id);
		return (string)$this->capability;
	}
}

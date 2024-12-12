<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

final class Configurator
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access public
	 * @param ?string $key
	 * @return object
	 */
	public function reflect(?string $key = null) : object
	{
		return $this->getConfig($key);
	}
}

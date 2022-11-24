<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2022 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\Filesystem\{
    TypeCheck, FileCache
};

final class Cache extends FileCache
{
	use TraitConfiguration;

	/**
	 * @param string $path
	 * @param int $ttl
	 */
	public function __construct($path = 'temp', $ttl = null)
	{
		// Init configuration
		$this->initConfig();
		// Set cache TTL
		$ttl = (TypeCheck::isNull($ttl)) ? $this->getCacheTTL() : (int)$ttl;
		// Instance cache
		parent::__construct([
			'path'        => "{$this->getCachePath()}/{$path}",
			'securityKey' => 'private'
		], $ttl);
	}

	/**
	 * Clear adapter instances
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function __destruct()
	{
		parent::__destruct();
	}
}

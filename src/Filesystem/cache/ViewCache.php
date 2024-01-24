<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem\cache;

use FloatPHP\Classes\Filesystem\{
	File, Stringify
};

final class ViewCache
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access private
	 * @var string $path
	 */
	private $path = 'view';

	/**
	 * @uses initConfig()
	 * @uses resetConfig()
	 */
    public function __construct()
    {
		// Init configuration
		$this->initConfig();

		// Set path
		$this->path = "{$this->getCachePath()}/{$this->path}";

		// Reset configuration
		$this->resetConfig();
    }

	/**
	 * Purge cache.
	 *
	 * @access public
	 * @return bool
	 */
	public function purge() : bool
	{
		if ( Stringify::contains($this->path, '/cache/') ) {
			return File::clearDir($this->path);
		}
		return false;
	}
}

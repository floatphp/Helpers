<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem\cache;

use FloatPHP\Classes\Filesystem\{
	File, Stringify
};
use FloatPHP\Kernel\TraitConfiguration;

final class ViewCache
{
	use TraitConfiguration;

	/**
	 * @access private
	 * @var string $path
	 */
	private $path = 'view';

	/**
	 * @param void
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
	 * @param void
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

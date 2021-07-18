<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Filesystem Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\Filesystem\Storage as ParentStorage;

class Storage extends ParentStorage
{
	use TraitConfiguration;

	/**
	 * @param string $table
	 * @param array $config
	 * @param string $dir
	 */
	public function __construct($table = 'table', $config = [], $dir = 'database')
	{
		// Init configuration
        $this->initConfig();
        // Override
        $dir = "{$this->getAdminUploadPath()}/{$dir}";
		// Instance cache
		parent::__construct($table,$dir,$config);
	}
}

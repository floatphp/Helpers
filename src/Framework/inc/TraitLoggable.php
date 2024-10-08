<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Helpers\Filesystem\Logger;

trait TraitLoggable
{
    /**
     * @access protected
     * @var object $logger, Logger object
     */
    protected $logger;

    /**
     * Get logger object.
     *
     * @access protected
     * @param string $path
     * @param string $file
     * @param string $ext
     * @return object
     */
    protected function getLoggerObject(string $path = '/core', string $file = 'debug', string $ext = 'log') : Logger
    {
		$this->logger = new Logger($path, $file, $ext);
        return $this->logger;
    }
}

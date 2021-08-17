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
use FloatPHP\Classes\Filesystem\Stringify;
use FloatPHP\Classes\Filesystem\Logger as ParentLogger;

final class Logger extends ParentLogger
{
    use TraitConfiguration;

    /**
     * @param string $path
     * @param string $filename
     * @param string $extension
     */
    public function __construct($path = '', $filename = 'debug', $extension = 'log')
    {
        // Init configuration
        $this->initConfig();

        // Override
        $path = "{$this->getLoggerPath()}/{$path}";
        $this->setPath(Stringify::formatPath($path,1));
        $this->setFilename($filename);
        $this->setExtension($extension);
    }
}

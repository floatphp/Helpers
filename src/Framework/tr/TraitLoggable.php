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

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Helpers\Filesystem\Logger;

/**
 * Define logging functions.
 */
trait TraitLoggable
{
    /**
     * Log debug message.
     *
     * @access protected
     * @inheritdoc
     */
    protected function debug($message, bool $isArray = false) : bool
    {
        return (new Logger())->debug($message, $isArray);
    }

    /**
     * Log error message.
     *
     * @access protected
     * @inheritdoc
     */
    protected function error(string $message) : bool
    {
        return (new Logger())->error($message);
    }

    /**
     * Log warning message.
     *
     * @access protected
     * @inheritdoc
     */
    protected function warning(string $message) : bool
    {
        return (new Logger())->warning($message);
    }
}

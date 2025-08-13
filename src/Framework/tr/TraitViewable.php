<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Helpers\Html\Template;

/**
 * Define template engine functions.
 */
trait TraitViewable
{
    /**
     * Get view environment.
     * 
     * @access protected
     * @inheritdoc
     */
    protected function getEnvironment($path, array $options = []) : object
    {
        return Template::getEnvironment($path, $options);
    }

    /**
     * Extend view callables.
     * 
     * @access protected
     * @inheritdoc
     */
    protected function extend(string $name, $callable = null, array $options = []) : object
    {
        return Template::extend($name, $callable, $options);
    }
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Html Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Html;

use Twig\Loader\FilesystemLoader as Loader;
use Twig\Environment as Environment;
use Twig\TwigFunction as Module;

/**
 * Wrapper class for Twig template engine.
 * @see https://twig.symfony.com
 */
final class Template
{
    /**
     * Get view environment.
     * Used single path for security.
     *
     * @access public
     * @param mixed $path
     * @param array $options
     * @return object
     */
    public static function getEnvironment($path, array $options = []) : Environment
    {
        return new Environment(new Loader($path), $options);
    }

    /**
     * Add view callable.
     *
     * @access public
     * @param string $name
     * @param callable $callable
     * @param array $options
     * @return object
     */
    public static function extend(string $name, $callable = null, array $options = []) : Module
    {
        return new Module($name, $callable, $options);
    }
}

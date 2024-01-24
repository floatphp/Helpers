<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Html Component
 * @version    : 1.1.1
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
     * 
     * @param mixed $path
     * @param array $settings
     * @return object
     */
    public static function getEnvironment($path, array $settings = []) : Environment
    {
        return new Environment(new Loader($path), $settings);
    }

    /**
     * Add view functions.
     * 
     * @param string $name
     * @param callable $function
     * @return object
     */
    public static function extend(string $name, $function) : Module
    {
        return new Module($name, $function);
    }
}

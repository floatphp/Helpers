<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Classes\Filesystem\TypeCheck;

/**
 * Wrapper class for YAML file.
 * @see https://symfony.com/components/Yaml
 */
final class Yaml
{
    /**
     * @access public
     */
    public const DEPENDENCY = 'Symfony\Component\Yaml\Yaml';

    /**
     * Check Yaml dependency.
     * 
     * @access public
     * @return bool
     */
    public static function isInstalled() : bool
    {
        return TypeCheck::isClass(self::DEPENDENCY);
    }

    /**
     * Parse Yaml file.
     *
     * @access public
     * @param string $path
     * @param string $section
     * @return mixed
     */
    public static function parse(string $path, ?string $section = null) : mixed
    {
        if ( self::isInstalled() ) {
            $data = self::DEPENDENCY::parseFile($path);
            if ( $section ) {
                $data = $data[$section] ?? null;
            }
            return $data;
        }
        return null;
    }
}

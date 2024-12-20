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

use FloatPHP\Classes\Filesystem\Arrayify;

trait TraitMappable
{
    /**
     * Map array function.
     * 
     * @access protected
     * @param mixed $value
     * @return mixed
     */
    protected function mapArray($function, array $data) : array
    {
        switch ($function) {
            case 'values':
                $function = 'array_values';
                break;
        }
        return Arrayify::map($function, $data);
    }

    /**
     * @access protected
     * @inheritdoc
     */
    protected function recursiveArray(&$array, $callback, $arg = null) : bool
    {
        return Arrayify::recursive($array, $callback, $arg);
    }
}

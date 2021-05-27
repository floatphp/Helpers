<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers;

use FloatPHP\Classes\Filesystem\TypeCheck;

final class Mcrypter
{
    /**
     * @access public
     * @param void
     * @return bool
     */
    public static function exists() : bool
    {
        return TypeCheck::isFunction('mcrypt_generic');
    }

    /**
     * @access public
     * @param string $data
     * @param string $key
     * @param string $algorithm
     * @return mixed
     */
    public static function encrypt($data, $key, $algorithm = 'blowfish')
    {
		// Open mcrypt
		$td = mcrypt_module_open($algorithm,'','ecb','');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
		// Process key
		$key = substr($key,0,mcrypt_enc_getKey_size($td));
		// Init mcrypt
		mcrypt_generic_init($td,$key,$iv);
		// encrypt data
		$crypt = mcrypt_generic($td,Stringify::serialize($srcArray));
		// Shutdown mcrypt
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
    }

    /**
     * @access public
     * @param string $algorithm
     * @return mixed
     */
    public static function open($algorithm = 'blowfish')
    {
		return mcrypt_module_open($algorithm,'','ecb','');
    }
}

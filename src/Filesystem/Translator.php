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
use FloatPHP\Classes\Http\Session;
use FloatPHP\Classes\Filesystem\Translation;

final class Translator extends Translation
{
    use TraitConfiguration;

    /**
     * @param string $locale
     */
    public function __construct($locale = '')
    {
        // Init configuration
        $this->initConfig();
        // Override
        $locale = !empty($locale) ? $locale : $this->getLanguage();
        parent::__construct($locale,$this->getTranslatePath());
    }

    /**
     * @access private
     * @param array $strings
     * @return array
     */
    public function translateArray(array $strings = []) : array
    {
        foreach ($strings as $key => $value) {
            $strings[$key] = $this->translate($value);
        }
        return $strings;
    }

    /**
     * @access private
     * @param void
     * @return string
     */
    private function getLanguage()
    {
        $lang = Session::get('--lang');
        if ( empty($lang) ) {
            $lang = Session::get('--default-lang');
        }
        return $lang;
    }
}

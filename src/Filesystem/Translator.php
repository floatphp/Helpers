<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\{
    Http\Session,
    Filesystem\Translation
};

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
        parent::__construct($locale, $this->getTranslatePath());
        
        // Reset configuration
        $this->resetConfig();
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

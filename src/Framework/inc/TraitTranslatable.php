<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Helpers\Filesystem\Translator;

trait TraitTranslatable
{
    /**
     * @access protected
     * @var object $translator, Translator object
     */
    protected $translator;

    /**
     * Get translator object.
     *
     * @access protected
     * @param string $driver
     * @return object
     */
    protected function getTranslatorObject(?string $locale = null) : Translator
    {
        $this->translator = new Translator($locale);
        return $this->translator;
    }
}

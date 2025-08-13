<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Classes\Filesystem\Translation;

/**
 * Built-in Translation factory class.
 */
final class Translator extends Translation
{
    use \FloatPHP\Kernel\TraitConfiguration;

    /**
     * @inheritdoc
     */
    public function __construct(?string $locale = null)
    {
        // Override
        parent::__construct($locale, $this->getTranslatePath());

        // Check
        if ( !$this->canTranslate && $this->isDebug() && $this->isEnv('dev') ) {
            $logger = new Logger('core');
            $logger->warning("Invalid language locale [{$locale}]");
        }
    }
}

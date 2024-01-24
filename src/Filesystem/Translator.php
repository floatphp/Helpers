<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
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
     * @uses initConfig()
     * @uses resetConfig()
     */
    public function __construct(?string $locale = null)
    {
        // Init configuration
        $this->initConfig();

        // Override
        parent::__construct($locale, $this->getTranslatePath());

        // Check
        if ( !$this->canTranslate && $this->isDebug() ) {
            $logger = new Logger('core');
            $logger->warning("Invalid language locale [{$locale}]");
        }
        
        // Reset configuration
        $this->resetConfig();
    }
}

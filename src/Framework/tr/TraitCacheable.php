<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

trait TraitCacheable
{
    /**
     * @access protected
     * @var bool $useCache, Cache status
     */
    protected $useCache = true;

    /**
     * Disable cache.
     *
     * @access protected
     * @return object
     */
    protected function noCache() : object
    {
        $this->useCache = false;
        return $this;
    }
}

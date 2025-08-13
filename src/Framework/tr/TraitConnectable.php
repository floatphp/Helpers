<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Connection\Db;
use FloatPHP\Classes\Filesystem\Logger;

trait TraitConnectable
{
    /**
     * @access protected
     * @var object $db, Database object
     */
    protected $db;

    /**
     * Get database object.
     *
     * @access protected
     * @param array $config
     * @param string $path
     * @param string $filename
     * @return object
     */
    protected function getDbObject(array $config = [], string $path = '/database', string $filename = 'db') : Db
    {
        $this->db = new Db($config, new Logger($path, $filename));
        return $this->db;
    }
}

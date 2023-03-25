<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Classes\Server\System;
use \PDOException;
use \PDO;

System::setTimeLimit(0);
System::setMemoryLimit('-1');

class Infile
{
    /**
     * @access private
     * @var array $config
     */
    private $config = [];

    /**
     * Set config.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge([
            'db'        => '',
            'host'      => 'localhost',
            'port'      => 3306,
            'user'      => 'root',
            'pswd'      => '',
            'charset'   => false,
            'debug'     => false,
            'truncate'  => false,
            'table'     => '',
            'file'      => '',
            'action'    => 'REPLACE', // IGNORE
            'delimiter' => ';',
            'enclosed'  => '\"',
            'line'      => '\n',
            'ignore'    => false
        ], $config);
    }

    /**
     * Execute.
     *
     * @access public
     * @param void
     * @return void
     */
    public function execute()
    {
        try {

            $start = microtime(true);

            // Set PDO params
            $dsn  = "mysql:dbname={$this->config['db']};";
            $dsn .= "host={$this->config['host']};";
            $dsn .= "port={$this->config['port']}";

            // Init PDO INFILE
            $pdo = new PDO($dsn,$this->config['user'],$this->config['pswd'],[
                PDO::MYSQL_ATTR_LOCAL_INFILE  => true
            ]);

            // Truncate table
            if ( $this->config['truncate'] ) {
                $pdo->exec("TRUNCATE `{$this->config['table']}`;");
            }

            // Execute query
            $pdo->exec($this->buildQuery());

            if ( $this->config['debug'] ) {
                $end = microtime(true); 
                $time = ($end - $start);
                echo "Executed in : {$time} seconds\n";
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Build query.
     *
     * @access private
     * @param void
     * @return string
     */
    private function buildQuery()
    {
        $query  = "LOAD DATA INFILE '{$this->config['file']}' ";
        if ( $this->config['action'] ) {
            $query .= "{$this->config['action']} ";
        }
        $query .= "INTO TABLE `{$this->config['table']}` ";
        if ( $this->config['charset'] ) {
            $query .= "CHARACTER SET {$this->config['charset']} ";
        }
        $query .= "FIELDS TERMINATED BY '{$this->config['delimiter']}' ";
        $query .= "OPTIONALLY ENCLOSED BY '{$this->config['enclosed']}' ";
        $query .= "LINES TERMINATED BY '{$this->config['line']}'";
        if ( $this->config['ignore'] ) {
            $query .= " IGNORE {$this->config['ignore']} LINES";
        }
        return $query;
    }
}

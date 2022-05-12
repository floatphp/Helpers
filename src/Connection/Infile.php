<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Connection;

use \PDOException;
use \PDO;

ini_set('memory_limit',-1);
set_time_limit(0);

class Infile
{
    /**
     * @access private
     * @param array $config
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
        ],$config);
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
     * build query.
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

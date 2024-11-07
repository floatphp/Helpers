<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Connection Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Classes\{
	Filesystem\Arrayify,
	Server\System
};
use \mysqli;

System::setTimeLimit(0);
System::setMemoryLimit('-1');

/**
 * MySQLi dump class.
 */
final class Dump
{
	use \FloatPHP\Kernel\TraitConfiguration;

	/**
	 * @access private
	 * @var array $access
	 */
	private $access = [];

	/**
	 * @param array $config
	 */
	public function __construct($config = [])
	{
		// Init configuration
		$this->initConfig();

		// Init access
		$this->access = Arrayify::merge(
			$this->getDbAccess(),
			$config
		);
		
		// Reset configuration
		$this->resetConfig();
	}

	/**
	 * Import dump file.
	 *
	 * @access public
	 * @param string $file
	 * @return bool
	 */
	public function import(string $file) : bool
	{
		// Init connection
		$connection = @new mysqli(
			$this->access['host'],
			$this->access['user'],
			$this->access['pswd'],
			$this->access['db']
		);

		// Check connection
		if ( $connection->connect_errno ) {
			return false;
		}

		$status = 0;

		// Temporary variable, store current query
		$temp = '';

		// Read file
		$lines = file($file);

		// Loop through each line
		foreach ($lines as $line) {

			// Skip comment
			if ( substr($line, 0, 2) == '--' || $line == '' ) {
				continue;
			}

			// Add line to current segment
			$temp .= $line;

			// End of query
			if ( substr(trim($line), -1, 1) == ';' ) {

			    // Perform query
			    $i = (int)$connection->query($temp);
			    $status += $i;
			    if ( !$i ) {
			    	break;
			    }

			    // Reset temp
			    $temp = '';
			}
		}

		$connection->close();
		return (bool)$status;
	}

	/**
	 * Export dump file.
	 *
	 * @access public
	 * @param string $file
	 * @return bool
	 */
	public function export(string $file = 'dump.sql') : bool
	{
		$command  = 'mysqldump --opt';
		$command .= " -u {$this->access['user']}";

		if ( $this->access['host'] ) {
			$command .= " -h {$this->access['host']}";
		}
		if ( $this->access['pswd'] ) {
			$command .= " -p {$this->access['pswd']}";
		}
		
		$command .= " {$this->access['db']} > {$file}";

		exec($command, $output, $status);
		return ($status === 0);
	}
}

<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Connection Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Connection;

use FloatPHP\Kernel\TraitConfiguration;
use \mysqli;

final class Dump
{
	use TraitConfiguration;

	/**
	 * @access private
	 * @var object $this->connectionnection
	 */
	private $this->connectionnection = false;

	/**
	 * @param void
	 */
	public function __construct()
	{
		// Init configuration
		$this->initConfig();
		// Init connection
		$this->init();
	}

	/**
	 * Import dump file
	 *
	 * @access public
	 * @param string $file
	 * @return bool
	 */
	public function import($file = '') : bool
	{
		// Check connection
		if ( $this->connection->connect_errno ) {
			return false;
		}
		// Temporary variable, store current query
		$temp = '';
		// Read in entire file
		$lines = file($filename);
		// Loop through each line
		foreach ($lines as $line) {
			// Skip it if it's a comment
			if ( substr($line, 0, 2) == '--' || $line == '' ) {
				continue;
			}
			// Add line to current segment
			$temp .= $line;
			// End of query
			if ( substr(trim($line), -1, 1) == ';' ) {
			    // Perform query
			    $this->connection->query($temp) or die();
			    // Reset temp
			    $temp = '';
			}
		}
		$this->connection->close($this->connection);
		return true;
	}

	/**
	 * Export dump file
	 *
	 * @access public
	 * @param string $file
	 * @return bool
	 */
	public function export($file = '') : bool
	{
		$mysqlDatabaseName ='nom de la base de données';
		$mysqlUserName ='Nom dutilisateur';
		$mysqlPassword ='Mot de passe';
		$mysqlHostName ='dbxxx.hosting-data.io';
		$mysqlExportPath ='nom-du-fichier-dexport.sql';

		$command='mysqldump --opt -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' > ' .$mysqlExportPath;
		exec($command,$output,$worked);
		switch($worked){
		case 0:
		break;
		case 1:
		break;
		case 2:
		break;
		}
	}

	/**
	 * @access private
	 * @param void
	 * @return void
	 */
	private function init()
	{
		// Connect to MySQL server
		$access = $this->getDatabaseAccess();
		$this->connection = @new mysqli($access['host'],$access['user'],$access['pswd'],$access['db']);
	}
}

<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Kernel Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers;

use FloatPHP\Kernel\BaseOptions;
use FloatPHP\Kernel\Orm;
use FloatPHP\Classes\Filesystem\File;
use FloatPHP\Classes\Filesystem\Json;

final class Configurator extends BaseOptions
{
	/**
	 * Setup application
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function setup()
	{
		// Setup database
		if ( $this->getDatabaseFile() && $this->getMigratePath() ) {
			$this->migrate();
		}
		// Setup rewrite
		if ( !File::exists("{$this->getRoot()}/.htaccess") ) {
			$this->rewrite();
		}
		if ( !File::exists("{$this->getAppDir()}/.htaccess") ) {
			File::w("{$this->getAppDir()}/.htaccess",'deny from all');
		}
		// Setup config
		if ( !File::exists($this->getConfigFile()) ) {
			$this->config();
		}
	}

	/**
	 * Parse application config
	 *
	 * @access public
	 * @param array $config
	 * @return array
	 */
	public static function parse($config = [])
	{
		$parse['--disable-setup'] = isset($config['--disable-setup'])
		? isset($config['--disable-setup']) : false;

		$parse['--disable-powered-by'] = isset($config['--disable-powered-by'])
		? isset($config['--disable-powered-by']) : false;

		$parse['--disable-session'] = isset($config['--disable-session'])
		? isset($config['--disable-session']) : false;

		$parse['--default-lang'] = isset($config['--default-lang'])
		? isset($config['--default-lang']) : 'en';

		$parse['--default-timezone'] = isset($config['--default-timezone'])
		? isset($config['--default-timezone']) : 'Europe/Paris';

		return $parse;
	}

	/**
	 * Migrate application database
	 *
	 * @access private
	 * @param void
	 * @return void
	 */
	private function migrate()
	{
		$orm = new Orm();

		// Create database
		$orm->createDatabase();

		// Create tables
		$tables = array_diff(scandir($this->getMigratePath()),['.','..']);
		if ( !$tables ) {
			return;
		}
		foreach ($tables as $table) {
			$sql = File::r("{$this->getMigratePath()}/{$table}");
			if ( !empty($sql) ) {
				$orm->init();
				$orm->query($sql);
			}
		}
	}

	/**
	 * Create application rewrite
	 *
	 * @access private
	 * @param void
	 * @return void
	 */
	private function rewrite()
	{
		$htaccess = File::r(dirname(__FILE__).'/bin/.htaccess');
		File::w("{$this->getRoot()}/.htaccess",$htaccess);
	}

	/**
	 * Create application config
	 *
	 * @access private
	 * @param void
	 * @return void
	 */
	private function config()
	{
		File::w($this->getConfigFile(),Json::format($this->getConfig(),64|128|256));
	}
}

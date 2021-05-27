<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers;

use FloatPHP\Kernel\Base;
use FloatPHP\Kernel\Orm;
use FloatPHP\Classes\Filesystem\File;
use FloatPHP\Classes\Filesystem\Json;

final class Configurator extends Base
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
		$transient = new Transient();
		if ( !$transient->getTemp('--installed') ) {
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
			$transient->setTemp('--installed',true,0);
		}
	}

	/**
	 * Migrate application database
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function migrate($path = null)
	{
		if ( !$path ) {
			$path = $this->getMigratePath();
		}

		$orm = new Orm();

		// Create database
		$orm->createDatabase();

		// Create tables
		$tables = glob("{$path}/*.{sql}", GLOB_BRACE);
		if ( !$tables ) {
			return;
		}
		foreach ($tables as $table) {
			$sql = File::r("{$table}");
			if ( !empty($sql) ) {
				$orm->init();
				$orm->query($sql);
			}
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
		$parse = [];
		$parse['--disable-setup'] = isset($config['--disable-setup'])
		? $config['--disable-setup'] : false;

		$parse['--disable-powered-by'] = isset($config['--disable-powered-by'])
		? $config['--disable-powered-by'] : false;

		$parse['--disable-session'] = isset($config['--disable-session'])
		? $config['--disable-session'] : false;

		$parse['--default-lang'] = isset($config['--default-lang'])
		? $config['--default-lang'] : 'en';

		$parse['--default-timezone'] = isset($config['--default-timezone'])
		? $config['--default-timezone'] : 'Europe/Paris';

		return $parse;
	}

	/**
	 * Reset application config
	 *
	 * @access public
	 * @param bool $all
	 * @return void
	 */
	public static function reset($all = false)
	{
		$transient = new Transient();
		if ( $all ) {
			$transient->resetBaseTemp();
		} else {
			$transient->setBaseTemp('--installed',false,0);
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
		File::w("{$path}/.htaccess",$htaccess);
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

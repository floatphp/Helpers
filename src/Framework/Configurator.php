<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.0.0
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Helpers\Filesystem\Transient;
use FloatPHP\Helpers\Connection\Role;
use FloatPHP\Kernel\Base;
use FloatPHP\Kernel\Orm;
use FloatPHP\Classes\Filesystem\File;
use FloatPHP\Classes\Filesystem\Json;
use FloatPHP\Classes\Filesystem\Stringify;
use FloatPHP\Classes\Http\Server;

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
			// Setup config
			if ( !File::exists($this->getConfigFile()) ) {
				$this->config();
			}
			// Setup database
			if ( $this->getDatabaseFile() && $this->getMigratePath() ) {
				$this->setBuiltinTables();
				$this->migrate();
				$this->importBuiltinTables();
			}
			if ( !$transient->getBaseTemp('--installed') ) {
				// Setup rewrite
				if ( !File::exists("{$this->getRoot()}/.htaccess") ) {
					$this->rewrite();
				}
				if ( !File::exists("{$this->getAppDir()}/.htaccess") ) {
					File::w("{$this->getAppDir()}/.htaccess",'deny from all');
				}
				$transient->setBaseTemp('--installed',true,0);
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

		// Create database
		$orm = new Orm();
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
	 * Set builtin database tables
	 *
	 * @access private
	 * @param void
	 * @return void
	 */
	private function setBuiltinTables()
	{
		$path = $this->getMigratePath();
		if ( !File::exists("{$path}/config.sql") ) {
			File::cp(dirname(__FILE__).'/bin/config.sql.default',"{$path}/config.sql");
		}
		if ( !File::exists("{$path}/user.sql") ) {
			File::cp(dirname(__FILE__).'/bin/user.sql.default',"{$path}/user.sql");
		}
		if ( !File::exists("{$path}/role.sql") ) {
			File::cp(dirname(__FILE__).'/bin/role.sql.default',"{$path}/role.sql");
		}
	}

	/**
	 * Import builtin data
	 *
	 * @access private
	 * @param void
	 * @return void
	 */
	private function importBuiltinTables()
	{
		$roles = Json::decode(File::r(dirname(__FILE__).'/bin/role.default.json'),true);
		$item = new Role();
		$item->deleteAll();
		$item->resetId();
		foreach ($roles as $role) {
			$item->name = $role['name'];
			$item->slug = $role['slug'];
			$item->capability = Json::encode($role['capability']);
			$item->create();
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
		
		$parse['--enable-maintenance'] = isset($config['--enable-maintenance'])
		? $config['--enable-maintenance'] : false;

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
		$base = $this->getBaseRoute(false);
		$base = Stringify::replace('//','/',"/{$base}/");
		$domain = Server::get('server-name');
		$domain = Stringify::replace('www.','',$domain);
		$file = basename(Server::get('script-filename'));
		$file = Stringify::replace('.php','',$file);
		$htaccess = Stringify::replaceArray([
			'/__BASE__/' => $base,
			'__FILE__'   => $file,
			'__DOMAIN__' => $domain
		],$htaccess);
		if ( Server::isHttps() ) {
			$htaccess = Stringify::replaceArray([
				'# RewriteCond %{HTTPS} off'  => 'RewriteCond %{HTTPS} off',
				'# RewriteRule (.*) https://' => 'RewriteRule (.*) https://'
			],$htaccess);
		}
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

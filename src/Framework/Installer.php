<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Kernel\Orm;
use FloatPHP\Helpers\{
	Connection\Transient,
	Connection\Role
};

/**
 * Framwork application installer.
 */
final class Installer
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\inc\TraitRequestable;

	/**
	 * Setup application.
	 *
	 * @access public
	 * @return void
	 */
	public function setup()
	{
		// Setup config
		if ( !$this->hasFile($this->getConfigFile()) ) {
			$this->setConfig();
		}

		// Setup database
		if ( $this->getDatabaseFile() && $this->getMigratePath() ) {
			$this->setTables();
			$this->migrate();
			$this->importRoles();
		}

		// Setup rewrite
		if ( !$this->hasFile("{$this->getRoot()}/.htaccess") ) {
			$this->rewrite();
		}

		// Setup security
		if ( !$this->hasFile("{$this->getAppDir()}/.htaccess") ) {
			$this->writeFile("{$this->getAppDir()}/.htaccess", 'deny from all');
		}

		self::install();
	}

	/**
	 * Migrate application database.
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
		(new Orm())->noConnect()->setup();

		// Import tables
		$tables = glob("{$path}/*.{sql}", GLOB_BRACE);
		if ( !$tables ) {
			return;
		}

		// Create tables
		$orm = new Orm();
		foreach ($tables as $table) {
			$sql = $this->readFile("{$table}");
			if ( !empty($sql) ) {
				$orm->query($sql);
			}
		}
	}

	/**
	 * Parse application config.
	 *
	 * @access public
	 * @param array $config
	 * @return array
	 */
	public function parse(array $config = []) : array
	{
		return $this->mergeArray([
            '--enable-maintenance' => false,
            '--disable-setup'      => false,
            '--disable-powered-by' => false,
            '--disable-session'    => false,
            '--default-lang'       => 'en',
            '--default-timezone'   => 'Europe/Paris',
        ], $config);
	}

	/**
	 * Check installed status.
	 *
	 * @access public
	 * @return bool
	 */
	public static function isInstalled() : bool
	{
		return (bool)(new Transient())->get('--installed', false);
	}

	/**
	 * Set installed status.
	 *
	 * @access public
	 * @return bool
	 */
	public static function install() : bool
	{
		return (new Transient())->set('--installed', true, 0);
	}

	/**
	 * Reset installed status.
	 *
	 * @access public
	 * @return bool
	 */
	public static function reset() : bool
	{
		return (new Transient())->delete('--installed');
	}

	/**
	 * Set built-in database tables.
	 *
	 * @access private
	 * @return void
	 */
	private function setTables()
	{
		$path = $this->getMigratePath();
		$tables = [
			'config.sql',
			'user.sql',
			'role.sql'
		];
		foreach ($tables as $sql) {
			if ( !$this->hasFile("{$path}/{$sql}") ) {
				$this->copyFile( dirname(__FILE__) . "/bin/{$sql}.default", "{$path}/{$sql}");
			}
		}
	}

	/**
	 * Import built-in roles.
	 *
	 * @access private
	 * @return void
	 */
	private function importRoles()
	{
		$path = dirname(__FILE__) . '/bin/role.default.json';
		$roles = $this->decodeJson($this->readFile($path), true);
		$r = new Role();
		$r->clear();
		$r->resetId();
		foreach ($roles as $role) {
			$r->name = $role['name'];
			$r->slug = $role['slug'];
			$r->capability = $this->encodeJson($role['capability']);
			$r->create();
		}
	}
	
	/**
	 * Setup application rewrite.
	 *
	 * @access private
	 * @return void
	 */
	private function rewrite()
	{
		$htaccess = $this->readFile(dirname(__FILE__) . '/bin/.htaccess');

		// Set base
		$base = $this->getBaseRoute(false);
		$base = $this->replaceString('//', '/', "/{$base}/");

		// Set domain
		$domain = $this->getServer('server-name');
		$domain = $this->removeString('www.', $domain);

		// Set file
		$file = $this->basename($this->getServer('script-filename'));
		$file = $this->removeString('.php', $file);

		$htaccess = $this->replaceStringArray([
			'/__BASE__/' => $base,
			'__FILE__'   => $file,
			'__DOMAIN__' => $domain
		], $htaccess);

		if ( $this->isSsl() ) {
			$htaccess = $this->replaceStringArray([
				'# RewriteCond %{HTTPS} off'  => 'RewriteCond %{HTTPS} off',
				'# RewriteRule (.*) https://' => 'RewriteRule (.*) https://'
			], $htaccess);
		}
		
		$this->writeFile("{$this->getRoot()}/.htaccess", $htaccess);
	}

	/**
	 * Set application config file.
	 *
	 * @access private
	 * @return void
	 */
	private function setConfig()
	{
		$config = $this->formatJson($this->getConfig(), 64|128|256);
		$this->writeFile($this->getConfigFile(), $config);
	}
}

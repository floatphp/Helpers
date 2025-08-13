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

namespace FloatPHP\Helpers\Framework;

use FloatPHP\Kernel\Orm;
use FloatPHP\Helpers\Connection\{Transient, Role};

/**
 * Framwork application installer.
 */
final class Installer
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\tr\TraitRequestable;

	/**
	 * Setup application.
	 *
	 * @access public
	 * @param bool $db
	 * @param bool $yaml
	 * @return void
	 */
	public function setup(bool $db = true) : void
	{
		// Setup config
		if ( !$this->isFile($this->getConfigFile()) ) {
			$this->setConfig();
		}

		// Setup database
		if ( $db ) {
			if ( $this->getDbFile() && $this->getMigratePath() ) {
				$this->setTables();
				$this->migrate();
				$this->importRoles();
			}
		}

		// Setup rewrite
		if ( !$this->isFile("{$this->getRoot()}/.htaccess") ) {
			$this->rewrite();
		}

		// Setup security
		if ( !$this->isFile("{$this->getAppDir()}/.htaccess") ) {
			$this->writeFile("{$this->getAppDir()}/.htaccess", 'deny from all');
		}

		self::install();
	}

	/**
	 * Migrate application database.
	 *
	 * @access public
	 * @param ?string $path
	 * @return void
	 */
	public function migrate(?string $path = null) : void
	{
		if ( !$path ) {
			$path = $this->getMigratePath();
		}

		// Create database
		Orm::noConnect();
		(new Orm())->setup();

		// Import tables
		$tables = glob(pattern: "{$path}/*.{sql}", flags: GLOB_BRACE);
		if ( !$tables ) return;

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
	 * Default app config.
	 *
	 * @access public
	 * @param array $config
	 * @return array
	 */
	public function default(array $config = []) : array
	{
		return $this->mergeArray([
			'--enable-maintenance' => false,
			'--enable-setup'       => false,
			'--enable-database'    => false,
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
		if ( !(new Orm())->hasTable('config') ) {
			return false;
		}
		return (bool)(new Transient())
			->get(key: '--installed', default: false);
	}

	/**
	 * Set installed status.
	 *
	 * @access public
	 * @return bool
	 */
	public static function install() : bool
	{
		return (new Transient())
			->set(key: '--installed', value: true, ttl: 0);
	}

	/**
	 * Reset installed status.
	 *
	 * @access public
	 * @return bool
	 */
	public static function reset() : bool
	{
		return (new Transient())
			->delete(key: '--installed');
	}

	/**
	 * Set built-in database tables.
	 *
	 * @access private
	 * @return void
	 */
	private function setTables() : void
	{
		$path = $this->getMigratePath();
		$tables = [
			'config.sql',
			'user.sql',
			'role.sql'
		];
		foreach ($tables as $table) {
			if ( !$this->isFile("{$path}/{$table}") ) {
				$this->copyFile(dirname(__FILE__) . "/bin/db/{$table}", "{$path}/{$table}");
			}
		}
	}

	/**
	 * Import built-in roles.
	 *
	 * @access private
	 * @return void
	 */
	private function importRoles() : void
	{
		$path = dirname(__FILE__) . '/bin/data/role.json';
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
	private function rewrite() : void
	{
		$htaccess = $this->readFile(dirname(__FILE__) . '/bin/server/.htaccess');

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
	private function setConfig() : void
	{
		$config = $this->formatJson($this->getConfig(), 64 | 128 | 256);
		$this->writeFile($this->getConfigFile(), $config);
	}
}

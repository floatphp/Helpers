<?php
/**
 * @author    : Jakiboy
 * @package   : VanillePlugin
 * @version   : 1.0.x
 * @copyright : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link      : https://jakiboy.github.io/VanillePlugin/
 * @license   : MIT
 *
 * This file if a part of VanillePlugin Framework.
 */

declare(strict_types=1);

namespace VanillePlugin\tr;

use VanillePlugin\lib\Migrate;

/**
 * Define database migration functions.
 */
trait TraitMigratable
{
	/**
	 * Check whether has migrate lock.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isMigrated() : bool
	{
		return false;
	}

	/**
	 * Install database tables.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function installTables() : bool
	{
		return false;
	}

	/**
	 * Rebuild database tables.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function rebuildTables() : bool
	{
		return false;
	}

	/**
	 * Remove database tables.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected static function dropTables() : bool
	{
		return false;
	}

	/**
	 * Upgrade database table(s).
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function upgradeTables() : bool
	{
		return false;
	}

	/**
	 * Migrate options.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function migrateOptions(array $options) : bool
	{
		return false;
	}

	/**
	 * Export database table.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function exportTable(string $table, ?string $column = null) : mixed
	{
		return false;
	}

	/**
	 * Import database table.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function importTable(string $table, array $data) : bool
	{
		return false;
	}

	/**
	 * Clear database table.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function clearTable(string $table) : bool
	{
		return false;
	}
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Html\{Hook, Shortcode};

trait TraitHookable
{
	/**
	 * Add filter hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function addFilter(string $name, $callback, int $priority = Hook::PRIORITY, int $args = Hook::COUNT) : mixed
	{
		return Hook::getInstance()->addFilter($name, $callback, $priority, $args);
	}

	/**
	 * Remove filter hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeFilter(string $name, $callback, int $priority = Hook::PRIORITY) : bool
	{
		return Hook::getInstance()->removeFilter($name, $callback, $priority);
	}

	/**
	 * Remove all filters.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeFilters(string $name, $priority = false) : bool
	{
		return Hook::getInstance()->removeFilters($name, $priority);
	}

	/**
	 * Check filter hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasFilter(string $name, $callback = false) : mixed
	{
		return Hook::getInstance()->hasFilter($name, $callback);
	}

	/**
	 * Apply filter hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function applyFilter(string $name, $value, ...$args) : mixed
	{
		return Hook::getInstance()->applyFilter($name, $value, ...$args);
	}

	/**
	 * Add action hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function addAction(string $name, $callback, int $priority = Hook::PRIORITY, int $args = Hook::COUNT) : bool
	{
		return Hook::getInstance()->addAction($name, $callback, $priority, $args);
	}

	/**
	 * Check action hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasAction(string $name, $callback = false) : mixed
	{
		return Hook::getInstance()->hasAction($name, $callback);
	}

	/**
	 * Remove action hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeAction(string $name, $callback, int $priority = Hook::PRIORITY) : bool
	{
		return Hook::getInstance()->removeAction($name, $callback, $priority);
	}

	/**
	 * Remove all actions hooks.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeActions(string $name, $priority = false) : bool
	{
		return Hook::getInstance()->removeActions($name, $priority);
	}

	/**
	 * Do action hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function doAction(string $name, ...$args) : void
	{
		Hook::getInstance()->doAction($name, ...$args);
	}

	/**
	 * Check fired action hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function didAction(string $name) : int
	{
		return Hook::getInstance()->didAction($name);
	}

	/**
	 * Add shortcode.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function addShortcode(string $name, $callback) : bool
	{
		return Shortcode::getInstance()->add($name, $callback);
	}

	/**
	 * Remove registered shortcode.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeShortcode(string $name) : bool
	{
		return Shortcode::getInstance()->remove($name);
	}

	/**
	 * Remove all registered shortcodes.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeShortcodes() : bool
	{
		return Shortcode::getInstance()->removeAll();
	}

	/**
	 * Check whether shortcode is registered.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasShortcode(string $name) : bool
	{
		return Shortcode::getInstance()->has($name);
	}

	/**
	 * Check whether content contains shortcode.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function containShortcode(string $content, string $name) : bool
	{
		return Shortcode::getInstance()->contain($content, $name);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function shortcodeAtts(array $default, array $atts, string $name = '') : mixed
	{
		return Shortcode::getInstance()->getAtts($default, $atts, $name);
	}

	/**
	 * Remove all shortcodes from content.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function stripShortcodes($content) : mixed
	{
		return Shortcode::getInstance()->strip($content);
	}

	/**
	 * Do shortcode hook.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function doShortcode(string $content, bool $escape = false) : mixed
	{
		return Shortcode::getInstance()->do($content, $escape);
	}

	/**
	 * Render shortcode.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function renderShortcode(string $content, bool $escape = false) : void
	{
		echo $this->doShortcode($content, $escape);
	}

	/**
	 * Spin shortcode string.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function spinText(string $content) : string
	{
		return Shortcode::spin($content);
	}
}

<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Classes\{
    Html\Hook,
    Html\Shortcode
};

trait TraitHookable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function addAction($hook, $method, $priority = 10, $args = 1)
	{
		return Hook::getInstance()->addAction($hook, $method, $priority, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeAction($hook, $method, $priority = 10)
	{
		return Hook::getInstance()->removeAction($hook, $method, $priority);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function doAction($tag, $args = null)
	{
		return Hook::getInstance()->doAction($tag, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasAction($tag, $args = null)
	{
		return Hook::getInstance()->hasAction($tag, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function addFilter($hook, $method, $priority = 10, $args = 1)
	{
		return Hook::getInstance()->addFilter($hook, $method, $priority, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeFilter($hook, $method, $priority = 10) : bool
	{
		return Hook::getInstance()->removeFilter($hook, $method, $priority);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function applyFilter($hook, $value, $args = null)
	{
		return Hook::getInstance()->applyFilter($hook, $value, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasFilter($hook, $method = false) : bool
	{
		return Hook::getInstance()->hasFilter($hook, $method);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function addShortcode($tag, $callback)
	{
		return Shortcode::getInstance()->addShortcode($tag,$callback);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function renderShortcode($content, $ignoreHTML = false)
	{
		echo $this->doShortcode($content, $ignoreHTML);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function doShortcode($content, $ignoreHTML = false)
	{
		return Shortcode::getInstance()->doShortcode($content, $ignoreHTML);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeShortcode($tag)
	{
		return Shortcode::getInstance()->removeShortcode($tag);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasShortcode($content, $tag) : bool
	{
		return Shortcode::getInstance()->hasShortcode($content, $tag);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function shortcodeAtts($pairs, $atts, $shortcode = '')
	{
		return Shortcode::getInstance()->shortcodeAtts($pairs, $atts, $shortcode);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function stripShortcodes($content)
	{
		return Shortcode::getInstance()->stripShortcodes($content);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function spinText($content)
	{
		return Shortcode::spin($content);
	}
}

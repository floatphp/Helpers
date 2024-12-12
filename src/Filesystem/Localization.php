<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Classes\Filesystem\Stringify;

final class Localization
{
	/**
	 * Get locale.
	 *
	 * @access public
	 * @param mixed $user
	 * @return string
	 * @todo
	 */
	public static function getLocale($user = null) : string
	{
		return '';
	}

	/**
	 * Parse lang from locale.
	 *
	 * @access public
	 * @param string $locale
	 * @return string
	 */
	public static function parseLang(string $locale) : string
	{
		$locale = self::normalizeLocale($locale);
		if ( Stringify::contains($locale, '-') ) {
			if ( ($locale = explode('-', $locale)) ) {
				$locale = $locale[0] ?? '';
			}
		}
		return $locale;
	}

	/**
	 * Parse region from locale.
	 *
	 * @access public
	 * @param string $locale
	 * @return string
	 */
	public static function parseRegion(string $locale) : string
	{
		$locale = self::normalizeLocale($locale);
		if ( Stringify::contains($locale, '-') ) {
			if ( ($locale = explode('-', $locale)) ) {
				$locale = $locale[1] ?? '';
			}
		}
		return $locale;
	}

	/**
	 * Normalize locale.
	 *
	 * @access public
	 * @param string $locale
	 * @return string
	 */
	public static function normalizeLocale(string $locale) : string
	{
		$locale = Stringify::slugify($locale);
		return Stringify::replace('_', '-', $locale);
	}
}

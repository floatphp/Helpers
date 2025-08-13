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

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\{
	Filesystem\TypeCheck,
	Filesystem\Stringify,
	Filesystem\Arrayify,
	Filesystem\Validator,
	Filesystem\Converter,
	Filesystem\Json,
	Http\Xml,
	Server\System
};

/**
 * Define formatting functions.
 */
trait TraitFormattable
{
	use TraitSerializable,
		TraitMappable,
		TraitSecurable;

	/**
	 * Format path.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function formatPath(string $path, bool $untrailing = false) : string
	{
		return Stringify::formatPath($path, $untrailing);
	}

	/**
	 * Format key.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function formatKey(string $key) : string
	{
		return Stringify::formatKey($key);
	}

	/**
	 * Format whitespaces.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function formatSpace(string $string) : string
	{
		return Stringify::formatSpace($string);
	}

	/**
	 * Strip spaces in string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function stripSpace(string $string) : string
	{
		return Stringify::stripSpace($string);
	}

	/**
	 * Lowercase string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function lowercase(string $string) : string
	{
		return Stringify::lowercase($string);
	}

	/**
	 * Uppercase string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function uppercase(string $string) : string
	{
		return Stringify::uppercase($string);
	}

	/**
	 * Capitalize string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function capitalize(string $string) : string
	{
		return Stringify::capitalize($string);
	}

	/**
	 * Camelcase string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function camelcase(string $string) : string
	{
		return Stringify::camelcase($string);
	}

	/**
	 * Slugify string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function slugify(string $string) : string
	{
		return Stringify::slugify($string);
	}

	/**
	 * Format dash (hyphen) into underscore.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function undash(mixed $string, bool $isGlobal = false) : mixed
	{
		return Stringify::undash($string, $isGlobal);
	}

	/**
	 * Remove slashes from value.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function unSlash($value)
	{
		return Stringify::unSlash($value);
	}

	/**
	 * Strip slashes in quotes or single quotes.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function stripSlash($value)
	{
		return Stringify::deepStripSlash($value);
	}

	/**
	 * Add slashes to value.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function slash($value)
	{
		return Stringify::slash($value);
	}

	/**
	 * Remove trailing slashes and backslashes if exist.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function untrailingSlash(string $string) : string
	{
		return Stringify::untrailingSlash($string);
	}

	/**
	 * Append trailing slashes.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function trailingSlash(string $string) : string
	{
		return Stringify::trailingSlash($string);
	}

	/**
	 * Search string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function hasString($string, $search) : bool
	{
		return Stringify::contains($string, $search);
	}

	/**
	 * Remove string in other string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function removeString(string $search, string $subject, bool $regex = false) : string
	{
		if ( $regex ) {
			return Stringify::removeRegex($search, $subject);
		}
		return Stringify::remove($search, $subject);
	}

	/**
	 * Search replace string(s).
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function replaceString($search, $replace, $subject, bool $regex = false)
	{
		if ( $regex ) {
			return Stringify::replaceRegex($search, $replace, $subject);
		}
		return Stringify::replace($search, $replace, $subject);
	}

	/**
	 * Search replace substring(s).
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function replaceSubString($search, $replace, $offset = 0, $length = null)
	{
		return Stringify::subreplace($search, $replace, $offset, $length);
	}

	/**
	 * Search replace string(s) using array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function replaceStringArray(array $replace, string $subject) : string
	{
		return Stringify::replaceArray($replace, $subject);
	}

	/**
	 * Match string using regex.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function matchString($regex, string $string, &$matches, int $flags = 0, int $offset = 0) : bool
	{
		return Stringify::match($regex, $string, $matches, $flags, $offset);
	}

	/**
	 * Match all strings using regex (g).
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function matchEveryString(string $regex, string $string, &$matches, int $flags = 0, int $offset = 0) : bool
	{
		return Stringify::matchAll($regex, $string, $matches, $flags, $offset);
	}

	/**
	 * Parse string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function parseString(string $string, &$result = []) : mixed
	{
		return Stringify::parse($string, $result);
	}

	/**
	 * Split string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function splitString(string $string, array $args = []) : mixed
	{
		return Stringify::split($string, $args);
	}

	/**
	 * Limit string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function limitString(string $string, int $limit = 150) : string
	{
		return Stringify::limit($string, $limit);
	}

	/**
	 * Get basename with path format.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function basename(string $path, string $suffix = '') : string
	{
		return Stringify::basename($path, $suffix);
	}

	/**
	 * Get break to line.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function breakString() : string
	{
		return Stringify::break();
	}

	/**
	 * Decode JSON.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function decodeJson(string $value, bool $isArray = false) : mixed
	{
		return Json::decode($value, $isArray);
	}

	/**
	 * Encode JSON without flags.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function encodeJson($value) : mixed
	{
		return Json::encode($value);
	}

	/**
	 * Encode JSON using flags.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function formatJson($value, int $flags = 64 | 256, int $depth = 512) : mixed
	{
		return Json::format($value, $flags, $depth);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function formatXml(string $xml) : string
	{
		return Xml::format($xml);
	}

	/**
	 * Parse XML string.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function parseXml(string $xml, int $args = 16384 | 20908) : mixed
	{
		return Xml::parse($xml, $args);
	}

	/**
	 * Parse XML file.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function parseXmlFile(string $path, int $args = 16384 | 20908) : mixed
	{
		return Xml::parseFile($path, $args);
	}

	/**
	 * Check array item.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function inArray($value, array $array) : bool
	{
		return Arrayify::inArray($value, $array);
	}

	/**
	 * Merge arrays.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function mergeArray(array ...$arrays) : array
	{
		return Arrayify::merge(...$arrays);
	}

	/**
	 * Filter array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function filterArray(array $array, $callback = null, $mode = 0) : array
	{
		return Arrayify::filter($array, $callback, $mode);
	}

	/**
	 * Check array key.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function hasArrayKey($key, array $array) : bool
	{
		return Arrayify::hasKey($key, $array);
	}

	/**
	 * Get array keys.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function arrayKeys(array $array, $value = null, bool $search = false) : array
	{
		return Arrayify::keys($array, $value, $search);
	}

	/**
	 * Get single array key.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function arrayKey(array $array) : mixed
	{
		return Arrayify::key($array);
	}

	/**
	 * Get array values.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function arrayValues(array $array) : array
	{
		return Arrayify::values($array);
	}

	/**
	 * Shift array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function shiftArray(array &$array) : array
	{
		return Arrayify::shift($array);
	}

	/**
	 * Get array diff.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function diffArray(array $array, array $arrays) : array
	{
		return Arrayify::diff($array, $arrays);
	}

	/**
	 * Sort array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function sortArray(array $array, $orderby = [], $order = 'ASC', bool $preserve = false)
	{
		return Arrayify::sort($array, $orderby, $order, $preserve);
	}

	/**
	 * Slice array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function sliceArray(array $array, int $offset, ?int $length = null, bool $preserve = false) : array
	{
		return Arrayify::slice($array, $offset, $length, $preserve);
	}

	/**
	 * Unique array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function uniqueArray(array $array, $flags = 2) : array
	{
		return Arrayify::unique($array, $flags);
	}

	/**
	 * Unique arrays.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function uniqueMultiArray(array $array) : array
	{
		return Arrayify::uniqueMultiple($array);
	}

	/**
	 * Format array key case.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function formatKeyCase(array $array, int $case = 0) : array
	{
		return Arrayify::formatKeyCase($array, $case);
	}

	/**
	 * Push array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function pushArray(array &$array, ...$values) : int
	{
		return Arrayify::push($array, ...$values);
	}

	/**
	 * Format array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function formatArray(array $array) : array
	{
		return Arrayify::format($array);
	}

	/**
	 * Check value type.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isType(string $type, $value) : bool
	{
		return match ($this->lowercase($type)) {
			'array', '[]'      => TypeCheck::isArray($value),
			'object', 'obj'    => TypeCheck::isObject($value),
			'string', 'str'    => TypeCheck::isString($value),
			'integer', 'int'   => TypeCheck::isInt($value),
			'zero', '0'        => TypeCheck::isZero($value),
			'numeric', 'num'   => TypeCheck::isNumeric($value),
			'float', 'double'  => TypeCheck::isFloat($value),
			'bool', 'boolean'  => TypeCheck::isBool($value),
			'false'            => TypeCheck::isFalse($value),
			'true'             => TypeCheck::isTrue($value),
			'null'             => TypeCheck::isNull($value),
			'empty'            => TypeCheck::isEmpty($value),
			'resource', 'res'  => TypeCheck::isResource($value),
			'class'            => TypeCheck::isClass($value),
			'interface'        => TypeCheck::isInterface($value),
			'function', 'fun'  => TypeCheck::isFunction($value),
			'callable', 'call' => TypeCheck::isCallable($value),
			'email'            => Validator::isEmail($value),
			'url'              => Validator::isUrl($value),
			'date'             => Validator::isDate($value),
			'ip'               => Validator::isIp($value),
			default            => false
		};
	}

	/**
	 * Check object.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function hasObject($type, $object, $item) : bool
	{
		return match ($this->lowercase($type)) {
			'interface' => TypeCheck::hasInterface($object, Stringify::toInterface($item)),
			'method'    => TypeCheck::hasMethod($object, $item),
			'parent'    => TypeCheck::isSubClassOf($object, $item),
			'child'     => TypeCheck::isObject($item, $object),
			default     => false
		};
	}

	/**
	 * Convert array to object.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function toObject(array $array, $strict = false) : object
	{
		return Converter::toObject($array, $strict);
	}

	/**
	 * Convert object to array.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function toArray(object $object) : array
	{
		return Converter::toArray($object);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function toFloat($number, int $decimals = 0, string $dSep = '.', string $tSep = ',') : float
	{
		return Converter::toFloat($number, $decimals, $dSep, $tSep);
	}

	/**
	 * Convert data to key.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function toKey($data) : string
	{
		return Converter::toKey($data);
	}

	/**
	 * Escape HTML.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function escapeHTML(string $string) : string
	{
		return $string;
	}

	/**
	 * Escape HTML attribute.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function escapeAttr(string $string) : string
	{
		return $string;
	}

	/**
	 * Escape textarea.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function escapeTextarea(string $string) : string
	{
		return $string;
	}

	/**
	 * Escape JS.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function escapeJS(string $string) : string
	{
		return $string;
	}

	/**
	 * Escape SQL.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function escapeSQL(string $string) : string
	{
		return $string;
	}

	/**
	 * Escape Url.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function escapeUrl(string $string) : string
	{
		return $string;
	}

	/**
	 * Sanitize text field.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function sanitizeText(string $string) : string
	{
		return $string;
	}

	/**
	 * Sanitize textarea field.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function sanitizeTextarea(string $string) : string
	{
		return $string;
	}

	/**
	 * Sanitize email.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function sanitizeEmail(string $string) : string
	{
		return $string;
	}

	/**
	 * Sanitize url.
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function sanitizeUrl(string $string) : string
	{
		return $string;
	}

	/**
	 * Sanitize HTML content (XSS).
	 *
	 * @access public
	 * @inheritdoc
	 * @todo
	 */
	public function sanitizeHTML(string $string) : string
	{
		return $string;
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isCli() : bool
	{
		return System::isCli();
	}

	/**
	 * Validate PHP module.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isModule(string $module) : bool
	{
		return Validator::isModule($module);
	}

	/**
	 * Validate server module.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isServerModule(string $module) : bool
	{
		return Validator::isServerModule($module);
	}

	/**
	 * Validate server config.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isServerConfig(string $name, $value) : bool
	{
		return Validator::isConfig($name, $value);
	}

	/**
	 * Validate version.
	 *
	 * @access public
	 * @inheritdoc
	 */
	public function isVersion(string $v1, string $v2, string $operator = '==') : bool
	{
		return Validator::isVersion($v1, $v2, $operator);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 * @todo
	 */
	protected function parseUrl(string $url, int $component = -1) : mixed
	{
		return Stringify::parseUrl($url, $component);
	}
}

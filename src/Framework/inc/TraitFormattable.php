<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

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

trait TraitFormattable
{
    use TraitSerializable;
    use TraitMapable;
    use TraitSecurable;
    
	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function formatKey(string $key) : string
    {
        return Stringify::formatKey($key);
    }
    
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function formatPath(string $path, bool $untrailing = false) : string
    {
        return Stringify::formatPath($path, $untrailing);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function lowercase(string $string) : string
    {
        return Stringify::lowercase($string);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function uppercase(string $string) : string
    {
        return Stringify::uppercase($string);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function capitalize(string $string) : string
    {
        return Stringify::capitalize($string);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function slugify(string $string) : string
    {
        return Stringify::slugify($string);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function untrailingSlash(string $string) : string
	{
	    return Stringify::untrailingSlash($string);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function trailingSlash(string $string) : string
	{
	    return Stringify::trailingSlash($string);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function searchString($string, $search) : bool
    {
        return Stringify::contains($string, $search);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function replaceString($search, $replace, $subject, $regex = false)
    {
        if ( $regex ) {
            return Stringify::replaceRegex($search, $replace, $subject);
        }
        return Stringify::replace($search, $replace, $subject);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function replaceSubString($search, $replace, $offset = 0, $length = null)
    {
        return Stringify::subreplace($search, $replace, $offset, $length);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function replaceStringArray(array $replace, string $subject) : string
	{
		return Stringify::replaceArray($replace, $subject);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function removeString($search, $subject) : string
	{
		return Stringify::remove($search, $subject);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function matchString($regex, $string, &$matches, $flags = 0, $offset = 0) : bool
	{
		return Stringify::match($regex, $string, $matches, $flags, $offset);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function matchEveryString(string $regex, string $string, &$matches, int $flags = 0, int $offset = 0) : bool
	{
		return Stringify::matchAll($regex, $string, $matches, $flags, $offset);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function splitString($string, $args = [])
	{
		return Stringify::split($string, $args);
	}

	/**
	 * Limit string.
	 *
	 * @access protected
	 * @inheritdoc
	 */
	protected function limitString(string $string, int $limit = 150) : string
	{
		return Stringify::limit($string, $limit);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function stripSpace($string, $replace = '') : string
	{
		return Stringify::stripSpace($string, $replace);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function formatSpace(string $string) : string
	{
		return Stringify::formatSpace($string);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function buildQuery($args, string $prefix = '', ?string $sep = '&', int $enc = 1) : string
	{
		return Stringify::buildQuery($args, $prefix, $sep, $enc);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function parseUrl(string $url, int $component = -1)
	{
		return Stringify::parseUrl($url, $component);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function parseString(string $string, array &$result = null)
	{
		return Stringify::parse($string, $result);
	}
	
	/**
	 * Get basename with path format.
	 * 
	 * @access protected
	 * @inheritdoc
	 */
	protected function basename(string $path, string $suffix = '') : string
	{
		return Stringify::basename($path, $suffix);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function formatJson($value, $flags = 64|256)
    {
        return Json::format($value, $flags);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function parseJson($file, $isArray = false)
    {
        return Json::parse($file, $isArray);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function decodeJson($value, $isArray = false)
	{
		return Json::decode($value, $isArray);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function encodeJson($value) : string
	{
		return Json::encode($value);
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
	 * @access protected
	 * @inheritdoc
	 */
	protected function parseXml(string $xml, int $args = 16384|20908)
	{
		return Xml::parse($xml, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function parseXmlFile(string $path, int $args = 16384|20908)
	{
		return Xml::parseFile($path, $args);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function inArray($value, $array) : bool
    {
        return Arrayify::inArray($value, $array);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function mergeArray(array ...$arrays) : array
    {
        return Arrayify::merge(...$arrays);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function filterArray(array $array, $callback = null, $mode = 0) : array
    {
        return Arrayify::filter($array, $callback, $mode);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasArrayKey($key, array $array) : bool
	{
		return Arrayify::hasKey($key, $array);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function arrayKeys(array $array) : array
	{
		return Arrayify::keys($array);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function arrayValues(array $array) : array
	{
		return Arrayify::values($array);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function arrayShift(array &$array)
	{
		return Arrayify::shift($array);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function uniqueArray(array $array, $flags = SORT_STRING) : array
	{
		return Arrayify::unique($array, $flags);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function uniqueMultiArray(array $array) : array
	{
		return Arrayify::uniqueMultiple($array);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
    protected function isType($type, $value) : bool
    {
        switch (Stringify::lowercase($type)) {
            case 'array':
                return TypeCheck::isArray($value);
                break;

            case 'string':
                return TypeCheck::isString($value);
                break;

            case 'int':
                return TypeCheck::isInt($value);
                break;

            case 'numeric':
                return TypeCheck::isNumeric($value);
                break;

            case 'float':
                return TypeCheck::isFloat($value);
                break;

            case 'bool':
                return TypeCheck::isBool($value);
                break;

            case 'false':
                return TypeCheck::isFalse($value);
                break;

            case 'true':
                return TypeCheck::isTrue($value);
                break;

            case 'null':
                return TypeCheck::isNull($value);
                break;

            case 'empty':
                return TypeCheck::isEmpty($value);
                break;

            case 'class':
                return TypeCheck::isClass($value);
                break;

            case 'function':
                return TypeCheck::isFunction($value);
                break;

            case 'callable':
                return TypeCheck::isCallable($value);
                break;

            case 'email':
                return Validator::isValidEmail($value);
                break;

            case 'url':
                return Validator::isValidUrl($value);
                break;
        }
        return false;
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
	 * @access protected
	 * @inheritdoc
	 */
    protected function hasItem($type, $object, $item) : bool
    {
        switch (Stringify::lowercase($type)) {

            case 'interface':
                $i = Stringify::lowercase($item);
                if ( !Stringify::contains($i, 'interface') ) {
                    $item = Stringify::capitalize($item);
                    $item = "{$item}Interface";
                }
                return TypeCheck::hasInterface($object, $item);
                break;

            case 'method':
                return TypeCheck::hasMethod($object, $item);
                break;

            case 'parent':
                return TypeCheck::isSubClassOf($object, $item);
                break;

        }

        return false;
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function toFloat($number, int $decimals = 0, string $dSep = '.', string $tSep = ',') : float
	{
		return Converter::toFloat($number, $decimals, $dSep, $tSep);
	}
}

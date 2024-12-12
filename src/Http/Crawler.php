<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Http Component
 * @version    : 1.4.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Http;

use FloatPHP\Classes\Filesystem\{Arrayify, Stringify};
use FloatPHP\Classes\Http\Client;
use FloatPHP\Classes\Http\Curl;
use FloatPHP\Classes\Server\System;
use FloatPHP\Exceptions\Helpers\CrawlerException;

System::setTimeLimit(0);
System::setMemoryLimit('-1');

/**
 * Built-in HTTP crawler.
 */
final class Crawler
{
	/**
	 * @access private
	 */
	private $urls = [];
	private $params = [];
	private $bypass = false;
	private $resources = 4;
	private $save = false;
	private $path = 'public/cache';
	private $signature = true;
	private $ua;
	private static $limit = 1000;

	/**
	 * Init crawler.
	 *
	 * @access public
	 * @param array $urls
	 * @param array $params
	 */
	public function __construct(array $urls = [], array $params = [])
	{
		// Limit URLs
		$this->setUrls($urls);

		// Set default User-Agent
		$this->ua = Stringify::formatPath(self::class);

		// Set Http client params
		$this->params = Arrayify::merge([
			'method'   => Client::GET,
			'timeout'  => 3,
			'redirect' => 3,
			'follow'   => true,
			'return'   => true,
			'ua'       => "{$this->ua}/1.0",
			'header'   => [
				'Cache-Control' => 'no-cache'
			]
		], $params);
	}

	/**
	 * Bypass resources check.
	 *
	 * @access public
	 * @return object
	 */
	public function bypass() : self
	{
		$this->bypass = true;
		return $this;
	}

	/**
	 * Set minimum CPU cores.
	 *
	 * @access public
	 * @param int $cores
	 * @return object
	 */
	public function setResources(int $cores) : self
	{
		$this->resources = $cores;
		return $this;
	}

	/**
	 * Set URLs limit.
	 *
	 * @access public
	 * @param int $limit
	 * @return void
	 */
	public static function limit(int $limit) : void
	{
		self::$limit = $limit;
	}

	/**
	 * Disable file signature.
	 *
	 * @access public
	 * @return object
	 */
	public function noSignature() : self
	{
		$this->signature = false;
		return $this;
	}

	/**
	 * Save crawled target response.
	 *
	 * @access public
	 * @param string $path
	 * @return object
	 */
	public function save(?string $path = null) : self
	{
		$path = $path ?: $this->path;
		$this->path = ltrim($path, '/');
		$this->save = true;
		return $this;
	}

	/**
	 * Run crawler.
	 *
	 * @access public
	 * @return array
	 * @throws CrawlerException
	 */
	public function run() : array
	{
		if ( !$this->bypass && !$this->hasResources() ) {
			throw new CrawlerException(
				CrawlerException::insufficientResources(
					$this->resources
				)
			);
		}

		// Set extra params
		$extra = [];
		if ( $this->save ) {
			$extra['path'] = $this->path;
			$extra['ext'] = '.html';
		}
		if ( $this->signature ) {
			$extra['signature'] = "<!-- Cache: {$this->ua} -->";
		}

		// Send multiple request
		return Curl::requestMultiple(
			$this->urls,
			$this->params,
			$extra
		);
	}

	/**
	 * Check server resources.
	 *
	 * @access private
	 * @return bool
	 */
	private function hasResources() : bool
	{
		$memory = System::isMemoryOut();
		$cpu = System::getCpuCores() < $this->resources;
		return !$memory && !$cpu;
	}

	/**
	 * Set crawler limited URLs.
	 *
	 * @access private
	 * @param array $urls
	 * @return void
	 */
	private function setUrls(array $urls) : void
	{
		if ( count($urls) > self::$limit ) {
			$urls = Arrayify::slice($urls, 0, self::$limit);
		}
		$this->urls = $urls;
	}
}

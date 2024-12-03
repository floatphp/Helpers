<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Http Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Http;

use FloatPHP\Classes\Filesystem\{Arrayify, File, Stringify};
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
	private $limit = 10000;
	private $bypass = false;
	private $save = false;
	private $resources = 2;

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

		// Set Http client params
		$ua = Stringify::formatPath(self::class);
		$this->params = Arrayify::merge([
			'method'   => Client::HEAD,
			'timeout'  => 3,
			'redirect' => 2,
			'follow'   => true,
			'ua'       => "{$ua}/1.0",
			'header'   => [
				'Cache-Control' => 'no-cache'
			]
		], $params);
	}

	/**
	 * Run crawler.
	 *
	 * @access public
	 * @return bool
	 * @throws CrawlerException
	 */
	public function run() : bool
	{
		if ( !$this->bypass && !$this->hasResources() ) {
			throw new CrawlerException(
				CrawlerException::insufficientResources(
					$this->resources
				)
			);
		}

		// Set multiple handles
		$multiple = Curl::initMultiple();
		$handles = [];

		// Extract params
		$params = Client::getParams($this->params);
		extract($params);

		foreach ($this->urls as $url) {

			// Init
			$handle = Curl::init($url);

			// Set options
			Curl::setOptions($handle, [
				Curl::RETURNTRANSFER => $return,
				Curl::FOLLOWLOCATION => $follow,
				Curl::MAXREDIRS      => $redirect,
				Curl::TIMEOUT        => $timeout,
				Curl::HTTPHEADER     => $header,
				Curl::CUSTOMREQUEST  => $method,
				Curl::VERIFYHOST     => $ssl == true ? 2 : false,
				Curl::VERIFYPEER     => $ssl,
				Curl::USERAGENT      => $ua
			]);

			Curl::addHandle($multiple, $handle);
			$handles[$url] = $handle;
		}

		Curl::executeMultiple($multiple);

		foreach ($handles as $url => $h) {
			if ( !$this->save === false ) {
				$name = Stringify::slugify(
					Client::parseUrl($url)
				);
				$path = Stringify::formatPath("{$this->save}{$name}.bak");
				$content = Curl::getMultipleContent($h);
				File::w($path, $content);
			}
			Curl::removeHandle($multiple, $h);
			Curl::close($h);
		}

		Curl::closeMultiple($multiple);

		return false;
	}

	/**
	 * Save crawled target response.
	 *
	 * @access public
	 * @param ?string $path
	 * @return object
	 */
	public function save(?string $path = null) : self
	{
		$this->params = Arrayify::merge($this->params, [
			'method' => Client::GET,
			'return' => true
		]);
		$this->save = (string)$path;
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
	 * Check server resources.
	 *
	 * @access private
	 * @return bool
	 */
	private function hasResources() : bool
	{
		$outOfMemory = System::isMemoryOut();
		$outOfCpu = System::getCpuCores() < $this->resources;
		return !$outOfMemory && !$outOfCpu;
	}

	/**
	 * Set crawler URLs.
	 *
	 * @access private
	 * @param array $urls
	 * @return void
	 */
	private function setUrls(array $urls) : void
	{
		if ( count($urls) > $this->limit ) {
			$urls = Arrayify::chunk($urls, $this->limit);
		}
		$this->urls = $urls;
	}
}

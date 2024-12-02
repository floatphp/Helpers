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

use FloatPHP\Classes\Filesystem\Arrayify;
use FloatPHP\Classes\Http\Client;
use FloatPHP\Classes\Server\System;

System::setTimeLimit(0);
System::setMemoryLimit('-1');

/**
 * Built-in HTTP crawler.
 */
final class Crawler
{
	/**
	 * @access private
	 * @var array $urls
	 * @var array $options
	 * @var int $limit
	 * @var bool $bypass, resources
	 * @var array $resources
	 */
	private $urls = [];
	private $options = [];
	private $limit = 10000;
	private $bypass = false;
	private $resources = [
		'cpu'    => 2,
		'memory' => 4
	];

	/**
	 * Init crawler.
	 *
	 * @access public
	 * @param array $urls
	 * @param array $options
	 */
	public function __construct(array $urls = [], array $options = [])
	{
		$this->setUrls($urls);
		$this->options = Arrayify::merge([
			'method'      => 'GET',
			'timeout'     => 10,
			'redirection' => 2,
			'headers'     => [
				'Cache-Control' => 'no-cache',
				'User-Agent'    => 'FloatPHPCrawler/1.0'
			]
		], $options);
	}

	/**
	 * Set URLs limit.
	 *
	 * @access public
	 * @param int $limit
	 * @return object
	 */
	public function setLimit(int $limit) : self
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Set resources.
	 *
	 * @access public
	 * @param int $cpu
	 * @param int $memory
	 * @return object
	 */
	public function setResources(int $cpu, int $memory) : self
	{
		$this->resources = [
			'cpu'    => $cpu,
			'memory' => $memory
		];
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
	 * Start crawler.
	 *
	 * @access public
	 * @param int $try
	 * @return bool
	 */
	public function start(int $try = 2) : bool
	{
		// if ( self::hasResources() ) {
		// 	$try = ($try <= 5) ? $try : 2;
		// 	foreach (Post::all() as $post) {
		// 		if ( $this->pattern == '*' || $this->match($post['content']) ) {
		// 			$this->ping($post['link'], $try);
		// 		}
		// 	}
		// }
		// return (bool)$this->status;
	}

	/**
	 * Ping url.
	 *
	 * @access private
	 * @param string $url
	 * @param int $try
	 * @return void
	 */
	private function ping(string $url, int $try = 2)
	{
		// $i = 1;
		// while ($i <= $try) {
		// 	$response = self::do($url, $this->args);
		// 	if ( self::getStatusCode($response) == 200 ) {
		// 		$this->status += 1;
		// 	}
		// 	$i++;
		// 	sleep(1);
		// }
	}

	/**
	 * Check server resources.
	 *
	 * @access private
	 * @return bool
	 */
	private function hasResources() : bool
	{
		$memory = System::getSystemMemoryUsage();
		$cpu = System::getCpuUsage();
		$outOfMemory = $this->resources['memory'] > $memory;
		return System::getCpuCores() >= 2;
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

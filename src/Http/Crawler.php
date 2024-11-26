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

use FloatPHP\Classes\Http\Client;
use FloatPHP\Classes\Server\System;

System::setTimeLimit(0);
System::setMemoryLimit('-1');

/**
 * Built-in crawler.
 */
final class Crawler
{
	/**
	 * @access private
	 * @var array $urls
	 */
	private $urls = [];

	/**
	 * Init crawler.
	 *
	 * @param array $urls
	 */
	public function __construct(array $urls)
	{
		$this->pattern = $pattern;
		$this->args = Arrayify::merge([
			'method'      => self::GET,
			'timeout'     => 5,
			'redirection' => 2,
			'blocking'    => false,
			'headers'     => ['Cache-Control' => 'max-age=0']
		], $args);
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
		if ( self::canStart() ) {
			$try = ($try <= 5) ? $try : 2;
			foreach (Post::all() as $post) {
				if ( $this->pattern == '*' || $this->match($post['content']) ) {
					$this->ping($post['link'], $try);
				}
			}
		}
		return (bool)$this->status;
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
		$i = 1;
		while ($i <= $try) {
			$response = self::do($url, $this->args);
			if ( self::getStatusCode($response) == 200 ) {
				$this->status += 1;
			}
			$i++;
			sleep(1);
		}
	}

	/**
	 * Check server capacity.
	 *
	 * @access private
	 * @return bool
	 */
	private function canStart() : bool
	{
		return (System::getCpuCores() >= 2) ?? false;
	}

	/**
	 * Match crawling post using content.
	 *
	 * @access private
	 * @param string $content
	 * @return bool
	 */
	private function match(string $content) : bool
	{
		return Stringify::match($this->pattern, $content);
	}
}

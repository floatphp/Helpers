<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Classes\Server\System;

System::setTimeLimit(seconds: 0);
System::setMemoryLimit(value: '-1');

class Sitemap
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\inc\TraitDatable;

	/**
	 * @access protected
	 */
	protected const NAME   = 'sitemap';
	protected const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
	protected const TAG    = '<?xml version="1.0" encoding="UTF-8"?>';
	protected const EXT    = 'xml';
	protected const DATE   = 'Y-m-d\TH:i:sP';
	protected const LIMIT  = 10000;
	protected const PATHS  = [
		'index' => '',
		'item'  => '/sitemap'
	];

	/**
	 * @access protected
	 * @var array $paths
	 * @var array $urls
	 * @var int $counter
	 * @var int $limit
	 * @var bool $baseUrl
	 * @var string $prefix
	 * @var bool $slash
	 * @var bool $robot
	 */
	protected $paths = [];
	protected $urls = [];
	protected $counter = 0;
	protected $limit = 0;
	protected $baseUrl = false;
	protected $prefix;
	protected $slash = false;
	protected $robot = true;

	/**
	 * Init sitemap.
	 *
	 * @access public
	 * @param int $limit
	 * @param bool $reset, Reset sitemaps
	 */
	public function __construct(int $limit = self::LIMIT, bool $reset = true)
	{
		$this->limit = ($limit > self::LIMIT)
			? self::LIMIT : $limit;

		$this->initConfig();
		$this->paths = [
			'index' => $this->getRoot(static::PATHS['index']),
			'item'  => $this->getPublicPath(static::PATHS['item'])
		];
		$this->urls = [
			'index' => $this->getBaseUrl(static::PATHS['index']),
			'item'  => $this->getPublicUrl(static::PATHS['item'])
		];
		$this->resetConfig();

		if ( $reset ) {
			$this->clearDir($this->paths['item']);
		}
	}

	/**
	 * Set base URL.
	 *
	 * @access public
	 * @return object
	 */
	public function setBaseUrl(?string $baseUrl = null) : self
	{
		if ( $baseUrl ) {

			$index = $this->urls['index'];
			$item = $this->urls['item'];

			$index = $this->replaceString('http:', $baseUrl, $index);
			$item = $this->replaceString('http:', $baseUrl, $item);

			$this->urls = ['index' => $index, 'item' => $item];
		}

		$this->baseUrl = true;
		return $this;
	}

	/**
	 * Set URLs prefix.
	 *
	 * @access public
	 * @param string $prefix
	 * @return object
	 */
	public function setPrefix(string $prefix) : self
	{
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * Add untrailing slash to URLs.
	 *
	 * @access public
	 * @return object
	 */
	public function slash() : self
	{
		$this->slash = true;
		return $this;
	}

	/**
	 * Disable robots.txt.
	 *
	 * @access public
	 * @return object
	 */
	public function noRobot() : self
	{
		$this->robot = false;
		return $this;
	}

	/**
	 * Generate sitemaps.
	 *
	 * @access public
	 * @param array $data
	 * @return bool
	 */
	public function generate(array $data = []) : bool
	{
		foreach ($data as $prefix => $item) {

			if ( !$item ) {
				continue;
			}

			// Set prefix from key
			if ( $this->isType('str', $prefix) ) {
				$this->prefix = $prefix;
			}

			// Add home URL
			if ( $this->counter === 0 ) {
				$item = $this->mergeArray(['/'], $item);
			}

			// Generate item sitemaps
			if ( $this->isType('[]', $item) ) {
				$this->generateItem($item);
			}
		}

		// Generate index sitemap
		$this->generateIndex();

		// Generate robots.txt
		if ( $this->robot ) {
			$this->generateRoboto();
		}

		return (bool)$this->counter;
	}

	/**
	 * Generate robots.txt.
	 *
	 * @access protected
	 * @return bool
	 */
	protected function generateRoboto() : bool
	{
		$path = $this->untrailingSlash(
			$this->paths['index']
		);
		$file = "{$path}/robots.txt";
		$file = $this->open($file);

		if ( !$this->isType('res', $file) ) {
			return false;
		}

		$name = static::NAME;
		$ext = static::EXT;
		$base = $this->untrailingSlash(
			$this->urls['index']
		);

		$url = "{$base}/{$name}.{$ext}";
		$url = $this->formatPath($url);

		$this->write($file, "User-Agent: *");
		$this->write($file, "Sitemap: {$url}", true);

		return true;
	}

	/**
	 * Generate item sitemaps from 2D array.
	 *
	 * @access protected
	 * @param array $data
	 * @return bool
	 */
	protected function generateItem(array $data = []) : bool
	{
		if ( !($total = count($data)) ) {
			return false;
		}

		$path = $this->untrailingSlash(
			$this->paths['item']
		);

		$ext = static::EXT;
		$name = static::NAME;
		$name = "{$path}/{$name}";

		for ($offset = 0; $offset < $total; $offset += $this->limit) {

			$parts = $this->sliceArray($data, $offset, $this->limit);

			$this->counter++;
			$file = "{$name}-{$this->counter}.{$ext}";
			$file = $this->open($file);

			if ( !$this->isType('res', $file) ) {
				return false;
			}

			$this->setHeader($file);
			foreach ($parts as $url) {
				$this->setUrl($file, $url);
			}
			$this->setFooter($file);
		}

		return true;

	}

	/**
	 * Generate index sitemap.
	 *
	 * @access protected
	 * @return bool
	 */
	protected function generateIndex() : bool
	{
		$name = static::NAME;
		$ext = static::EXT;

		$file = $this->untrailingSlash(
			$this->paths['index']
		);

		$file = "{$file}/{$name}.{$ext}";
		$file = $this->open($file);

		if ( !$this->isType('resource', $file) ) {
			return false;
		}

		$this->setHeader($file, true);
		for ($i = 0; $i < $this->counter; $i++) {
			$n = $i + 1;
			$this->setUrl($file, "{$name}-{$n}.{$ext}", true);
		}
		$this->setFooter($file, true);

		return true;
	}

	/**
	 * Set XML header and opening tag.
	 *
	 * @access protected
	 * @param resource $file
	 * @param bool $index
	 * @return void
	 */
	protected function setHeader($file, bool $index = false) : void
	{
		$this->write($file, static::TAG);

		$tag = '<urlset xmlns="';
		if ( $index ) {
			$tag = '<sitemapindex xmlns="';
		}

		$this->write($file, $tag . static::SCHEMA . '">');
	}

	/**
	 * Set XML closing tag.
	 *
	 * @access protected
	 * @param resource $file
	 * @param bool $index
	 * @return void
	 */
	protected function setFooter($file, bool $index = false) : void
	{
		$tag = '</urlset>';
		if ( $index ) {
			$tag = '</sitemapindex>';
		}

		$this->write($file, $tag, true);
	}

	/**
	 * Set sitemap single URL.
	 *
	 * @access protected
	 * @param resource $file
	 * @param string $url
	 * @param bool $index
	 * @return void
	 */
	protected function setUrl($file, string $url, bool $index = false) : void
	{
		$tag = 'url';

		if ( $index ) {

			$tag = 'sitemap';
			$base = $this->urls['item'];
			$url = "{$base}/{$url}";

		} else {

			if ( $this->prefix && $url !== '/' ) {
				$url = "{$this->prefix}/{$url}";
			}

			if ( $this->baseUrl ) {
				$base = $this->urls['index'];
				$url = "{$base}/{$url}";
			}

			if ( $this->slash ) {
				$url = "{$url}/";
			}

		}

		$url = htmlspecialchars(
			$this->formatPath($url)
		);

		$this->write($file, "<{$tag}>");
		$this->write($file, "<loc>{$url}</loc>");

		if ( $index ) {
			$date = $this->getDate('now', static::DATE);
			$this->write($file, "<lastmod>{$date}</lastmod>");
		}

		$this->write($file, "</{$tag}>");
	}

	/**
	 * Open file.
	 *
	 * @access protected
	 * @param resource $file
	 * @return mixed
	 */
	protected function open($file) : mixed
	{
		return @fopen($file, 'w');
	}

	/**
	 * Set file content.
	 *
	 * @access protected
	 * @param resource $file
	 * @param string $content
	 * @param bool $close
	 * @return void
	 */
	protected function write($file, string $content, bool $close = false) : void
	{
		@fwrite($file, "{$content}{$this->breakString()}");
		if ( $close ) {
			fclose($file);
		}
	}
}

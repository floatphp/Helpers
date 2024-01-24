<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Http Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Http;

/**
 * Dynamic request catcher class,
 * Used to extract vars from request.
 */
final class Catcher
{
	use \FloatPHP\Helpers\Framework\inc\TraitRequestable,
		\FloatPHP\Helpers\Framework\inc\TraitFormattable;

	/**
	 * @access private
	 * @var array $vars, Request vars
	 * @var string $group, Request vars group
	 */
	private $vars;
	private $group;

	/**
	 * Init catcher.
	 * 
	 * @param array $request
	 * @param string $group
	 */
    public function __construct(?array $request = [], ?string $group = null)
    {
		$this->group = $group;
		$this->extract($request);
    }

	/**
	 * Get request var.
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get(string $name)
	{
		return $this->vars["--{$name}"] ?? null;
	}

	/**
	 * Extract request vars from server.
	 *
	 * @access private
	 * @param array $request
	 * @return void
	 */
	private function extract(?array $request = [])
	{
		// Set default request
		$default = (array)$this->getRequest($this->group);
		$this->vars = $this->mergeArray($request, $default);

		// Set unique key
		if ( isset($this->vars['key']) ) {
			$this->vars['--key'] = $this->vars['key'];
			unset($this->vars['key']);
		}

		// Init request unique key
		if ( !isset($this->vars['--key']) ) {
			$this->vars['--key'] = false;
		}

		// Set request dynamic key
		if ( !$this->vars['--key'] ) {

			$url = $this->getServer('http-referer');
			if ( empty($url) ) {
				$url = $this->getServer('request-uri');
			}

			$url = $this->parseUrl($url);
			$path = $url['path'] ?? false;

            if ( strpos($path, '/admin/') == 0 ) {
	        	$path = $this->removeString('/admin/', $path);
	        	$vars = explode('/', $path);
	        	$this->vars['--key'] = $vars[0] ?? false;
            }
		}

		// Format key
		$this->vars['--key'] = $this->stripSpace(
			$this->vars['--key']
		);
		if ( $this->searchString($this->vars['--key'], ',') ) {
			$this->vars['--key'] = explode(',', $this->vars['--key']);
		}
	}
}

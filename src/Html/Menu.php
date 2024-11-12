<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Html Component
 * @version    : 1.2.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Html;

use FloatPHP\Helpers\Filesystem\Cache;

/**
 * Menu factory class.
 */
final class Menu
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\inc\TraitPermissionable,
		\FloatPHP\Helpers\Framework\inc\TraitCacheable,
		\FloatPHP\Helpers\Framework\inc\TraitTranslatable,
		\FloatPHP\Helpers\Framework\inc\TraitRequestable;

	/**
	 * @access private
	 * @var array $menu
	 * @var array $native
	 * @var bool $debug
	 * @var string $lang
	 * @var int $user
	 * @var string $itemClass
	 * @var string $linkClass
	 * @var string $linkSubClass
	 * @var string $subClass
	 * @var string $activeClass
	 * @var string $SHOW
	 */
	private $menu;
	private $native;
	private $debug;
	private $lang;
	private $user;
	private $itemClass;
	private $linkClass;
	private $linkSubClass;
	private $subClass;
	private $activeClass;
	private $showClass;

	/**
	 * @access private
	 * @var string LANG
	 * @var string ITEM
	 * @var string LINK
	 * @var string LINKSUB
	 * @var string SUB
	 * @var string ACTIVE
	 */
	private const ITEM    = 'nav-item';
	private const LINK    = 'nav-link';
	private const LINKSUB = 'with-sub';
	private const SUB     = 'nav-sub';
	private const ACTIVE  = 'active';
	private const SHOW    = 'show';

	/**
	 * @param int $user
	 * @param string $lang
	 */
	function __construct(?int $user = null, ?string $lang = null)
	{
		// Init configuration
		$this->initConfig();

		$this->debug = $this->isDebug();
		$this->user = $user;
		$this->lang = $lang;
		$this->native = $this->menu = $this->getMenu();
		$this->setCSS();

		$this->getTranslatorObject($this->lang);

		// Reset configuration
		$this->resetConfig();
	}

	/**
	 * Get native menu without translation.
	 * 
	 * @access public
	 * @return array
	 */
	public function getNative() : array
	{
		return $this->native;
	}

	/**
	 * Generate translated menu.
	 * 
	 * @access public
	 * @return array
	 */
	public function generate() : array
	{
		$this->setActive();
		return $this->menu;
	}

	/**
	 * Prepare menu generating.
	 * 
	 * @access public
	 * @return object
	 */
	public function prepare() : Menu
	{
		if ( $this->useCache && !$this->debug ) {

			Cache::$debug = false;
			$cache = new Cache();
			$key = $cache->getKey('menu', [
				'user' => $this->user,
				'lang' => $this->lang
			]);

			$data = $cache->get($key, $status);
			if ( !$status ) {
				$data = $this->build($this->menu);
				$cache->set($key, $data, 0, 'menu');
			}
			$this->menu = $data;

		} else {
			$this->menu = $this->build($this->menu);
		}
		
		return $this;
	}

	/**
	 * Append item to menu array.
	 * 
	 * @access public
	 * @param array $item
	 * @return object
	 */
	public function with(array $item) : Menu
	{
		$item = $this->build($item);
		$this->menu = $this->mergeArray($this->menu, $item);
		return $this;
	}

	/**
	 * Disable cache.
	 *
	 * @access public
	 * @return object
	 */
	public function noCache() : self
	{
		$this->useCache = false;
		return $this;
	}

	/**
	 * Set menu CSS class.
	 * 
	 * @access public
	 * @param array $css
	 * @return void
	 */
	public function setCSS($css = [])
	{
		$this->itemClass = $css['item'] ?? self::ITEM;
		$this->linkClass = $css['link'] ?? self::LINK;
		$this->linkSubClass = $css['linksub'] ?? self::LINKSUB;
		$this->subClass = $css['sub'] ?? self::SUB;
		$this->showClass = $css['show'] ?? self::SHOW;
		$this->activeClass = $css['active'] ?? self::ACTIVE;
	}

	/**
	 * Build menu.
	 * 
	 * @access private
	 * @param array $menu
	 * @return array
	 */
	private function build(array $menu) : array
	{
		// Convert 2D
		$temp = $menu[0] ?? false;
		if ( !$this->isType('array', $temp) ) {
			$menu = [$menu];
		}

		foreach ($menu as $key => $item) {

			// Check 2D
			if ( !$this->isType('int', $key) 
			  && !$this->isType('array', $item) ) {
				continue;
			}

			// Translate
			if ( isset($menu[$key]['name']) ) {
				$menu[$key]['name'] = $this->translator->translate($item['name']);
			}

			// Set access
			if ( !$this->hasAccess($item) ) {
				unset($menu[$key]);
			}

		}

		return $this->filterArray($menu);
	}

	/**
	 * Check whether user has menu access.
	 * 
	 * @access private
	 * @param array $item
	 * @return bool
	 */
	private function hasAccess(array $item) : bool
	{
		if ( isset($item['role']) ) {
			return $this->hasRole($item['role']);
		}
		return true;
	}

	/**
	 * Set active menu.
	 * 
	 * @access private
	 * @return void
	 */
	private function setActive()
	{
		foreach ($this->menu as $key => $item) {
			if ( isset($item['type']) && $item['type'] == 'menu' ) {
				$sub = $item['sub'] ?? false;

				// Default link class
				$linkClass = $this->menu[$key]['link-class'] ?? '';
				if ( empty($linkClass) ) {
					$linkClass = $this->linkClass;
				}
				$this->menu[$key]['link-class'] = $linkClass;

				if ( $sub !== false ) {
					
					// Activate first level menu sub class
					$this->menu[$key]['link-class'] .= " {$this->linkSubClass}";

					foreach ($sub as $n => $i) {

						// Default sub parent class
						$itemClass = $this->menu[$key]['class'] ?? '';
						if ( empty($itemClass) ) {
							$itemClass = $this->itemClass;
						}

						// Default sub class
						$subClass = $this->menu[$key]['sub'][$n]['class'] ?? '';
						if ( empty($subClass) ) {
							$subClass = $this->subClass;
						}

						// Activate sub and parent
						if ( $this->isActive($i['url']) ) {
							$itemClass .= " {$this->activeClass} {$this->showClass}";
							$subClass .= " {$this->activeClass}";
						}

						$this->menu[$key]['class'] = $itemClass;
						$this->menu[$key]['sub'][$n]['class'] = $subClass;

						// Translate sub
						$this->menu[$key]['sub'][$n]['name'] = $this->translator->translate($i['name']);

						// Set sub access
						if ( !$this->hasAccess($i) ) {
							unset($this->menu[$key]['sub'][$n]);
						}
					}

				} else {

					// Default first level class
					$itemClass = $this->menu[$key]['class'] ?? '';
					if ( empty($itemClass) ) {
						$itemClass = $this->itemClass;
					}

					// Activate first level menu
					if ( $this->isActive($item['url']) ) {
						$itemClass .= " {$this->activeClass}";
					}
					$this->menu[$key]['class'] = $itemClass;
				}
			}
		}
	}

	/**
	 * Check whether menu is active.
	 * 
	 * @access private
	 * @param string $url
	 * @return bool
	 */
	private function isActive($url = '') : bool
	{
		if ( $this->searchString($this->getServer('request-uri'), $url) ) {
			return true;
		}
		return false;
	}
}

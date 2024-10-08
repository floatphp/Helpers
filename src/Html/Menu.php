<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Html Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Html;

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
	private const ITEM = 'nav-item';
	private const LINK = 'nav-link';
	private const LINKSUB = 'with-sub';
	private const SUB = 'nav-sub';
	private const ACTIVE = 'active';
	private const SHOW = 'show';

	/**
	 * @param int $user
	 * @param string $lang
	 */
	function __construct(?int $user = null, ?string $lang = null)
	{
		// Init configuration
		$this->initConfig();

		$this->user = $user;
		$this->lang = $lang;
		$this->native = $this->menu = $this->getMenu();
		$this->setCSS();

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
		if ( $this->useCache ) {
			$this->getCacheObject();
			$key = $this->cache->generateKey('menu', false, [
				'user' => $this->user,
				'lang' => $this->lang
			]);
			$menu = $this->cache->get($key);
			if ( !$this->cache->isCached() ) {
				$menu = $this->build($this->menu);
				$this->cache->set($menu, 'menu', 3600);
			}
			$this->menu = $menu;

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
		$this->getTranslatorObject($this->lang);

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

			if ( isset($item['type']) && $item['type'] == 'menu' ) {

				// Translate
				if ( isset($menu[$key]['name']) ) {
					$menu[$key]['name'] = $this->translator->translate($item['name']);
				}

				// Set access
				if ( !$this->hasAccess($item) ) {
					unset($menu[$key]);
				}
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

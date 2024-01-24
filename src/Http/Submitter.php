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
 * Dynamic request submitter class,
 * Used to format request input.
 */
final class Submitter
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\inc\TraitRequestable;

	/**
	 * @access private
	 * @var array $vars, Request vars
	 * @var string $group, Request vars group
	 */
	private $vars;
	private $group;

	/**
	 * Init input.
	 * 
	 * @param string $group
	 * @uses initConfig()
	 * @uses resetConfig()
	 */
    public function __construct(?string $group = null)
    {
        // Init configuration
        $this->initConfig();

		// Set input
		$this->group = (new Catcher(['--key' => $group]))->key;
		$this->vars = $this->getVars();

        // Reset configuration
        $this->resetConfig();
    }

	/**
	 * Format request input.
	 *
	 * @access public
	 * @param array $input
	 * @return array
	 */
	public function format(?array $input = []) : array
	{
		if ( !$input ) {
			$input = $this->getPost();
		}
		if ( !$this->isType('array', $input) ) {
			$input = (array)$input;
		}
		$vars = $this->vars[$this->group] ?? [];
		foreach ($input as $key => $value) {
			if ( isset($vars[$key]) ) {

				// Default
				if ( !$value && !$this->isType('null', $vars[$key]['default']) ) {
					$value = $vars[$key]['default'];
				}

				// Format
				if ( $vars[$key]['type'] == 'string' ) {
					$value = (string)$value;

				} elseif ( $vars[$key]['type'] == 'int' ) {
					$value = (int)$value;

				} elseif ( $vars[$key]['type'] == 'float' ) {
					$value = (float)$value;

				} elseif ( $vars[$key]['type'] == 'bool' ) {
					$value = (bool)$value;
				}

				// Length
				if ( $vars[$key]['length'] && $this->isType('string', $value) ) {
					$value = substr($value, 0, $vars[$key]['length']);
				}

				// Filter
				if ( !$this->isType('null', $vars[$key]['filter']) ) {
					if ( !$this->isType($vars[$key]['filter'], $value) ) {
						$value = '';
					}
				}

				// List
				if ( $this->inArray('list', $vars[$key]['functions']) ) {
					$list = explode("\n", $value);
					foreach ($list as $n => $item) {
						$item = $this->formatSpace((string)$item);
						if ( empty($item) ) {
							unset($list[$n]);
							
						} else {
							$list[$n] = $this->formatSpace((string)$item);
						}
					}
					$value = implode(',', $list);
				}

				// Serialize
				if ( $this->inArray('serialize', $vars[$key]['functions']) ) {
					$value = $this->serialize($value);
				}

				// Lowercase
				if ( $this->inArray('lowercase', $vars[$key]['functions']) ) {
					$value = $this->lowercase((string)$value);
				}

				// Uppercase
				if ( $this->inArray('uppercase', $vars[$key]['functions']) ) {
					$value = $this->uppercase((string)$value);
				}

				// Capitalize
				if ( $this->inArray('capitalize', $vars[$key]['functions']) ) {
					$value = $this->capitalize((string)$value);
				}

				// Slugify
				if ( $this->inArray('slugify', $vars[$key]['functions']) ) {
					$value = $this->slugify((string)$value);
				}

				// Switch
				if ( $this->inArray('switch', $vars[$key]['functions']) ) {
					if ( $vars[$key]['type'] == 'int' ) {
						$value = ($value == 'on') ? 1 : 0;

					} else {
						$value = ($value == 'on') ? true : false;
					}
				}

				$input[$key] = $value;
			}
		}
		
		return $input;
	}

	/**
	 * Read request input.
	 *
	 * @access public
	 * @param array $input
	 * @return array
	 */
	public function read(array $input) : array
	{
		$vars = $this->vars[$this->group] ?? [];
		$input = $this->format($input);
		foreach ($input as $key => $value) {
			if ( isset($vars[$key]) ) {
				// Unlist
				if ( $this->inArray('list', $vars[$key]['functions']) ) {
					$input[$key] = implode("\n", explode(',', $value));
				}
			}
		}
		return $input;
	}

	/**
	 * Format request id.
	 *
	 * @access public
	 * @param mixed $id
	 * @param bool $force
	 * @return int
	 */
	public static function formatId($id, bool $force = false) : int
	{
		if ( $force ) {
			return intval($id);
		}
		return (int)$id;
	}
}

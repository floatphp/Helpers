<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Html Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Html;

use FloatPHP\Kernel\Base;
use FloatPHP\Classes\{
    Filesystem\TypeCheck,
    Filesystem\Stringify,
    Filesystem\File,
    Filesystem\Json,
    Html\Form
};

final class FormBuilder extends Base
{
	/**
	 * @access private
	 * @var object $form
	 * @var string $path
	 */
	private $form;
	private $path;

	/**
	 * @param array $options
	 * @param array $attributes
	 */
	function __construct($options = false, $attributes = false)
	{
		// Init configuration
		$this->initConfig();

		$this->path = "{$this->getAdminUploadPath()}/forms";
		$this->form = new Form($options, $attributes);
		$this->form->setToken(
			$this->getToken($this->form->getSource())
		);
		
        // Reset configuration
        $this->resetConfig();
	}

	/**
	 * @access public
	 * @param void
	 * @return object
	 */
	public function getForm() : object
	{
		return $this->form;
	}

	/**
	 * Save form schema
	 *
	 * @access public
	 * @param bool $serialize
	 * @param string $path
	 * @return string
	 */
	public function save($serialize = false, $path = '')
	{
		$data = $this->export($serialize);
		$path = !empty($path) ? $path : $this->path;
		$source = $this->form->getSource();
		if ( !empty($source) ) {
			$source .= '-';
		}
		$source .= date('d-m-Y');
		if ( $serialize ) {
			$source .= '.txt';
		} else {
			$source .= '.json';
		}
		File::w("{$path}/{$source}", $data);
	}

	/**
	 * Export form schema
	 *
	 * @access public
	 * @param bool $serialize
	 * @return string
	 */
	public function export($serialize = false)
	{
		$data = [
			'form' => [
				'attributes' => $this->form->getAttributes(),
				'options'    => $this->form->getOptions(),
				'inputs'     => $this->form->getInputs()
			]
		];
		if ( $serialize ) {
			return Stringify::serialize($data);
		}
		return Json::format($data, 64|128|256);
	}

	/**
	 * Import form schema
	 *
	 * @access public
	 * @param string $data
	 * @param bool $serialize
	 * @return void
	 */
	public function import($data = '', $serialize = false)
	{
		if ( $serialize ) {
			$data = Stringify::unserialize($data);

		} else {
			$data = Json::decode($data, true);
		}
		if ( TypeCheck::isArray($data) ) {

			// Import attributes
			$attributes = isset($data['form']['attributes'])
			? (array) $data['form']['attributes'] : [];
			foreach ($attributes as $key => $value) {
				$this->form->setAttribute($key,$value);
			}
			// Import options
			$options = isset($data['form']['options'])
			? (array) $data['form']['options'] : [];
			foreach ($options as $key => $value) {
				$this->form->setOptions($key,$value);
			}
			// Import inputs
			$inputs = isset($data['form']['inputs'])
			? (array) $data['form']['inputs'] : [];
			$this->form->setInputs($inputs);
		}
	}

	/**
	 * Import form schema file
	 *
	 * @access public
	 * @param string $path
	 * @param bool $serialize
	 * @return void
	 */
	public function importFile($path = '', $serialize = false)
	{
		$data = File::r($path);
		$this->import($data,$serialize);
	}
}

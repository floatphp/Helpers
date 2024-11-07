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

use FloatPHP\Classes\Html\Form as Main;

/**
 * Form factory class.
 */
final class Form extends Main
{
	use \FloatPHP\Kernel\TraitConfiguration,
		\FloatPHP\Helpers\Framework\inc\TraitTranslatable;

	/**
	 * @access private
	 * @var string $path
	 */
	private $path;

	/**
	 * @inheritdoc
	 * @uses initConfig()
	 * @uses resetConfig()
	 */
	function __construct(array $options = [], array $atts = [])
	{
		// Init configuration
		$this->initConfig();

		// Override
		$options = $this->mergeArray([
			'form'   => false,
			'submit' => false
        ], $options);

		$this->path = "{$this->getAdminUploadPath()}/forms";
		parent::__construct($options, $atts);
		
        // Reset configuration
        $this->resetConfig();
	}

	/**
	 * @inheritdoc
	 */
	public function setDefault(array $default = []) : self
	{
		parent::setDefault($this->translateDefault($default));
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setValues(array $values = []) : self
	{
		parent::setValues($values);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function generate(array $inputs = []) : string
	{
		$this->addInputs($this->translateInputs($inputs));
		return parent::generate();
	}

	/**
	 * Generate multiple forms.
	 *
	 * @access public
	 * @param array $forms
	 * @return array
	 */
	public function generateArray(array $forms = []) : array
	{
		$output = [];
		foreach ($forms as $name => $inputs) {
			if ( $this->isType('array', $inputs) 
			  && $this->isType('string', $name) ) {
				$output[$name] = $this->generate($inputs);
			}
		}
		return $output;
	}

	/**
	 * Generate sections.
	 *
	 * @access public
	 * @param array $sections
	 * @return array
	 */
	public function generateSections(array $sections = []) : array
	{
		$this->getTranslatorObject();
		$wrapper = [];
		foreach ($sections as $key => $section) {
			$wrapper[$key] = [
				'name'          => $this->translator->translate($section),
				'slug'          => $this->slugify($section),
				'class'         => 'nav-link ',
				'content-class' => 'tab-pane fade'
			];
			if ( $key == 0 ) {
				$wrapper[$key]['class'] .= ' active';
				$wrapper[$key]['content-class'] .= ' show active';
			}
		}
		return $wrapper;
	}
	
	/**
	 * Save multiple forms.
	 *
	 * @access public
	 * @param array $forms
	 * @param string $filename
	 * @param string $ext
	 * @return bool
	 */
	public function saveArray(array $forms = [], string $filename = '{name}{date}', string $ext = '.json') : bool
	{
		$filename = $this->replaceStringArray([
			'{name}' => 'data-',
			'{date}' => date('dmyhis')
		], $filename);

		$path = $this->formatPath("{$this->path}/{$filename}{$ext}");
		$schema = $this->formatJson($forms, 64|128|256);
		return $this->writeFile($path, $schema);
	}

	/**
	 * Load multiple forms.
	 *
	 * @access public
	 * @param string $filename
	 * @param string $ext
	 * @return array
	 */
	public function loadArray(string $filename, string $ext = '.json') : array
	{
		$path = $this->formatPath("{$this->path}/{$filename}{$ext}");
		$schema = $this->readFile($path);
		return (array)$this->decodeJson($schema, true);
	}

	/**
	 * Save form.
	 *
	 * @access public
	 * @param string $filename
	 * @return bool
	 */
	public function save(string $filename = '{name}{date}{ext}') : bool
	{
		$filename = $this->replaceStringArray([
			'{name}' => 'data-',
			'{date}' => date('dmyhis'),
			'{ext}'  => '.json'
		], $filename);

		$path = $this->formatPath("{$this->path}/{$filename}");
		return $this->writeFile($path, $this->export());
	}

	/**
	 * Export form.
	 *
	 * @access public
	 * @return string
	 */
	public function export() : string
	{
		$data = [
			'form' => [
				'attributes' => $this->getAtts(),
				'options'    => $this->getOptions(),
				'inputs'     => $this->getInputs()
			]
		];
		return $this->formatJson($data, 64|128|256);
	}

	/**
	 * Import form.
	 *
	 * @access public
	 * @param string $schema
	 * @return void
	 */
	public function import(string $schema)
	{
		$data = $this->decodeJson($schema, true);

		if ( $this->isType('array', $data) ) {
			// Import attributes
			$atts = $data['form']['attributes'] ?? [];
			foreach ((array)$atts as $key => $value) {
				$this->setAttribute($key, $value);
			}

			// Import options
			$options = $data['form']['options'] ?? [];
			foreach ((array)$options as $key => $value) {
				$this->setOptions($key, $value);
			}

			// Import inputs
			$inputs = $data['form']['inputs'] ?? [];
			$this->setInputs((array)$inputs);
		}
	}

	/**
	 * Load form file.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function load(string $path)
	{
		$data = $this->readFile($path);
		$this->import($data);
	}

	/**
	 * Translate inputs.
	 *
	 * @access private
	 * @param array $inputs
	 * @return array
	 */
	private function translateInputs(array $inputs = []) : array
	{
		$this->getTranslatorObject();
		foreach ($inputs as $key => $value) {

			if ( isset($value['type']) && $value['type'] == 'hidden' ) {
				continue;
			}

			// label
			if ( isset($value['label']) && !empty($value['label']) ) {
				$inputs[$key]['label'] = $this->translator->translate($value['label']);
			}

			// placeholder
			if ( isset($value['placeholder']) && !empty($value['placeholder']) ) {
				$inputs[$key]['placeholder'] = $this->translator->translate($value['placeholder']);
			}

			// description
			if ( isset($value['description']) && !empty($value['description']) ) {
				$inputs[$key]['description'] = $this->translator->translate($value['description']);
			}

			// string
			if ( isset($value['type']) && $value['type'] == 'string' ) {
				if ( isset($value['string']) && !empty($value['string']) ) {
					$inputs[$key]['string'] = $this->translator->translate($value['string']);
				}
			}

			// submit
			if ( isset($value['type']) && $value['type'] == 'submit' ) {
				if ( isset($value['value']) && !empty($value['value']) ) {
					$inputs[$key]['value'] = $this->translator->translate($value['value']);
				}
			}
		}
		
		return $inputs;
	}

	/**
	 * Translate default.
	 *
	 * @access private
	 * @param array $default
	 * @return array
	 */
	private function translateDefault(array $default = []) : array
	{
		$this->getTranslatorObject();
		foreach ($default as $input => $values) {
			if ( $this->isType('array', $values) ) {
				foreach ($values as $key => $value) {
					$default[$input][$key] = $this->translator->translate($value);
				}
			}
		}
		return $default;
	}
}

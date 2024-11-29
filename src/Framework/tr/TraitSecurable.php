<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.3.x
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Security\{Tokenizer, Password, Encryption};

trait TraitSecurable
{
	/**
	 * @access protected
	 * @var object $hash, Hash object
	 */
	protected $hash;

	/**
	 * Get hash object.
	 *
	 * @access protected
	 * @param mixed $data
	 * @param string $key
	 * @param string $vector
	 * @param int $length
	 * @return object
	 */
	protected function getHashObject($data, ?string $key = 'v6t1pQ97JS', ?string $vector = 'XRtvQPlFs', ?int $length = 16) : Encryption
	{
		$this->hash = new Encryption($data, $key, $vector, $length);
		return $this->hash;
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function generateToken(int $length = 32) : string
	{
		return Tokenizer::generate($length);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function generateHash($value) : string
	{
		return Tokenizer::hash($value);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function verifyHash(string $hash, $value) : bool
	{
		return Tokenizer::verify($hash, $value);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isPassword(string $password, string $hash) : bool
	{
		return Password::isValid($password, $hash);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isStrongPassword($password = '', int $length = 8) : bool
	{
		return Password::isStrong($password, $length);
	}
}

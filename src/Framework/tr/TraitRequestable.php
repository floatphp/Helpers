<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file is a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\tr;

use FloatPHP\Classes\Http\{Server, Request, Response, Post};

trait TraitRequestable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getRequest($key = null) : mixed
	{
		return Request::get($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasRequest($key = null) : bool
	{
		return Request::isSetted($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function setResponse(string $msg = '', array $content = [], string $status = 'success', int $code = 200) : void
	{
		Response::set($msg, $content, $status, $code);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getPost($key = null) : mixed
	{
		return Post::get($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getServer($key = null) : mixed
	{
		return Server::get($key);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getServerBaseUrl() : string
	{
		return Server::getBaseUrl();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getServerCurrentUrl($escape = false) : string
	{
		return Server::getCurrentUrl($escape);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getServerIp($domain = null) : mixed
	{
		return Server::getIp($domain);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isBasicAuth() : bool
	{
		return Server::isBasicAuth();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getBasicAuthUser() : string
	{
		return Server::getBasicAuthUser();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getBasicAuthPwd() : string
	{
		return Server::getBasicAuthPwd();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getBearerToken() : string
	{
		return Server::getBearerToken();
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function redirect(string $url = '/', int $code = 0) : void
	{
		Server::redirect($url, $code);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isSsl() : bool
	{
		return Server::isSsl();
	}
}

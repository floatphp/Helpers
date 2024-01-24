<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Framework Component
 * @version    : 1.1.1
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Framework\inc;

use FloatPHP\Classes\{
    Http\Server,
	Http\Request,
	Http\Response,
	Http\Post
};

trait TraitRequestable
{
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getRequest($key = null)
    {
        return Request::get($key);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function hasRequest($key = null)
    {
        return Request::isSetted($key);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function setResponse(string $msg = '', array $content = [], string $status = 'success', int $code = 200)
	{
		Response::set($msg, $content, $status, $code);
	}
	
	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getPost($key = null)
    {
        return Post::get($key);
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function getServer($key = null)
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
	protected function getServerIp($domain = null)
	{
		return Server::getIp($domain);
	}

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function isBasicAuth()
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
    protected function getBearerToken()
    {
        return Server::getBearerToken();
    }

	/**
	 * @access protected
	 * @inheritdoc
	 */
	protected function redirect(string $url = '/', int $code = 0)
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

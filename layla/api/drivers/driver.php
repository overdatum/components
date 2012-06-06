<?php
/**
 * Part of the API for Layla.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file licence.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getlayla.com so I can send you a copy immediately.
 *
 * @package    Layla Components
 * @version    1.0
 * @author     Koen Schmeets <koen@getlayla.com>
 * @license    MIT License
 * @link       http://getlayla.com
 */

namespace Layla\API\Drivers;

use Layla\API;

use Laravel\Config;

abstract class Driver {

	/**
	 * Get the API config
	 * 
	 * @return array
	 */
	protected static function config()
	{
		return Config::get('layla.'.API::$component.'.api');
	}

	/**
	 * Create the URL from segments
	 * 
	 * @param array $segments
	 * @param array $data
	 * 
	 * @return array divided in 3 items: BASE_URL, QUERY_SEGMENTS and QUERY_STRING
	 */
	protected static function url($segments, $data = array())
	{
		$config = static::config();

		return array($config['url'].'/', 'v'.$config['version'].'/'.implode('/', $segments), (count($data) > 0 ? '?' . http_build_query($data) : ''));
	}

	/**
	 * Create a GET request
	 * 
	 * @param array $segments
	 * @param array $data
	 * 
	 * @return Layla\API\Response
	 */
	public static function get($segments, $data = array())
	{
		return static::request('GET', $segments, $data);
	}

	/**
	 * Create a POST request
	 * 
	 * @param array $segments
	 * @param array $data
	 * 
	 * @return Layla\API\Response
	 */
	public static function post($segments, $data = array())
	{
		return static::request('POST', $segments, $data);
	}

	/**
	 * Create a PUT request
	 * 
	 * @param array $segments
	 * @param array $data
	 * 
	 * @return Layla\API\Response
	 */
	public static function put($segments, $data = array())
	{
		return static::request('PUT', $segments, $data);
	}

	/**
	 * Create a DELETE request
	 * 
	 * @param array $segments
	 * @param array $data
	 * 
	 * @return Layla\API\Response
	 */
	public static function delete($segments, $data = array())
	{
		return static::request('DELETE', $segments, $data);
	}

}
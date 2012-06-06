<?php
/**
 * Part of the API for Layla.
 *
 * LICENSE
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

use Exception;

use Layla\API\Response;

use Laravel\Input;
use Laravel\Request;
use Laravel\Routing\Route;

class Directly extends Driver {

	/**
	 * "Forward" the request
	 * 
	 * This driver is great for a single server install,
	 * and when debugging your application.
	 * 
	 * @param string 	$method 	GET, POST, PUT, DELETE, etc.
	 * @param array 	$segments 	for example array('account', 'all')
	 * @param array 	$data 		the post / put data
	 */
	public static function request($method, $segments, $data = array())
	{
		$method = strtoupper($method);
		if(in_array($method, array('GET', 'POST', 'PUT', 'DELETE')))
		{
 			Input::replace($data);
		}

		$config = static::config();
		$_SERVER['PHP_AUTH_USER'] = $config['username'];
		$_SERVER['PHP_AUTH_PW'] = $config['password'];

		list($url, $uri, $query_string) = static::url($segments, $data);
		$response = Route::forward($method, $uri);

		$code = $response->foundation->getStatusCode();
		$body = $response->content;

		return new Response($code, json_decode($body));
	}

}
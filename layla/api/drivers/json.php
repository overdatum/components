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

use Layla\API\Response;

use Httpful\Request;

use Laravel\Config;
use Exception;

/**
 * This class makes it easy to call methods on the Layla API (Returning JSON data) via HTTP
 */
class JSON extends Driver {

	/**
	 * Make a JSON request
	 * 
	 * @param string 	$method 	GET, POST, PUT, DELETE, etc.
	 * @param array 	$segments 	for example array('account', 'all')
	 * @param array 	$data 		the post / put data
	 */
	public static function request($method, $segments, $data = array())
	{
		$url = implode('', static::url($segments, $data));
		
		$config = static::config();
		
		$response = Request::init($method)
					->uri($url)->mime('json')
					->basicAuth($config['username'], $config['password'])
					->send();

		if($response->code === 404)
		{
			throw new Exception('API method "'.$url.'" does not exist.');
		}

		if($response->code === 401)
		{
			throw new Exception('Unauthorized to use this API method "'.$url.'".');
		}

		return new Response($response->code, $response->body);
	}

}
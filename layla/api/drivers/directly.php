<?php namespace Layla\API\Drivers;

use Exception;

use Layla\API\Response;

use Laravel\Input;
use Laravel\Request;
use Laravel\Routing\Route;

class Directly extends Driver {

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
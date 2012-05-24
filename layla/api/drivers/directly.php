<?php namespace Components\Layla\API\Drivers;

use Exception;

use Components\Layla\API\Response;

use Laravel\Input;
use Laravel\Request;
use Laravel\Routing\Route;

class Directly extends Driver {

	public static function request($method, $segments, $data = array())
	{
		//Request::foundation()->query->replace($segments);

		$method = strtoupper($method);
		if(in_array($method, array('GET', 'POST', 'PUT', 'DELETE')))
		{
 			Input::replace($data);
		}

		list($url, $uri, $query_string) = static::url($segments, $data);
		$response = Route::forward($method, $uri);

		$code = $response->foundation->getStatusCode();
		$body = $response->content;
		
		return new Response($code, $body);
	}

}
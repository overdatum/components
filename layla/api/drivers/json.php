<?php namespace Components\Layla\API\Drivers;

use Components\Layla\API\Response;

use Httpful\Request;

use Laravel\Config;
use Exception;

class JSON extends Driver {

	public static function request($method, $segments, $data)
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
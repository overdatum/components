<?php namespace Components\Layla\API\Drivers;

use Components\Layla\API\Response;

use Laravel\Config;
use Exception;

class JSON extends Driver {

	public static function request($method, $segments, $data)
	{
		$url = implode('', static::url($segments, $data));
		
		$config = static::config();
		
		$headers = array(
			'accept: application/json',
			'content-type: application/json',
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD, $config['username'].':'.$config['password']);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		
		if($method !== 'GET' && $method !== 'DELETE')
		{
			$data = json_encode($data);
 			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		$body = curl_exec($ch);
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return new Response($response_code, $body);
	}

}
<?php namespace Layla\API\Drivers;

use Layla\API;

use Laravel\Config;

abstract class Driver {

	protected static function config()
	{
		return Config::get('layla.'.API::$component.'.api');
	}

	protected static function url($segments, $data = array())
	{
		$config = static::config();

		return array($config['url'].'/', 'v'.$config['version'].'/'.implode('/', $segments), (count($data) > 0 ? '?' . http_build_query($data) : ''));
	}

	public static function get($segments, $data = array())
	{
		return static::request('GET', $segments, $data);
	}

	public static function post($segments, $data = array())
	{
		return static::request('POST', $segments, $data);
	}

	public static function put($segments, $data = array())
	{
		return static::request('PUT', $segments, $data);
	}

	public static function delete($segments, $data = array())
	{
		return static::request('DELETE', $segments, $data);
	}

}
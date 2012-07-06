<?php namespace Layla;

use Laravel\Config;
use Laravel\Str;
use Laravel\Routing\Router;
use Laravel\Routing\Route as Laravel_Route;

class Route extends Laravel_Route {

	/**
	 * All of the API route types
	 * 
	 * @var array (HTTP Method, plural, has identity)
	 */
	public static $types = array(
		'read_multiple' => array(
			'GET',
			true,
			false,
			''
		),
		'create' => array(
			'POST',
			false,
			false,
			'add/(:any?)/(:any?)'
		),
		'read' => array(
			'GET',
			false,
			true,
			'detail/(:any?)'
		),
		'update' => array(
			'PUT',
			false,
			true,
			'edit/(:any?)'
		),
		'translate' => array(
			'PUT',
			false,
			true,
			''
		),
		'delete' => array(
			'DELETE',
			false,
			true,
			'delete/(:any?)'
		)
	);

	/**
	 * Register API controllers
	 * 
	 * @param array		$controllers	the controllers and config
	 * @param string	$bundle			the bundle where the controller can be found
	 * @param string	$url_prefix		a global url prefix
	 * @param array		$parents
	 */
	public static function api($controllers, $bundle, $url_prefix, $parents = array())
	{
		static::opinionated($controllers, $bundle, $url_prefix, 'api', $parents);
	}
	
	/**
	 * Register page controllers
	 * 
	 * @param array		$controllers	the controllers and config
	 * @param string	$bundle			the bundle where the controller can be found
	 * @param string	$url_prefix		a global url prefix
	 * @param array		$parents
	 */
	public static function pages($controllers, $bundle, $url_prefix, $parents = array())
	{
		static::opinionated($controllers, $bundle, $url_prefix, 'pages', $parents);
	}

	/**
	 * Register opinionated controllers
	 * 
	 * @param array		$controllers	the controllers and config
	 * @param string	$bundle			the bundle where the controller can be found
	 * @param string	$url_prefix		a global url prefix
	 * @param string	$route_type		the type of the route (pages or api)
	 * @param array		$parents
	 */
	public static function opinionated($controllers, $bundle, $url_prefix, $route_type, $parents = array())
	{
		if( ! ends_with($url_prefix, '/'))
		{
			$url_prefix .= '/';
		}

		foreach ($controllers as $controller => $options)
		{
			list($types, $children) = $options;

			foreach ($types as $type)
			{
				list($method, $plural, $has_identity, $postfix) = static::$types[$type];

				$segment = $controller;

				$action = ($bundle ? $bundle.'::' : '').implode('.', $parents).(count($parents) > 0 ? '.' : '').$segment.'@'.$type;

				if($plural)
				{
					$segment = Str::plural($segment);
				}

				$prefixes = array_map(function($parent)
				{
					return $parent ? $parent.'/(:any)/' : '(:any)/';
				}, $parents);

				$prefix = implode('', $prefixes);

				$route = $url_prefix.$prefix.$segment.($has_identity ? '/(:any)' : '').($route_type == 'pages' && $postfix ? '/'.$postfix : '');

				if($route_type == 'pages')
				{
					$method = '*';
				}

				Router::register($method, $route, $action);
			}

			$parents[] = $controller;

			if(is_array($children))
			{
				static::opinionated($children, $bundle, $url_prefix, $route_type, $parents);
			}

			$parents = array();
		}
	}

}

<?php namespace Layla;

use Laravel\Config;
use Laravel\Str;
use Laravel\Routing\Route as Laravel_Route;

class Route extends Laravel_Route {

	/**
	 * All of the API route types
	 * 
	 * @var array (HTTP Method, plural, has identity)
	 */
	public static $types = array(
		'list' => array(
			true,
			false,
			''
		),
		'create' => array(
			false,
			false,
			'add'
		),
		'read' => array(
			false,
			true,
			'detail'
		),
		'update' => array(
			false,
			true,
			'edit'
		),
		'delete' => array(
			false,
			true,
			'delete'
		)
	);

	/**
	 * Register API controllers
	 * 
	 * @param array $controllers 
	 * @param array $parents
	 */
	public static function opinionated($controllers, $bundle, $url_prefix, $enable_postfix = false, $parents = array())
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
				list($plural, $has_identity, $postfix) = static::$types[$type];

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

				$route = $url_prefix.$prefix.$segment.($has_identity ? '/(:any)' : '').($enable_postfix && ! is_null($postfix) ? '/'.$postfix : '');

				static::any($route, $action);
			}

			$parents[] = $controller;

			if(is_array($children))
			{
				static::opinionated($children, $bundle, $url_prefix, $enable_postfix, $parents);
			}

			$parents = array();
		}
	}

}

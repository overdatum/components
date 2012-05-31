<?php namespace Layla;

use Closure;
use Exception;

use Laravel\Str;
use Laravel\Config;
use Laravel\Bundle;

use Layla\Module\Renderers\Table;
use Layla\Module\Renderers\Bootstrap;
use Layla\Module\Catcher;

class Module {

	/**
	 * The currently active module renderers.
	 *
	 * @var array
	 */
	public static $drivers = array();

	/**
	 * All the modules that have been registered
	 * 
	 * @var array
	 */
	public static $modules = array(
		'page' => array(),
		'form' => array()
	);

	/**
	 * The third-party module renderer registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * Get a module renderer driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver)) $driver = Config::get('admin::renderer.driver');

		if ( ! isset(static::$drivers[$driver]))
		{
			static::$drivers[$driver] = static::factory($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Create a new module renderer driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	protected static function factory($driver)
	{
		if (isset(static::$registrar[$driver]))
		{
			$resolver = static::$registrar[$driver];

			return $resolver();
		}

		switch ($driver)
		{
			case 'bootstrap':
				return new Bootstrap;
			case 'table':
				return new Table;

			default:
				throw new Exception("Module renderer driver {$driver} is not supported.");
		}
	}

	/**
	 * Register a third-party module renderer driver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}

	public static function page($name)
	{
		$arguments = func_get_args();
		$arguments = array_slice($arguments, 1);
		return static::load('page', $name, $arguments);
	}

	public static function form($name)
	{
		$arguments = func_get_args();
		$arguments = array_slice($arguments, 1);
		return static::load('form', $name, $arguments);
	}

	protected static function load($type, $name, $arguments = array())
	{
		if(array_key_exists($name, static::$modules[$type]))
		{
			if(static::$modules[$type][$name] instanceof Closure)
			{
				return static::render(static::$modules[$type][$name]);
			}
			else
			{
				list($file, $class_name, $method) = static::parse($name, $type);
				require_once $file;
				return static::render(function($catcher) use ($class_name, $method, $arguments)
				{
					array_unshift($arguments, $catcher);
					$class = new $class_name;
					return call_user_func_array(array($class, $method), $arguments);
				});
			}
		}
	}

	protected static function parse($name, $type)
	{
		list($path, $method) = explode('@', static::$modules[$type][$name]);
		list($bundle, $path) = Bundle::parse($path);

		$file = Bundle::path($bundle).$type.'s'.DS.str_replace('.', DS, $path).EXT;
		$class_name = static::format($bundle, $path, $type);

		return array($file, $class_name, $method);
	}

	/**
	 * Format a bundle and controller identifier into the Form's or Page's class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $controller
	 * @return string
	 */
	protected static function format($bundle, $path, $type)
	{
		return Bundle::class_prefix($bundle).Str::classify($path).'_'.ucfirst($type);
	}

	public static function register($type, $name, $action = null)
	{
		static::$modules[$type][$name] = $action;
	}

	/**
	 * The method for rendering the fields
	 */
	public static function render($callback, $driver = null)
	{
		$callback($catched = new Catcher);
		return static::driver($driver)->render($catched->calls);
	}

}
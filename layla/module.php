<?php namespace Layla;

use Exception;
use Laravel\Config;
use Layla\Module\Renderers\Bootstrap;
use Layla\Module\Fields;

class Module {

	/**
	 * The currently active module renderers.
	 *
	 * @var array
	 */
	public static $drivers = array();

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

	/**
	 * The method for rendering the fields
	 */
	public static function render($callback)
	{
		$callback($fields = new Fields($callback));
		return static::driver()->render($fields->fields);
	}

}
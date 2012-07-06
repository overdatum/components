<?php
/**
 * Artifact - A View abstraction taken from Layla.
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

namespace Layla;

use Layla\Artifact\Renderers\Tabs;
use Layla\Artifact\Renderers\Table;
use Layla\Artifact\Renderers\Bootstrap;
use Layla\Artifact\Renderers\Layla;
use Layla\Artifact\Catcher;

use Closure;
use Exception;

use Laravel\Str;
use Laravel\Config;
use Laravel\Bundle;

/**
 * This class can turn an array or a callback defining a view into HTML and has
 * Driver support. (Bootstrap, Table and Tabs drivers are included)
 */
class Artifact {

	/**
	 * The currently active artifact renderers.
	 *
	 * @var array
	 */
	public static $drivers = array();

	/**
	 * All the artifacts that have been registered
	 * 
	 * @var array
	 */
	public static $artifacts = array();

	/**
	 * The third-party artifact renderer registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * The artifact's type
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * The artifact's name
	 * 
	 * @var string
	 */
	public $name;

	/**
	 * The artifact's data.
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * All of the shared view data.
	 *
	 * @var array
	 */
	public static $shared = array();

	/**
	 * Create a new Artifact instance
	 */
	public function __construct($type, $name, $data = array())
	{
		$this->type = $type;
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * Add a key / value pair to the artifact data.
	 *
	 * Bound data will be available to the view as variables.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return View
	 */
	public function with($key, $value = null)
	{
		if (is_array($key))
		{
			$this->data = array_merge($this->data, $key);
		}
		else
		{
			$this->data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Add a key / value pair to the shared view data.
	 *
	 * Shared view data is accessible to every view created by the application.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function share($key, $value)
	{
		static::$shared[$key] = $value;

		return $this;
	}

	/**
	 * Get the array of artifact data for the artifact instance.
	 *
	 * The shared view data will be combined with the view data.
	 *
	 * @return array
	 */
	public function data()
	{
		return array_merge($this->data, static::$shared);
	}

	/**
	 * Retrieve a page or form
	 * 
	 * @param 	string 	$type 		whether we are loading a page or a form
	 * @param 	string 	$name 		the page identifier
	 * @param 	array 	$data 		artifact data
	 * 
	 * @return 	string 	the HTML
	 */
	protected static function load($type, $name, $data = array())
	{
		return new static($type, $name, $data);
	}

	public function get()
	{
		if( ! array_key_exists($this->name, static::$artifacts[$this->type]))
		{
			throw new Exception("The {$type} you are trying to retrieve does not exist.");
		}

		if(static::$artifacts[$this->type][$this->name] instanceof Closure)
		{
			return static::render(static::$artifacts[$this->type][$this->name]);
		}
		
		list($file, $class_name, $method) = static::parse($this->name, $this->type);

		require_once $file;

		$data = $this->data();

		return static::render(function($catcher) use ($class_name, $method, $data)
		{
			$artifact = new $class_name;
			return $artifact->$method($catcher, $data);
		});
	}

	/**
	 * Get a artifact renderer driver instance.
	 *
	 * @param  string  $driver
	 * 
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
	 * Create a new artifact renderer driver instance.
	 *
	 * @param  string  $driver
	 * 
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
			case 'tabs':
				return new Tabs;
			case 'layla':
				return new Layla;

			default:
				throw new Exception("Artifact renderer driver \"{$driver}\" is not supported.");
		}
	}

	/**
	 * Register a third-party artifact renderer driver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * 
	 * @return void
	 */
	public static function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}

	/**
	 * Parse the path to the form or page
	 * 
	 * @param string $name
	 * @param string $type
	 * 
	 * @return array($file,$class_name,$method)
	 */
	protected static function parse($name, $type)
	{
		list($path, $method) = explode('@', static::$artifacts[$type][$name]);
		list($bundle, $path) = Bundle::parse($path);

		$file = Bundle::path($bundle).Str::plural($type).DS.str_replace('.', DS, $path).EXT;
		$class_name = static::format($bundle, $path, $type);

		return array($file, $class_name, $method);
	}

	/**
	 * Format a bundle and controller identifier into the Form's or Page's class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $controller
	 * 
	 * @return string
	 */
	protected static function format($bundle, $path, $type)
	{
		return Bundle::class_prefix($bundle).Str::classify($path).'_'.ucfirst($type);
	}

	/**
	 * Register the route to a form or page
	 * 
	 * @param 	string 	$type 		whether we are registering a page or a form
	 * @param 	string 	$name 		the identifier
	 * @param 	string 	$action 	path to the class and method
	 * 
	 * @return 	void
	 */
	public static function register($type, $name, $action = null)
	{
		static::$artifacts[$type][$name] = $action;
	}

	/**
	 * The method for rendering the fields
	 * 
	 * @param Closure 	$callback 	The Closure containing the calls
	 * 
	 * @return string 	the generated HTML
	 */
	public static function render($callback)
	{
		return static::driver()->render($callback);
	}

	/**
	 * Get the evaluated string content of the Artifact.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->get();
	}

	/**
	 * The method for loading a registered artifact
	 * 
	 * @param	string	$method The method being called, identifying the artifact's type
	 * @param	array	$parameters The parameters on the method
	 */
	public static function __callStatic($method, $parameters)
	{
		$name = array_shift($parameters);

		return static::load($method, $name, array_shift($parameters));
	}

}
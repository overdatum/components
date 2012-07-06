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

namespace Layla\Artifact\Renderers;

use Layla\Artifact\Catcher;
use Layla\Notification;

use Closure;

use Laravel\Session;
use Laravel\Messages;

/**
 * This is the base class for all other Renderer classes and provides functionality that is shared among them
 */
class Renderer {

	/**
	 * The post-validation error messages.
	 *
	 * @var Messages
	 */
	public $errors;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->errors = Session::has('errors') ? Session::get('errors') : new Messages(); 
	}

	public function notifications()
	{
		return Notification::show();
	}

	/**
	 * Method for rendering the calls in a callback function
	 * 
	 * @param $callback	Closure
	 * @return $html	string
	 */
	public function render($callback)
	{
		$callback($catched = new Catcher);

		$html = '';
		foreach ($catched->calls as $field)
		{
			foreach ($field as $type => $options)
			{
				$html .= call_user_func_array(array($this, $type), $options);
			}
		}

		return $html;
	}

	/**
	 * Method for applying the calls in a callback function to the driver
	 * 
	 * @param $callback	Closure
	 * @return void
	 */
	public function apply($callback)
	{
		$callback($catched = new Catcher);
		foreach ($catched->calls as $field)
		{
			foreach ($field as $type => $options)
			{
				call_user_func_array(array($this, $type), $options);
			}
		}		
	}

	/**
	 * Method for adding any kind of child
	 * 
	 * @param mixed $children
	 * @return string
	 */
	public function add($children)
	{
		if(is_string($children))
		{
			return $children;
		}

		$output = '';
		foreach ((array) $children as $child)
		{
			if($child instanceof Closure)
			{
				$output .= Artifact::render($child);
			}
			else {
				$output .= "\n".$child;
			}
		}

		return $output;
	}

	/**
	 * Method for adding raw text to the output
	 * 
	 * @param contents $string
	 * @return string
	 */
	public function raw($contents)
	{
		return $contents;
	}

}
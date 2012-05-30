<?php namespace Layla\Module\Renderers;

use Closure;

use Laravel\Session;
use Laravel\Messages;

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

	/**
	 * Method for rendering an array of fields
	 * 
	 * @param $fields array
	 * @return $html string
	 */
	public function render($fields)
	{
		$html = '';
		foreach ($fields as $field)
		{
			foreach ($field as $type => $options)
			{
				$html .= call_user_func_array(array($this, $type), $options);
			}
		}

		return $html;
	}

	/**
	 * Method for adding any kind of child
	 * 
	 * @param mixed $children
	 * @return string
	 */
	public function add($children)
	{
		$output = '';
		foreach ((array) $children as $child)
		{
			if($child instanceof Closure)
			{
				$output .= Module::render($child);
			}
			else {
				$output .= "\n".$child;
			}
		}

		return $output;
	}

	/**
	 * Method for adding the return value of the callback to the output
	 * 
	 * @param mixed $callback
	 * @return string
	 */
	public function raw($callback)
	{
		return $callback();
	}

}
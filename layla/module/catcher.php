<?php namespace Layla\Module;

/**
 * This class will catch all methods (and their arguments) that are called on it
 */
class Catcher {

	/**
	 * All of the methods that are called on this class including their arguments
	 * 
	 * @var array
	 */
	public $calls = array();

	/**
	 * Collect the method calls.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return void
	 */
	public function __call($method, $arguments)
	{
		$this->calls[] = array($method => $arguments);
	}

}
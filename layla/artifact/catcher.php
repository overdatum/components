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

namespace Layla\Artifact;

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
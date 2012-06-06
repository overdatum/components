<?php
/**
 * Part of the API for Layla.
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

namespace Layla\API;

use ArrayObject;

class Response {

	/**
	 * @var integer
	 */
	public $code;

	/**
	 * @var string
	 */
	public $body;

	/**
	 * @var boolean
	 */
	public $success;

	/**
	 * Create a new Response instance.
	 *
	 * @param  integer  $code
	 * @return string 	$body
	 */
	public function __construct($code, $body)
	{
		$this->code = $code;
		$this->body = $body;
		$this->success = $code === 200;
	}

	/**
	 * Get (an item of) the response
	 */
	public function get($key = null, $default = null)
	{
		return is_null($key) ? $this->body : array_get((array) $this->body, $key, $default);
	}

}
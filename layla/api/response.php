<?php namespace Components\Layla\API;

use ArrayObject;

class Response {

	public $code;

	public $body;

	public $success;

	public function __construct($code, $body)
	{
		$this->code = $code;
		$this->body = $body;
		$this->success = $code === 200;
	}

	public function get($key = null, $default = null)
	{
		return is_null($key) ? $this->body : array_get((array) $this->body, $key, $default);
	}

}
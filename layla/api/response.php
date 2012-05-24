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

	protected function json_to_array($json)
	{
		return json_decode($json);	
	}

	public function get($key = null, $default = null)
	{
		$data = $this->json_to_array($this->body);
		return is_null($key) ? $data : array_get((array) $data, $key, $default);
	}

}
<?php Components\Layla;

class Form {

	public $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function make($config)
	{
		return new static($config);
	}

}
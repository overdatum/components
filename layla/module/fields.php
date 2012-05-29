<?php namespace Layla\Module;

class Fields {

	public $fields = array();

	public function nest($method, $callback)
	{
		$callback($children = new Fields);
		$field = array('nest' => array($method, $children->fields));
		$this->fields[] = $field;
	}

	public function __call($field, $arguments)
	{

		$this->fields[] = array($field => $arguments);
	}

}